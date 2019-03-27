<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;

class Offer extends Model
{
    protected $table = 'offers';
    protected $fillable = [
        'order_id', 'packet_Id'
    ];

    public function get_offers_for_select($order_id=''){
        $orders = DB::table("offers")
                    ->leftJoin('packets', 'packets.id', '=', 'offers.packet_Id')
                    ->leftJoin('user_packets', function($join)
                         {
                            $user_id = Auth::user()->id;
                            $join->on('user_packets.packet_id', '=', 'packets.id');
                            $join->on('user_packets.user_id', '=', DB::raw("'$user_id'"));
                         })
            ->select('offers.id',
                    'packets.id as packet_id',
                    DB::raw("CONCAT(packets.name,' (',user_packets.user_price, ' TL)') as packet_name")
                        );

        if($order_id)
            $orders->where('offers.order_id', $order_id);

        return $orders->get();
    }
}
