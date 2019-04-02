<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;

class User_Packet extends Model
{
    protected $table = 'user_packets';
    protected $fillable = [
        'user_id', 'packet_id', 'admin_price', 'user_price', 'is_available'
    ];
    
    public function get_user_packets_table($user_id){
        $user_type = Auth::user()->type;
        $packet_price = $user_type == 'admin'? "packets.price" : DB::raw("'empty' as packet_price");

        $all_user_packets = DB::table("user_packets")
            ->leftJoin('packets', 'packets.id', '=', 'user_packets.packet_id')
            ->select('user_packets.id',
                    "packets.id as packet_id",
                    "packets.name as packet_name",
                    $packet_price,
                    "user_packets.admin_price as selling_price",
                    DB::raw("(CASE WHEN is_available = 1 THEN '".__('users_lng.available')."' ELSE '".__('users_lng.unavailable')."' END) AS is_available")
                    )
            ->where("user_packets.user_id", $user_id)
            ->get();

        $user_type = Auth::user()->type;
        $packets = [];
        
        $user_packets = [];
        if($user_type != 'admin'){
            foreach ($all_user_packets as $user_packet) {
                $user_packet_for_user = User_Packet::where('packet_id', $user_packet->packet_id)->where('user_id', Auth::user()->id)->get()[0];
                if($user_packet_for_user['is_available']){
                    $user_packet->packet_price = $user_packet_for_user['admin_price'];
                    array_push($user_packets, $user_packet);
                }
            }
        }else
            $user_packets = $all_user_packets;

        return $user_packets;
    }

    public function get_packet_users_table($packet_id){
        $user_type = Auth::user()->type;
        
        $packet_price = '';
        if($user_type == 'admin'){
            $packet_price = "packets.price as packet_price";
        }elseif($user_type == 'agent'){
            $admin_price = User_packet::where('packet_id', $packet_id)->where('user_id', Auth::user()->id)->get()[0]['admin_price'];
            $packet_price = DB::raw("'$admin_price' as packet_price");
        }

        $packet_users = DB::table("user_packets")
            ->leftJoin('users', 'users.id', '=', 'user_packets.user_id')
            ->leftJoin('packets', 'packets.id', '=', 'user_packets.packet_id')
            ->select('user_packets.id',
                    "users.id as user_id",
                    "users.name as user",
                    $packet_price,
                    "user_packets.admin_price as selling_price",
                    DB::raw("(CASE WHEN is_available = 1 THEN '".__('users_lng.available')."' ELSE '".__('users_lng.unavailable')."' END) AS is_available")
                    )
            ->where("user_packets.packet_id", $packet_id)
            ->where("users.created_by_user_id", Auth::user()->id);

        return $packet_users->get();
    }
}
