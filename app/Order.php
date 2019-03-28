<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'mobile', 'status', 'user_id', 'selected_packet_id', 'operator_price', 'admin_price', 'user_price', 'customer_name', 'operator', 'created_at', 'message'
    ];

    public function get_regular_orders_with_all_fields_table($user_id){
        $orders = DB::table("orders")
                    ->leftJoin('packets', 'packets.id', '=', 'orders.selected_packet_id')
            ->select('orders.id',
                    'orders.status as status_hidden',
                    "orders.customer_name",
                    "orders.mobile",
                    'packets.name as packet_name',
                    'packets.name as type packet_type',
                    'orders.admin_price as purchasing_price',
                    'orders.user_price as selling_price',
                    DB::raw('(orders.user_price - orders.admin_price) as profit'),
                    DB::raw("(CASE orders.status 
                                WHEN 'check_pending' THEN '".__('home_lng.check_pending')."' 
                                WHEN 'selecting_packet' THEN '".__('home_lng.selecting_packet')."' 
                                WHEN 'in_review' THEN '".__('home_lng.in_review')."' 
                                WHEN 'in_progress' THEN '".__('home_lng.in_progress')."' 
                                WHEN 'rejected' THEN '".__('home_lng.rejected')."' 
                                WHEN 'completed' THEN '".__('home_lng.completed')."'
                                WHEN 'canceled' THEN '".__('home_lng.canceled')."' 
                            END) AS status"),
                    "orders.created_at as request_date",
                    "orders.updated_at as response_date"
                    )
            ->where("orders.user_id", $user_id);

        return $orders->get();
    }

    public function get_regular_orders_table($user_id, $status=[], $is_for_today=false){
        $orders = DB::table("orders")
            ->select('id',
                    'status as status_hidden',
                    "operator as operator_hidden",
                    "customer_name",
                    "mobile",
                    DB::raw("(CASE status 
                                WHEN 'check_pending' THEN '".__('orders_lng.check_pending')."' 
                                WHEN 'selecting_packet' THEN '".__('orders_lng.selecting_packet')."' 
                                WHEN 'in_review' THEN '".__('home_lng.in_review')."' 
                                WHEN 'in_progress' THEN '".__('home_lng.in_progress')."' 
                                WHEN 'rejected' THEN '".__('home_lng.rejected')."' 
                                WHEN 'completed' THEN '".__('home_lng.completed')."'
                                WHEN 'canceled' THEN '".__('home_lng.canceled')."' 
                            END) AS status"),
                    "created_at as request_date",
                    "message"
                    )
            ->where("user_id", $user_id)
            ->whereIn("status", $status);

        if($is_for_today)
            $orders->where(function($q) {
                $q->whereDate('created_at', '=', Carbon::today()->toDateString())
                ->orWhereDate('updated_at', '=', Carbon::today()->toDateString());
            });

        return $orders->get();
    }

    public function get_regular_orders_with_extra_culomns_table($user_id, $status=[], $is_for_today=false){
        $orders = DB::table("orders")
                    ->leftJoin('packets', 'packets.id', '=', 'orders.selected_packet_id')
            ->select('orders.id',
                    'orders.status as status_hidden',
                    "orders.customer_name",
                    "orders.mobile",
                    'packets.name as packet_name',
                    'orders.admin_price as purchasing_price',
                    'orders.user_price as selling_price',
                    DB::raw('(orders.user_price - orders.admin_price) as profit'),
                    DB::raw("(CASE orders.status 
                                WHEN 'check_pending' THEN '".__('home_lng.check_pending')."' 
                                WHEN 'selecting_packet' THEN '".__('home_lng.selecting_packet')."' 
                                WHEN 'in_review' THEN '".__('home_lng.in_review')."' 
                                WHEN 'in_progress' THEN '".__('home_lng.in_progress')."' 
                                WHEN 'rejected' THEN '".__('home_lng.rejected')."' 
                                WHEN 'completed' THEN '".__('home_lng.completed')."'
                                WHEN 'canceled' THEN '".__('home_lng.canceled')."' 
                            END) AS status"),
                    "orders.created_at as request_date"
                    )
            ->where("orders.user_id", $user_id)
            ->whereIn("orders.status", $status);

            if($is_for_today)
                $orders->where(function($q) {
                    $q->whereDate('orders.created_at', '=', Carbon::today()->toDateString())
                    ->orWhereDate('orders.updated_at', '=', Carbon::today()->toDateString());
                });
        return $orders->get();
    }

    public function get_admin_orders_table($status=[]){
        $orders = DB::table("orders")
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.id',
                    "users.name as name_of_user",
                    "orders.customer_name",
                    "orders.mobile",
                    "orders.created_at as request_date",
                    'orders.operator as operator_hidden',
                    'users.id as user_id',
                    'orders.message'
            );

        if($status)
            $orders->whereIn("status", $status);

        return $orders->get();
    }

    public function get_admin_orders_with_extra_culomns_table($status=[]){
        $orders = DB::table("orders")
                    ->leftJoin('packets', 'packets.id', '=', 'orders.selected_packet_id')
                    ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.id',
                    'orders.status as status_hidden',
                    'users.name as name_of_user',
                    'orders.mobile',
                    'packets.name as packet_name',
                    "packets.price as purchasing_price",
                    "orders.admin_price as selling_price",
                    DB::raw('(orders.admin_price - packets.price) as profit'),
                    "orders.created_at as request_date",
                    DB::raw("(CASE orders.status 
                                WHEN 'check_pending' THEN '".__('home_lng.check_pending')."' 
                                WHEN 'selecting_packet' THEN '".__('home_lng.selecting_packet')."' 
                                WHEN 'in_review' THEN '".__('home_lng.in_review')."' 
                                WHEN 'in_progress' THEN '".__('home_lng.in_progress')."' 
                                WHEN 'rejected' THEN '".__('home_lng.rejected')."' 
                                WHEN 'completed' THEN '".__('home_lng.completed')."'
                                WHEN 'canceled' THEN '".__('home_lng.canceled')."' 
                            END) AS status")
                    );

        if($status)
            $orders->whereIn("status", $status);
        return $orders->get();
    }
}
