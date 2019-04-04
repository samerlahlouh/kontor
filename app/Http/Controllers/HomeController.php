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

class HomeController extends Controller
{
    public function __construct(){
        $this->order_model = new Order();
        $this->packet_model = new Packet();
        $this->offer_model = new Offer();
        $this->charging_model = new Charging();
    }

//-------------------------------------------------- Indexes -----------------------------------------------//
    public function index(){
       if (Auth::user() && (Auth::user()->type == 'admin' || Auth::user()->type == 'agent'))
            return $this->index_admin();
        elseif (Auth::user() && Auth::user()->type == 'regular')
            return $this->index_regular();
        else
            return view('home.index');
    }

    private function index_admin(){
        $user = Auth::user();
        View::share('page_js', 'admin_home');

        // Format checking orders table
        $checking_orders = $this->order_model->get_admin_orders_table(['check_pending']); 
        $checking_orders_cols = [
            'id',
            __('home_lng.name_of_user'),
            __('home_lng.customer_name'),
            __('home_lng.mobile'),
            __('home_lng.request_date'),
            'operator_hidden',
            'user_id',
            __('home_lng.message'),
        ];

        $checking_transfers = [];
        $checking_transfers_cols = [];
        $chargings = [];
        $chargings_cols = [];
        $packets = [];
        if($user->type == 'admin'){
            // Format checking transfers table
            $checking_transfers = $this->order_model->get_admin_orders_with_extra_culomns_table(['in_review', 'in_progress'])->toArray();
            $checking_transfers_cols = [
                'id',
                'status_hidden',
                __('home_lng.name_of_user'),
                __('home_lng.mobile'),
                __('home_lng.packet'),
                __('home_lng.purchasing_price'),
                __('home_lng.selling_price'),
                __('home_lng.profit'),
                __('home_lng.request_date'),
                __('home_lng.status'),
            ];

            // Format chargings table
            $chargings = $this->charging_model->get_admin_chargings_table(['in_waiting'])->toArray();
            $chargings_cols = [
                'id',
                __('home_lng.user'),
                __('home_lng.type'),
                __('home_lng.amount'),
                __('home_lng.balance_before'),
                __('home_lng.balance_after'),
                __('home_lng.notes'),
                __('home_lng.request_date'),
            ];

            // Format variables for send
            $packets = $this->get_packets_for_checkbox();
        }


        return view('home.admin.index', ['checking_orders'                  => $checking_orders,
                                                'checking_orders_cols'      => $checking_orders_cols,
                                                'checking_transfers'        => $checking_transfers,
                                                'checking_transfers_cols'   => $checking_transfers_cols,
                                                'chargings'                 => $chargings,
                                                'chargings_cols'            => $chargings_cols,
                                                'packets'                   => $packets
                                                ]);
    }

