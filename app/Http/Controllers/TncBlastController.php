<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TncBlastController extends Controller
{
    const BATCH_SIZE = 20;

    public function index()
    {
        $this->roleAccess();

        $setting = DB::table('tnc_setting')->where('id', 1)->first();
        $data['currentVersion'] = $setting->version;
        $data['pendingCount'] = $this->pendingQuery($setting->version)->count();
        $data['totalCustomer'] = Customer::count();

        return view('pages.tncblast', $data);
    }

    public function table(Request $request)
    {
        $this->roleAccess();

        $currentVersion = DB::table('tnc_setting')->where('id', 1)->value('version');
        $search = $request->input('search')['value'] ?? '';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $base = $this->pendingQuery($currentVersion);
        $recordsTotal = (clone $base)->count();

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('middle_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $recordsFiltered = (clone $base)->count();

        $rows = $base->orderBy('cust_list.id')
            ->skip($start)
            ->when($length > 0, fn ($q) => $q->take($length))
            ->get();

        $data = [];
        $no = $start;
        foreach ($rows as $row) {
            $no++;
            $label = $row->cust_type_id == 'IND' ? 'Customer' : 'Client';
            $lastSent = $row->tnc_version_sent
                ? "<div>v{$row->tnc_version_sent}</div><div>" . $this->dateFormatIndo($row->tnc_sent_at, 2) . '</div>'
                : '<div style="color:red">Belum pernah dikirim</div>';

            $data[] = [
                "<input type='checkbox' class='tncCheckbox' value='{$row->id}'>",
                $no,
                "<div style='font-weight:700'>{$row->first_name} {$row->middle_name} {$row->last_name}</div><div>{$label} - ID{$row->id}</div>",
                "<div>{$row->email}</div>",
                $lastSent,
            ];
        }

        return json_encode([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function updateVersion(Request $request)
    {
        $this->roleAccess();

        if (!Auth::user()->tncblast_send) {
            return json_encode(['status' => 403, 'title' => 'Tidak Diizinkan', 'text' => 'Anda tidak memiliki akses untuk mengubah versi TnC']);
        }

        $version = trim((string) $request->input('version'));

        if ($version === '') {
            return json_encode(['status' => 422, 'title' => 'Gagal', 'text' => 'Versi tidak boleh kosong']);
        }

        DB::table('tnc_setting')->where('id', 1)->update([
            'version' => $version,
            'updated_by' => Auth::user()->username ?? Auth::user()->fullname,
            'updated_at' => now(),
        ]);

        return json_encode(['status' => 200, 'title' => 'Berhasil', 'text' => "Versi TnC aktif sekarang: $version", 'version' => $version]);
    }

    public function send(Request $request)
    {
        $this->roleAccess();

        if (!Auth::user()->tncblast_send) {
            return json_encode(['status' => 403, 'title' => 'Tidak Diizinkan', 'text' => 'Anda tidak memiliki akses untuk mengirim blast TnC']);
        }

        if (!env('TNC_ENABLED', false)) {
            return json_encode(['status' => 422, 'title' => 'Gagal', 'text' => 'Fitur TNC_ENABLED belum aktif di server ini']);
        }

        $currentVersion = DB::table('tnc_setting')->where('id', 1)->value('version');
        $ids = $request->input('ids', []);

        $query = $this->pendingQuery($currentVersion);
        if (!empty($ids)) {
            $query->whereIn('cust_list.id', $ids);
        }

        $batch = $query->orderBy('cust_list.id')->take(self::BATCH_SIZE)->get();

        $sent = 0;
        $failed = 0;
        foreach ($batch as $customer) {
            if (!filter_var($customer->email, FILTER_VALIDATE_EMAIL)) {
                $failed++;
                continue;
            }

            try {
                $emailData = [
                    'subject' => 'Pembaruan Syarat & Ketentuan Mismass Logistic',
                    'name' => trim($customer->first_name . ' ' . $customer->middle_name . ' ' . $customer->last_name),
                    'email' => $customer->email,
                    'modes' => 'TNC',
                ];

                Mail::to($customer->email)->send(new SendMail($emailData));

                Customer::where('id', $customer->id)->update([
                    'tnc_version_sent' => $currentVersion,
                    'tnc_sent_at' => now(),
                ]);

                $sent++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        $remaining = $this->pendingQuery($currentVersion)->count();

        return json_encode([
            'status' => 200,
            'sent' => $sent,
            'failed' => $failed,
            'remaining' => $remaining,
            'version' => $currentVersion,
        ]);
    }

    private function pendingQuery(string $currentVersion)
    {
        return Customer::query()
            ->where(function ($q) use ($currentVersion) {
                $q->whereNull('tnc_version_sent')
                    ->orWhere('tnc_version_sent', '!=', $currentVersion);
            })
            ->where('email', 'like', '%@%.%')
            ->where('email', 'not like', 'belum\_ada%')
            ->where('email', 'not like', '%test%')
            ->where('email', 'not like', 'tes@%');
    }
}
