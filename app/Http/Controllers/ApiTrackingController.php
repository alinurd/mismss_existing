<?php

namespace App\Http\Controllers;

use App\Models\TrackSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Read-only JSON tracking for API consumers (GET /api/tracking).
 * Standalone: does not touch ShiptripController / TrackSystem::loadTracking (which build HTML),
 * it only calls their read-only helpers getWayBills() and checkResiTemporary().
 */
class ApiTrackingController extends Controller
{
    public function show(Request $request)
    {
        $id = trim((string) $request->input('id'));
        if ($id === '') {
            return response()->json(['status' => 422, 'message' => 'Parameter id is required'], 422);
        }

        try {
            $track = new TrackSystem;
            $data = $track->getWayBills($id);
            if ($data['wayBill'] === '') {
                return response()->json(['status' => 404, 'message' => 'Tracking ID not found', 'id' => $id], 404);
            }

            $msTrackId = $data['wayBill'][0]['id'];
            $waybills = array_values(array_unique(array_map(fn ($w) => $w['id'], $data['wayBill'])));

            $shipment = $this->shipment($msTrackId);
            $timeline = $this->timeline($msTrackId);

            // Every waybill has its own shipment + timeline; the first one is already loaded above.
            $details = [];
            foreach ($waybills as $wb) {
                if ($wb === $msTrackId) {
                    $details[] = ['id' => $wb, 'temporary' => TrackSystem::checkResiTemporary($wb) !== '', 'shipment' => $shipment, 'timeline' => $timeline];
                    continue;
                }

                // one broken secondary waybill must not fail the whole response
                try {
                    $details[] = [
                        'id' => $wb,
                        'temporary' => TrackSystem::checkResiTemporary($wb) !== '',
                        'shipment' => $this->shipment($wb),
                        'timeline' => $this->timeline($wb),
                    ];
                } catch (\Throwable $e) {
                    Log::warning('API tracking: waybill '.$wb.' skipped: '.$e->getMessage());
                    $details[] = ['id' => $wb, 'temporary' => false, 'shipment' => null, 'timeline' => []];
                }
            }

            return response()->json([
                'status' => 200,
                'id' => $msTrackId,
                'temporary' => $details[0]['temporary'],
                'waybills' => $waybills,
                'shipment' => $shipment,
                'timeline' => $timeline,
                'waybill_details' => $details,
            ]);
        } catch (\Throwable $e) {
            Log::error('API tracking failed for '.$id.': '.$e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Failed to load tracking', 'id' => $id], 500);
        }
    }

    private function shipment($msTrackId)
    {
        $shipment = [
            'shipment_date' => null,
            'warehouse' => ['id' => null, 'location' => null],
            'service' => null,
            'weight_kg' => null,
            'item' => null,
            'cbm' => null,
            'invoice_number' => null,
            'foreign_tracks' => DB::table('shiptrip_foreign_track_list')->where('ms_track_id', $msTrackId)->pluck('id')->all(),
            'forwarder' => null,
            'attachments' => $this->attachments($msTrackId),
            'customer' => ['type' => null, 'name' => null, 'phone' => null, 'address' => null],
        ];

        $dl = DB::table('data_list')
            ->select('data_list.*', 'service_list.name as servname', 'warehouse_list.location as wareloc',
                'order_list.to_sg_man_created_at as order_shipped_at', 'cust_type_list.name as custtypename')
            ->join('order_list', 'order_list.id', '=', 'data_list.mismass_order_id')
            ->join('cust_type_list', 'cust_type_list.id', '=', 'data_list.cust_type_id')
            ->join('service_list', 'service_list.id', '=', 'data_list.service_id')
            ->join('warehouse_list', 'warehouse_list.id', '=', 'data_list.warehouse_id');

        $isShipNum = (clone $dl)->where('data_list.shipping_number', $msTrackId)->first();
        $invoiceId = DB::table('order_list')->where('ms_track_id', $msTrackId)->value('invoice_id');

        if ($isShipNum || $invoiceId) {
            $base = $isShipNum
                ? (clone $dl)->where('data_list.shipping_number', $msTrackId)
                : (clone $dl)->where('data_list.mismass_invoice_id', $invoiceId);
            $row = (clone $base)->first();
            $sum = (clone $base)->selectRaw('SUM(data_list.weight) AS kg, SUM(data_list.item) AS item, SUM(data_list.cbm) AS cbm')->first();

            $isInd = $row->cust_type_id === 'IND';
            $prefix = ($isShipNum || $isInd) ? 'cons' : 'sender';
            $shipDate = $isShipNum
                ? $row->order_shipped_at
                : DB::table('shiptrip_list')->where('ms_track_id', $msTrackId)->value('to_sg_man_created_at');

            $shipment['shipment_date'] = $this->date($shipDate);
            $shipment['warehouse'] = ['id' => $row->warehouse_id, 'location' => $row->wareloc];
            $shipment['service'] = $row->servname;
            $shipment['weight_kg'] = ['actual' => round($sum->kg, 2), 'rounded' => $this->pembulatan(round($sum->kg, 2))];
            $shipment['item'] = (int) $sum->item;
            $shipment['cbm'] = round($sum->cbm, 2);
            $shipment['invoice_number'] = $isShipNum ? null : $row->mismass_invoice_id;
            $shipment['customer'] = [
                'type' => $row->custtypename,
                'name' => trim(implode(' ', array_filter([$row->{$prefix.'_first_name'}, $row->{$prefix.'_middle_name'}, $row->{$prefix.'_last_name'}]))),
                'phone' => $row->{$prefix.'_phone'},
                'address' => implode(', ', array_filter([$row->{$prefix.'_address'}, $row->{$prefix.'_sub_district'}, $row->{$prefix.'_district'},
                    $row->{$prefix.'_city'}, $row->{$prefix.'_prov'}, $row->{$prefix.'_postal_code'}])),
            ];
        } else {
            $st = DB::table('shiptrip_list')
                ->select('shiptrip_list.to_sg_man_created_at', 'shiptrip_list.track_status_id', 'service_list.name as servname',
                    'service_list.warehouse_id as wareid', 'warehouse_list.location as wareloc', 'cust_list.*', 'cust_type_list.name as custtypename')
                ->join('cust_list', 'cust_list.id', '=', 'shiptrip_list.cust_id')
                ->join('cust_type_list', 'cust_type_list.id', '=', 'cust_list.cust_type_id')
                ->join('service_list', 'service_list.id', '=', 'shiptrip_list.service_id')
                ->join('warehouse_list', 'warehouse_list.id', '=', 'service_list.warehouse_id')
                ->where('shiptrip_list.ms_track_id', $msTrackId)
                ->first();

            if ($st) {
                $shipment['shipment_date'] = $st->track_status_id == 1 ? null : $this->date($st->to_sg_man_created_at);
                $shipment['warehouse'] = ['id' => $st->wareid, 'location' => $st->wareloc];
                $shipment['service'] = $st->cust_type_id === 'IND' ? $st->servname : null;
                $shipment['customer'] = [
                    'type' => $st->custtypename,
                    'name' => trim(implode(' ', array_filter([$st->first_name, $st->middle_name, $st->last_name]))),
                    'phone' => $st->phone,
                    'address' => implode(', ', array_filter([$st->address, $st->sub_district, $st->district, $st->city, $st->prov, $st->postal_code])),
                ];
            }
        }

        $sn = DB::table('data_list')->select('shipping_number', 'forwarder_id', 'forwarder_name')->where('ms_track_id', $msTrackId)->first();
        if ($sn && $sn->forwarder_id == 'VENDOR') {
            $shipment['forwarder'] = ['name' => $sn->forwarder_name, 'shipping_number' => $sn->shipping_number];
        }

        return $shipment;
    }

    private function timeline($msTrackId)
    {
        $rows = DB::table('shiptrip_track_list')
            ->select('shiptrip_track_list.*', 'shiptrip_track_status.title as tracktitle', 'shiptrip_track_status_manual.title as trackmantitle')
            ->join('shiptrip_track_status', 'shiptrip_track_list.track_status_id', 'shiptrip_track_status.id')
            ->join('shiptrip_track_status_manual', 'shiptrip_track_list.track_status_manual_id', 'shiptrip_track_status_manual.id')
            ->where('ms_track_id', $msTrackId)
            ->orderBy('created_at', 'desc')
            ->get();

        $timeline = [];
        foreach ($rows as $i => $r) {
            $time = date('H:i', strtotime($r->created_at));
            $text = str_replace('<br>', ' ', TrackSystem::checkTimelineText($r->text, $msTrackId, 1));
            $timeline[] = [
                'datetime' => date('c', strtotime($r->created_at)),
                'date' => date('Y-m-d', strtotime($r->created_at)),
                'time' => $time === '00:00' ? null : $time,
                'status' => $r->track_status_manual_id != 'A' ? $r->trackmantitle : $r->tracktitle,
                'description' => trim(html_entity_decode(strip_tags($text))),
                'attachments' => $this->linksFromHtml($text),
                'active' => $i === 0,
            ];
        }

        return $timeline;
    }

    /**
     * Package photos and proof-of-delivery (POD) files of one waybill, as absolute URLs.
     * Sources: shiptrip_image_list (photos), shiptrip_pod_image_list + data_list.shipping_success_pod (POD).
     */
    private function attachments($msTrackId)
    {
        $items = [];
        $add = function ($type, $label, $url) use (&$items) {
            if ($url && !isset($items[$url])) {
                $items[$url] = ['type' => $type, 'label' => $label, 'url' => $url];
            }
        };

        foreach (DB::table('shiptrip_image_list')->where('ms_track_id', $msTrackId)->get() as $i => $img) {
            $add('photo', 'Foto '.($i + 1), url('/assets/photos/'.$img->id.'.'.$img->ext));
        }

        $dl = DB::table('data_list')->select('shipping_number', 'shipping_success_pod')
            ->where(fn ($q) => $q->where('ms_track_id', $msTrackId)->orWhere('shipping_number', $msTrackId))
            ->get();

        $podKeys = array_values(array_unique(array_filter(array_merge([$msTrackId], $dl->pluck('shipping_number')->all()))));
        foreach (DB::table('shiptrip_pod_image_list')->whereIn('shipping_number', $podKeys)->get() as $pod) {
            $add('pod', 'Bukti penerimaan', url('/assets/pod/'.$pod->id.'.'.$pod->ext));
        }
        foreach ($dl->pluck('shipping_success_pod')->filter()->unique() as $file) {
            // webhook carriers (JNE, Sentral Cargo) store a full URL, drivers store a file name
            $add('pod', 'Bukti penerimaan', preg_match('#^https?://#i', $file) ? $file : url('/assets/pod/'.$file));
        }

        return array_values($items);
    }

    /** <a href="...">label</a> inside a timeline text -> [{label, url}] */
    private function linksFromHtml($html)
    {
        preg_match_all('/<a\b[^>]*\bhref=[\'"]([^\'"]+)[\'"][^>]*>(.*?)<\/a>/is', (string) $html, $m, PREG_SET_ORDER);

        return array_map(fn ($x) => [
            'label' => trim(html_entity_decode(strip_tags($x[2]))),
            'url' => html_entity_decode($x[1]),
        ], $m);
    }

    private function date($value)
    {
        return ($value && $value !== '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($value)) : null;
    }
}
