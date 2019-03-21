<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Educators\User_Packet;
use Auth;

class Packet extends Model
{
    protected $table = 'packets';
    protected $fillable = [
        'operator', 'sms', 'minutes', 'internet', 'type', 'price', 'is_global', 'is_teens', 'name'
    ];

    public function get_packets_table(){
        $allPackets = DB::table("packets")
            ->select('id',
                    "name",
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

        $user_id = Auth::user()->id;
        $user_type = Auth::user()->type;
        $packets = [];
        
        if($user_type != 'admin'){
            foreach ($allPackets as $packet) {
                $user_packet = User_Packet::where('packet_id', $packet->id)->where('user_id', $user_id)->get()[0];
                if($user_packet['is_available']){
                    $packet->price = $user_packet['admin_price'];
                    array_push($packets, $packet);
                }
            }
        }else
            $packets = $allPackets;
            
        return $packets;
    }
}