    private function index_regular(){
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

    
//--------------------------------------------------- Actions ---------------------------------------------//
    // Regular
    public function check_number(Request $request){
        $newData = [];
        $number         = $request->number;
        $customer_name  = $request->customer_name;
        $operator       = $request->operator;
        $message        = $request->message;

        $newData['user_id']         = Auth::user()->id;
        $newData['mobile']          = $number;
        $newData['operator']        = $operator;
        $newData['status']          = 'check_pending';
        $newData['customer_name']   = $customer_name;
        $newData['created_at']      = Carbon::now();
        $newData['message']         = $message;
        $createdDate = Order::create($newData);

        $this->create_or_update_parent_order($createdDate['id'], $newData);

        $toPage = 'home';
        if(Auth::user()->type == 'agent')
            $toPage = 'agent_transfer';
        return response()->json($toPage);
    }

    public function transfer_packet(Request $request){

        $this->is_regular_transfer_validate($request);
        $user = Auth::user();
        $user_id = Auth::user()->id;

        $newData['user_id'] = $user_id;
        $newData['selected_packet_id'] = $request->input('packet');
        $newData['status'] = 'in_review';

        $newData['operator_price'] = Packet::where('id', $newData['selected_packet_id'])->select('price')->get()[0]['price'];
        $user_packet = User_Packet::where('user_id', $user_id)->where('packet_id', $newData['selected_packet_id'])->select('admin_price', 'user_price')->get()[0];
        
        $newData['admin_price'] = $user_packet['admin_price'];
        $newData['user_price'] = $user_packet['user_price'];
        $newData['mobile'] = $request->input('mobile');
        $newData['customer_name'] = $request->input('customer');

        if($user->balance < $newData['admin_price'])
            return redirect("/home")->with('error', __('home_lng.balance_is_not_enough_warning'));

        $user->balance -= $newData['admin_price'];

        $user->save();

        $toPage = 'home';
        if(Auth::user()->type == 'agent')
            $toPage = 'agent_transfer';

        $createdDate = Order::create($newData);

        $this->create_parent_order_by_transfer_status($createdDate['id'], $newData);

        return redirect("/$toPage");
    }

    public function get_packets_by_operator_and_type(Request $request){
        $operator = $request->operator;
        $type = $request->type;

        $is_global = 'none';
        if(isset($request->is_global))
            $is_global = $request->is_global;
        $packets = $this->packet_model->get_packets_by_operator_and_type($operator, $type, $is_global)->toArray();

        $select_packets = [];
        foreach ($packets as $packet)
            $select_packets[$packet->id] = $packet->name;

        return response()->json($select_packets);
    }

    public function cancel_order_by_id(Request $request){
        $user = Auth::user();
        $order_id = $request->order_id;
        $parent_user = User::find($user->created_by_user_id);

        $order = Order::find($order_id);

        if($order->selected_packet_id != ''){
            $user->balance += $order->admin_price;

            if($parent_user->type == 'agent'){
                $parent_order_id = Order::where('original_order_id', $order_id)->get()[0]['id'];
                $parent_order = Order::find($parent_order_id);
                if($parent_order->selected_packet_id != '')
                    $parent_user->balance += $parent_order->admin_price;
            }
        }


        if($parent_user->type == 'agent'){
            $parent_order_id = Order::where('original_order_id', $order_id)->get()[0]['id'];
            $parent_order = Order::find($parent_order_id);
            $parent_order->delete();
        }
        $user->save();
        $parent_user->save();
        $order->delete();


        $toPage = 'home';
        if(Auth::user()->type == 'agent')
            $toPage = 'agent_transfer';

        return response()->json($toPage);
    }

    public function make_packet_in_transfer_status(Request $request){
        $user = Auth::user();
        $user_id = Auth::user()->id;
        $order_id = $request->order_id;
        $selected_packet_id = $request->selected_packet_id;
        $packet = Packet::find($selected_packet_id);
        $user_packets = User_Packet::where('user_id', $user_id)->where('packet_id', $selected_packet_id)->select('admin_price', 'user_price')->get()[0];

        $order = Order::find($order_id);
        $order->selected_packet_id = $selected_packet_id;
        $order->status = 'in_review';
        $order->operator_price = $packet->price;
        $order->admin_price = $user_packets->admin_price;
        $order->user_price = $user_packets->user_price;

        $is_fail = false;
        $message = '';
        if($user->balance < $order->admin_price){
            $is_fail = true;
            $message = __('home_lng.balance_is_not_enough_warning');
        }else{
            $user->balance -= $order->admin_price;

            $user->save();
            $order->save();
        }

        $toPage = 'home';
        if(Auth::user()->type == 'agent')
            $toPage = 'agent_transfer';

        $data['is_fail'] = $is_fail;
        $data['message'] = $message;
        $data['toPage'] = $toPage;
        return response()->json($data);
    }

    public function make_packet_in_transfer_status_for_regular(Request $request){
        $user = Auth::user();
        $child_order_id = $request->order_id;

        $parent_order = Order::where('original_order_id', $child_order_id)->get()[0];
        $child_order = Order::where('id', $child_order_id)->get()[0];

        $order_id = $parent_order['id'];
        $selected_packet_id = $child_order['selected_packet_id'];

        $packet = Packet::find($selected_packet_id);
        $user_packets = User_Packet::where('user_id', $user->id)->where('packet_id', $selected_packet_id)->select('admin_price', 'user_price')->get()[0];

        $order = Order::find($order_id);
        $order->selected_packet_id = $selected_packet_id;
        $order->status = 'in_review';
        $order->operator_price = $packet->price;
        $order->admin_price = $user_packets->admin_price;
        $order->user_price = $user_packets->user_price;

        $is_fail = false;
        $message = '';
        if($user->balance < $order->admin_price){
            $is_fail = true;
            $message = __('home_lng.balance_is_not_enough_warning');
        }else{
            $user->balance -= $order->admin_price;

            $user->save();
            $order->save();
        }

        $toPage = 'home';
        $data['is_fail'] = $is_fail;
        $data['message'] = $message;
        $data['toPage'] = $toPage;
        return response()->json($data);
    }

    // Admin
    public function change_order_status_by_id(Request $request){
        $order_id = $request->order_id;
        $status = $request->status;

        $order = Order::find($order_id);

        $user_in_order = User::select('type')->where('id', $order->user_id)->get()[0];
        if($user_in_order['type'] == 'agent' && $order->original_order_id != null){
            $child_order = Order::find($order->original_order_id);
            $child_order->status = $status;
            $child_order->save();
        }
        $order->status = $status;

        if($order->selected_packet_id != '' && $status == 'rejected'){
            $user = User::find($order->user_id);
            $user->balance += $order->admin_price;
            $user->save();

            if($user_in_order['type'] == 'agent' && $order->original_order_id != null){
                $child_order = Order::find($order->original_order_id);
                $child_user = User::find($child_order->user_id);
                $child_user->balance += $child_order->admin_price;
                $child_user->save();
            }

            if(Auth::user()->type == 'agent'){
                $parent_order_id = Order::select('id')->where('original_order_id', $order_id)->get()[0]['id'];
                $parent_order = Order::find($parent_order_id);
                $parent_order->delete();
            }
        }
        $order->save();
        return;
    }

    public function change_charging_status_by_id(Request $request){
        $current_user = Auth::user();
        $is_fail = false;
        $message = '';

        $charging_id    = $request->charging_id;
        $status         = $request->status;

        $charging = Charging::find($charging_id);

        $user = User::find($charging->user_id);
        if($status == 'accepted'){
            $current_user->balance -= $charging->amount;
            $user->balance += $charging->amount;
        }

        $charging->status = $status;

        if($status == 'accepted' && $current_user->type == 'agent' && $current_user->balance < $charging->amount){
            $is_fail = true;
            $message = __('home_lng.balance_is_not_enough_warning');
        }else{
            $current_user->save();
            $user->save();
            $charging->save();
        }

        $res_data['is_fail'] = $is_fail;
        $res_data['message'] = $message;
        return response()->json($res_data);
    }

    public function send_result_to_user(Request $request){
        $order_id = $request->input('id');
        $packet_ids = $request->input('packet_ids');
        $packet_ids = isset($packet_ids) ?$packet_ids : [];

        foreach ($packet_ids as $packet_id) {
            $newData['order_id'] = $order_id;
            $newData['packet_Id'] = $packet_id;
            Offer::create($newData);
        }

        $order = Order::find($order_id);
        $order->status = 'selecting_packet';
        $order->save();
        $this->send_result_to_parent_order($order->id, ['status'=>'selecting_packet']);
        
        return redirect("/home");
    }

    public function get_unavailable_packets_by_user(Request $request){
        $user_id = $request->user_id;
        $packets = User_Packet::where('user_id', $user_id)->where('is_available', false)->select('packet_id')->get();
        return response()->json($packets);
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

    private function is_regular_transfer_validate($request){
        $rules = array(
            'packet'    =>'required',
        );
        $this->validate($request ,$rules);
    }

    // Get packets group by operator for checkbox elements
    private function get_packets_for_checkbox(){
        $packets=[];
        $operators = $this->getEnumValues('packets', 'operator');
        foreach ($operators as $operator) {
            $full_packets = Packet::where('is_global', false)->where('operator', $operator)->select('id', 'name', 'is_teens')->get();
            $ids=[];
            $names=[];
            $is_teens=[];
            foreach ($full_packets as $full_packet) {
                array_push($ids, $full_packet['id']);
                array_push($names, $full_packet['name']);
                array_push($is_teens, $full_packet['is_teens']);
            }

            $packets[$operator]['ids'] = $ids;
            $packets[$operator]['names'] = $names;
            $packets[$operator]['is_teens'] = $is_teens;
        }
        return $packets;
    }

    // Test if user' parent user is agent for making order with
    private function create_or_update_parent_order($orderId, $newData){
        $order = Order::where('id', $orderId)->get()[0];
        $user = User::where('id', $order['user_id'])->get()[0];
        $parent_user = User::where('id', $user['created_by_user_id'])->get()[0];
        if($parent_user['type'] == 'agent'){
            if($order['original_order_id']){
                $parent_order = Order::find($order['original_order_id']);
                $parent_order->fill($newData);
                $parent_order->save();
            }else{
                $newData['user_id'] = $parent_user['id'];
                $newData['original_order_id'] = $orderId;
                Order::create($newData);
            }
        }
    }

    // Test if user' parent user is agent for making order with
    private function create_parent_order_by_transfer_status($orderId, $newData){
        $order = Order::where('id', $orderId)->get()[0];
        $user = User::where('id', $order['user_id'])->get()[0];
        $parent_user = User::where('id', $user['created_by_user_id'])->get()[0];
        if($parent_user['type'] == 'agent'){
            $newData['user_id'] = $parent_user['id'];
            $newData['original_order_id'] = $orderId;
            $newData['status'] = 'selecting_packet';
            Order::create($newData);
        }
    }

    private function send_result_to_parent_order($parentOrderId, $newData){
        $parent_order = Order::where('id', $parentOrderId)->get()[0];
        if($parent_order['original_order_id']){
            $child_order = Order::where('id', $parent_order['original_order_id'])->get()[0];
            $offers = Offer::select('packet_id')->where('order_id', $parent_order['id'])->get();
            foreach ($offers as $offer) {
                $newOffer['order_id'] = $child_order['id'];
                $newOffer['packet_Id'] = $offer['packet_id'];
                Offer::create($newOffer);
            }

            $updated_child_order = Order::find($child_order['id']);
            $updated_child_order->fill($newData);
            $updated_child_order->save();

        }
    }
}
