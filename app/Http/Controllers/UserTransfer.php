<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\Order;
use Educators\Packet;
use Educators\User;
use Educators\User_Packet;
use Educators\Charging;
use Educators\Offer;
use Auth;
use View;
use Config;
use Carbon\Carbon;

class UserTransfer extends Controller
{
    public function __construct(){
        $this->order_model = new Order();
        $this->packet_model = new Packet();
        $this->offer_model = new Offer();
        $this->charging_model = new Charging();
    }

    public function index(){
        View::share('page_js', 'regular_home');
        $user_id = Auth::user()->id;

        // Format checking orders table
        $incomplete_orders = [];
        $completed_orders = [];
        $checking_orders = $this->order_model->get_regular_orders_table($user_id, ['check_pending', 'selecting_packet'])->toArray();
        $checking_orders_cols = [
            'id',
            'status_hidden',
            'operator_hidden',
            __('home_lng.customer_name'),
            __('home_lng.mobile'),
            __('home_lng.status'),
            __('home_lng.request_date'),
            __('home_lng.message'),
        ];

        $selects_html = $this->get_packet_select_html($checking_orders);
        $checking_orders_extra_cols = [
            [
                'type'  =>  'custom',
                'title' =>  __('home_lng.select_packet'),
                'html' =>  $selects_html
            ]
        ];

        // Format checking transfers table
        $incomplete_orders = [];
        $completed_orders = [];
        $incomplete_orders = $this->order_model->get_regular_orders_with_extra_culomns_table($user_id, ['in_review', 'in_progress'])->toArray();
        $completed_orders = $this->order_model->get_regular_orders_with_extra_culomns_table($user_id, ['rejected', 'completed', 'canceled'], true)->toArray();

        $checked_orders = array_merge($incomplete_orders, $completed_orders);

        $checked_orders_cols = [
            'id',
            'status_hidden',
            __('home_lng.customer_name'),
            __('home_lng.mobile'),
            __('home_lng.packet'),
            __('home_lng.purchasing_price'),
            __('home_lng.selling_price'),
            __('home_lng.profit'),
            __('home_lng.status'),
            __('home_lng.request_date'),
        ];

        // Format variables for send
        $lang = Config::get('app.locale');
        $select_operators = $this->getEnumValues('packets', 'operator');
        $select_types = $this->getEnumValues('packets', 'type');

        return view('home.regular.regular_home', ['checking_orders'     => $checking_orders,
            'checking_orders_cols'  => $checking_orders_cols,
            'checked_orders'        => $checked_orders,
            'checked_orders_cols'   => $checked_orders_cols,
            'checking_orders_extra_cols'=> $checking_orders_extra_cols,
            'lang'                  => $lang,
            'select_operators'      => $select_operators,
            'select_types'          => $select_types
        ]);

    }

//------------------------------------------------ Functions ---------------------------------------------//
    private function get_packet_select_html($checking_orders){
        $checking_order_offers = [];

        $selects_html = [];

        foreach ($checking_orders as $checking_order) {
            $select_html = '';
            if($checking_order->status_hidden == 'selecting_packet'){
                $select_html = '<div class="form-group">
                                    <select class="form-control">
                                        <option value="0" hidden disabled selected>'. __('home_lng.select_packet').'</option>';

                // Format offers elements
                $checking_order_offers = $this->offer_model->get_offers_for_select($checking_order->id);
                if(count($checking_order_offers) != 0){
                    $select_html .= '<optgroup label="'.__('home_lng.offers').'">';

                    foreach ($checking_order_offers as $checking_order_offer)
                        $select_html .= "<option value='$checking_order_offer->packet_id'>$checking_order_offer->packet_name</option>";

                    $select_html .= '</optgroup>';
                }

                // Format all packets elements
                $all_packets = $this->packet_model->get_packets_by_operator_and_type($checking_order->operator_hidden, '', 1);
                $select_html .= '<optgroup label="'.__('home_lng.all_packets').'">';

                foreach ($all_packets as $packet)
                    $select_html .= "<option value='$packet->id'>$packet->name</option>";

                $select_html .= '       </optgroup>
                                    </select>
                                </div>';
            }else
                $select_html = '';

            array_push($selects_html, $select_html);
        }


        return $selects_html;
    }
}
