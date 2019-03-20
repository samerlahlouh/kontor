<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User_Packet extends Model
{
    protected $table = 'user_packets';
    protected $fillable = [
        'user_id', 'packet_id', 'admin_price', 'user_price', 'is_available'
    ];
    
    public function get_user_packets_table($user_id){
        $packets = DB::table("user_packets")
            ->leftJoin('packets', 'packets.id', '=', 'user_packets.packet_id')
            ->select('user_packets.id',
                    "packets.id as packet_id",
                    "packets.name as packet_name",
                    "packets.price as packet_price",
                    "user_packets.admin_price",
                    "user_packets.is_available",
                    DB::raw("(CASE WHEN is_available = 1 THEN '".__('users_lng.available')."' ELSE '".__('users_lng.unavailable')."' END) AS is_available")
                    )
            ->where("user_packets.user_id", $user_id)
            ->get();

        return $packets;
    }
}
