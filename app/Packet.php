<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Packet extends Model
{
    protected $table = 'packets';
    protected $fillable = [
        'operator', 'sms', 'minutes', 'internet', 'type', 'price', 'is_global', 'is_teens'
    ];

    public function get_packets_table(){
        $packets = DB::table("packets")
            ->select('id',
                    "operator",
                    "sms",
                    "minutes",
                    "internet",
                    "type",
                    "price",
                    DB::raw("(CASE WHEN is_global = 1 THEN '".__('main_lng.global')."' ELSE '".__('main_lng.private')."' END) AS is_global"),
                    DB::raw("(CASE WHEN is_teens = 1 THEN '".__('main_lng.teens')."' ELSE ' ' END) AS is_teens")
                    )
            ->get();

        return $packets;
    }
}
