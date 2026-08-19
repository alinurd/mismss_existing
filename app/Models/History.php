<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class History extends Model
{
    use HasFactory;

    protected $table = 'history_list';
    protected $primarykey = 'id';

    protected $order = ['history_list.created_at' => 'DESC'];
    protected $column_order = [
        'id',
        'created_at',
        'created_by',
        'description'
    ];

    public function getDTQuery(Request $request, $katakunci = '')
    {        

        $select = "history_list.*";
        $query = "history_list.id!=''";
        $where = "";

        $column_search = [
            'created_at',
            'created_by',
            'whatsapp_desc',
            'description'
        ];

        if (!empty($katakunci)) {
            for ($i = 0; $i <= count($column_search) - 1; $i++) {
                if ($i < count($column_search) - 1) {
                    $where .= $query . "AND $column_search[$i] LIKE '%$katakunci%' OR ";
                } else {
                    $where .= $query . "AND $column_search[$i] LIKE '%$katakunci%'";
                }
            }
        } else {
            $where = $query;
        }

        if ($request->input('order')) {
            $orderByA = $this->column_order[$request->input('order')['0']['column']];
            $orderByB = $request->input('order')['0']['dir'];
        } else if (isset($this->order)) {
            $orderByA = key($this->order);
            $orderByB = $this->order[key($this->order)];
        }

        $data = [
            'select' => $select,
            'where' => $where,
            'orderByA' => $orderByA,
            'orderByB' => $orderByB
        ];

        return $data;
    }

    public function getDT(Request $request, $katakunci)
    {

        $historyModel = new History;
        $query = $historyModel->getDTQuery($request, $katakunci);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
                return History::selectRaw($query['select'])
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }

        return History::selectRaw($query['select'])
                ->whereRaw($query['where'])
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
    }

    public function countFiltered(Request $request, $katakunci)
    {
        $historyModel = new History;
        $query = $historyModel->getDTQuery($request, $katakunci);
        return count(History::selectRaw($query['select'])
        ->whereRaw($query['where'])
        ->get());
    }

    public function countAll()
    {
        return History::count();
    }
}
