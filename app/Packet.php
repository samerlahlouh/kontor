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

    public function get_regular_packets_table($user_id){
        $packets = DB::table("user_packets")
            ->leftJoin('packets', 'packets.id', '=', 'user_packets.packet_id')
            ->select('user_packets.id',
                    "packets.name as packet_name",
                    "user_packets.admin_price as purchasing_price",
                    "user_packets.user_price as selling_price"
                    )
            ->where("user_packets.user_id", $user_id)
            ->where("user_packets.is_available", 1)
            ->get();

        return $packets;
    }

    public function get_packets_by_operator_and_type($operator, $type='', $is_global='none'){
        $packets = DB::table("packets")
            ->leftJoin('user_packets', function($join)
                         {
                            $user_id = Auth::user()->id;
                            $join->on('user_packets.packet_id', '=', 'packets.id');
                            $join->on('user_packets.user_id', '=', DB::raw("'$user_id'"));
                         })
            ->select('packets.id',
                    DB::raw("CONCAT(packets.name,' (',user_packets.user_price, ' TL)') as name")
                    )
            ->where("packets.operator", $operator);
        
        if($type)
           $packets->where("packets.type", $type);

        if($is_global != 'none')
           $packets->where("packets.is_global", $is_global);

        return $packets->get();
    }
}
