<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\Order;
use Auth;
use Config;
use View;

class OrderController extends Controller
{
    public function __construct(){
        $this->order_model = new Order();
    }

    public function index(){
        View::share('page_js', 'regular_orders');
        $lang = Config::get('app.locale');
        $user_id = Auth::user()->id;
        $orders = $this->order_model->get_regular_orders_with_all_fields_table($user_id); 

        $orders_cols = [
            'id',
            'status_hidden',
            __('regular_orders_lng.customer_name'),
            __('regular_orders_lng.mobile'),
            __('regular_orders_lng.operator'),
            __('regular_orders_lng.packet'),
            __('regular_orders_lng.packet_type'),
            __('regular_orders_lng.purchasing_price'),
            __('regular_orders_lng.selling_price'),
            __('regular_orders_lng.profit'),
            __('regular_orders_lng.status'),
            __('regular_orders_lng.request_date'),
            __('regular_orders_lng.response_date'),
        ];

        return view('regular_orders', ['orders' => $orders,
                                        'orders_cols' => $orders_cols,
                                        'is_parent' => false,
                                        'lang' => $lang
                                        ]);
    }

    public function admin_index(){
        View::share('page_js', 'regular_orders');
        $lang = Config::get('app.locale');
        $orders = $this->order_model->get_admin_orders_with_all_fields_table();

        $orders_cols = [
            'id',
            'status_hidden',
            __('regular_orders_lng.user'),
            __('regular_orders_lng.customer_name'),
            __('regular_orders_lng.mobile'),
            __('regular_orders_lng.operator'),
            __('regular_orders_lng.packet'),
            __('regular_orders_lng.packet_type'),
            __('regular_orders_lng.purchasing_price'),
            __('regular_orders_lng.selling_price'),
            __('regular_orders_lng.profit'),
            __('regular_orders_lng.status'),
            __('regular_orders_lng.request_date'),
            __('regular_orders_lng.response_date'),
        ];

        return view('regular_orders', ['orders' => $orders,
                                        'orders_cols' => $orders_cols,
                                        'is_parent' => true,
                                        'lang' => $lang
                                        ]);
    }

    public function get_filtered_admin_orders_table(Request $request){
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $filtered_orders = $this->order_model->get_admin_orders_with_all_fields_table($from_date, $to_date);

        return response()->json($filtered_orders);
    }

    public function get_filtered_regular_orders_table(Request $request){
        $user_id = Auth::user()->id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $filtered_orders = $this->order_model->get_regular_orders_with_all_fields_table($user_id, $from_date, $to_date);

        return response()->json($filtered_orders);
    }
}
