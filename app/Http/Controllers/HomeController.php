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
use Illuminate\Validation\ValidationException;
use View;
use Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

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

            // Format variables for send
            $packets = $this->get_packets_for_checkbox();
        }

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
        $select_operators = $this->getEnumValues('packets', 'operator');
        $select_types = $this->getEnumValues('packets', 'type');
        $lang = Config::get('app.locale');
        $pull = $lang == 'en'?'pull-right':'pull-left';

        return view('home.regular.regular_home', ['checking_orders'     => $checking_orders,
                                                'checking_orders_cols'  => $checking_orders_cols,
                                                'checked_orders'        => $checked_orders,
                                                'checked_orders_cols'   => $checked_orders_cols,
                                                'checking_orders_extra_cols'=> $checking_orders_extra_cols,
                                                'lang'                  => $lang,
                                                'select_operators'      => $select_operators,
                                                'select_types'          => $select_types,
                                                'pull'                  => $pull
                                                ]);

    }

    
//--------------------------------------------------- Actions ---------------------------------------------//
    // Regular
    public function check_number(Request $request){
        $user = Auth::user();

        if(!$user->is_checking_free && $user->balance < 1){
            $returnData = array(__('home_lng.balance_is_not_enough_warning'));
            return response()->json($returnData, 500);
        }elseif(!$user->is_checking_free){
            $user->balance--;
            $user->save();
        }

        $parent_user = User::find($user->created_by_user_id);
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
        $newData['is_number_checked']= '1';
        $createdDate = Order::create($newData);

        $this->create_or_update_parent_order($createdDate['id'], $newData);

        if($parent_user->type != 'agent') {
            $referenced_to_user = User::find($newData['user_id']);
            $msg_title = 'يوجد طلب فحص رقم من :' . $referenced_to_user->name;
            $msg_body = $newData['mobile'];
            sendMessage($msg_title, $msg_body);
        }else{
            $msg_title = 'يوجد طلب فحص رقم من :' . $parent_user->name;
            $msg_body = $newData['mobile'];
            sendMessage($msg_title, $msg_body);
        }

        $toPage = 'home';
        if(Auth::user()->type == 'agent')
            $toPage = 'agent_transfer';
        return response()->json($toPage);
    }

    public function transfer_packet(Request $request){
        $toPage = 'home';
        if(Auth::user()->type == 'agent')
            $toPage = 'agent_transfer';

        $this->is_regular_transfer_validate($request);
        $user = Auth::user();
        $parent_user = User::find($user->created_by_user_id);
        $user_id = Auth::user()->id;

        $newData['user_id'] = $user_id;
        $newData['selected_packet_id'] = $request->input('packet');
        $newData['operator'] = $request->input('operator');
        $newData['status'] = 'in_review';

        $newData['operator_price'] = Packet::where('id', $newData['selected_packet_id'])->select('price')->get()[0]['price'];
        $user_packet = User_Packet::where('user_id', $user_id)->where('packet_id', $newData['selected_packet_id'])->select('admin_price', 'user_price')->get()[0];

        $newData['admin_price'] = $user_packet['admin_price'];
        $newData['user_price'] = $user_packet['user_price'];
        $newData['mobile'] = $request->input('mobile');
        $newData['customer_name'] = $request->input('customer');

        if($user->balance < $newData['admin_price'])
            return redirect("/$toPage")->with('error', __('home_lng.balance_is_not_enough_warning'));

        $user->balance -= $newData['admin_price'];

        $user->save();

        $createdData = Order::create($newData);

        if($parent_user->type != 'agent' && $result = $this->transfer_by_api($createdData)){
            $order = Charging::find($createdData['id']);

            $user->balance += $order->admin_price;
            $user->save();

            $order->delete();

            return redirect("/home")->with('error', $result);
        }elseif($parent_user->type != 'agent'){
            $referenced_to_user = User::find($newData['user_id']);
            $selected_packet = Packet::find($newData['selected_packet_id']);
            $msg_title = 'يوجد طلب تحويل من :'.$referenced_to_user->name.' - '.$selected_packet->name;
            $msg_body = $newData['mobile'];
            sendMessage($msg_title, $msg_body);
        }else{
            $this->create_parent_order_by_transfer_status($createdData['id'], $newData);
            $data = $this->make_order_in_transfer_status_in_agent($createdData['id']);

            if($data['is_fail']){
                $order = Charging::find($createdData['id']);

                $user->balance += $order->admin_price;
                $user->save();

                $order->delete();

                return redirect("/home")->with('error', $data['message']);
            }
        }

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

        if($order->is_number_checked && !$user->is_checking_free){
            $user->balance++;
            $user->save();
        }

        if($order->selected_packet_id != ''){
            $operators_that_have_api = get_operators_that_have_api();
            if (in_array($order->operator, $operators_that_have_api))
            {
                $toPage = 'home';
                if(Auth::user()->type == 'agent')
                    $toPage = 'agent_transfer';

                $res = [
                    'toPage' => $toPage,
                    'fail'   => true,
                    'message'=> __('home_lng.disabled_cancel_warning')
                ];
                return response()->json($res);
            }

            $user->balance += $order->admin_price;

            $user->save();
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

        return response()->json(['toPage'=>$toPage]);
    }

    public function make_packet_in_transfer_status(Request $request){
        $is_fail = false;
        $message = '';
        if($request->selected_packet_id == ''){
            $is_fail = true;
            $message = __('home_lng.select_packet_warning');
            goto endPoint;
        }
        $user = Auth::user();
        $old_user = $user->replicate();
        $parent_user = User::find($user->created_by_user_id);
        $user_id = Auth::user()->id;
        $order_id = $request->order_id;
        $selected_packet_id = $request->selected_packet_id;
        $packet = Packet::find($selected_packet_id);
        $user_packets = User_Packet::where('user_id', $user_id)->where('packet_id', $selected_packet_id)->select('admin_price', 'user_price')->get()[0];

        $order = Order::find($order_id);
        $old_order = $order->replicate();
        $order->selected_packet_id = $selected_packet_id;
        $order->status = 'in_review';
        $order->operator_price = $packet->price;
        $order->admin_price = $user_packets->admin_price;
        $order->user_price = $user_packets->user_price;

        if($user->balance < $order->admin_price){
            $is_fail = true;
            $message = __('home_lng.balance_is_not_enough_warning');
        }elseif($parent_user->type != 'agent' && $result = $this->transfer_by_api($order)){
            $is_fail = true;
            $message = $result;
        }else{
            $user->balance -= $order->admin_price;
            $user->save();
            $order->save();
            if($parent_user->type != 'agent'){
                $referenced_to_user = User::find($order->user_id);
                $selected_packet = Packet::find($order->selected_packet_id);
                $msg_title = 'يوجد طلب تحويل من :'.$referenced_to_user->name.' - '.$selected_packet->name;
                $msg_body = $order->mobile;
                sendMessage($msg_title, $msg_body);
            }else{
                $returned_data = $this->make_order_in_transfer_status_in_agent($order_id);

                if($returned_data['is_fail']){
                    $user->balance = $old_user->balance;

                    $order->selected_packet_id  = $old_order->selected_packet_id;
                    $order->status              = $old_order->status;
                    $order->operator_price      = $old_order->operator_price;
                    $order->admin_price         = $old_order->admin_price;
                    $order->user_price          = $old_order->user_price;

                    $user->save();
                    $order->save();

                    $is_fail = $returned_data['is_fail'];
                    $message = $returned_data['message'];
                    goto endPoint;
                }
            }
        }
        goto endPoint;

        endPoint:$toPage = 'home';
        if(Auth::user()->type == 'agent')
            $toPage = 'agent_transfer';

        $data['is_fail'] = $is_fail;
        $data['message'] = $message;
        $data['toPage'] = $toPage;
        return response()->json($data);
    }

    public  function make_packet_in_transfer_status_for_regular(Request $request){     }

    public function get_regular_checking_orders_table()
    {
        $user_id = Auth::user()->id;
        $table = $this->order_model->get_regular_orders_table($user_id, ['check_pending', 'selecting_packet']);
        $selects_html = $this->get_packet_select_html($table);

        $checking_orders_btns = [
            'btn1' => create_button('', '', 'btn btn-danger', '', 'fas fa-times', '', 'onclick="cancel_order(this.parentNode.parentNode, 2)"'),
            'btn2' => create_button('', __('home_lng.transfer'), 'btn btn-success transfer', '', 'fa fa-rocket', '', 'onclick="make_packet_in_transfer_status($(this).parent().parent())"')
        ];

        foreach ($table as $key=>$row){
            $row->btn1 = $checking_orders_btns['btn1'];
            $row->btn2 = $checking_orders_btns['btn2'];
            $row->selected_packet = $selects_html[$key];
        }

        return response()->json($table);
    }

    public function get_regular_checking_transfers_table()
    {
        $user_id = Auth::user()->id;

        $this->check_transfer_status_in_api($user_id);

        $incomplete_orders = $this->order_model->get_regular_orders_with_extra_culomns_table($user_id, ['in_review', 'in_progress'])->toArray();
        $completed_orders = $this->order_model->get_regular_orders_with_extra_culomns_table($user_id, ['rejected', 'completed', 'canceled'], true)->toArray();

        $table = array_merge($incomplete_orders, $completed_orders);

        $checking_orders_btns = [
            'btn' => create_button('', '', 'btn btn-danger cancel', '', 'fas fa-times', '', 'onclick="cancel_order(this.parentNode.parentNode, 1)"'),
        ];

        foreach ($table as $row)
            $row->btn = $checking_orders_btns['btn'];

        return response()->json($table);
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

        $user = User::find($order->user_id);

        if($status == 'rejected' && $order->is_number_checked &&  !$user->is_checking_free){
            $user->balance++;
            $user->save();
        }

        if($order->selected_packet_id != '' && $status == 'rejected'){
            $user->balance += $order->admin_price;
            $user->save();

            if($user_in_order['type'] == 'agent' && $order->original_order_id != null){
                $user->balance -= $order->user_price;
                $user->save();
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

    public function get_checking_orders_table()
    {
        $table = $this->order_model->get_admin_orders_table(['check_pending']);

        if(Auth::user()->type == 'admin'){
            $checking_orders_btns = [
                'btn1' => create_button('', __('home_lng.send_result'), 'btn btn-primary', '', 'fa fa-rocket', '', 'onclick="send_result(this.parentNode.parentNode)"'),
                'btn2' => create_button('', __('home_lng.reject'), 'btn btn-danger', '', 'fas fa-times', '', 'onclick="change_status(this.parentNode.parentNode, \'rejected\')"'),
            ];
        }else{
            $checking_orders_btns = [
                create_button('', __('home_lng.transfer'), 'btn btn-success transfer', '', 'fa fa-rocket', '', 'onclick="make_packet_in_transfer_status($(this).parent().parent())"'),
                create_button('', __('home_lng.reject'), 'btn btn-danger', '', 'fas fa-times', '', 'onclick="change_status(this.parentNode.parentNode, \'rejected\')"'),
            ];
        }

        foreach ($table as $row){
            $row->btn1 = $checking_orders_btns['btn1'];
            $row->btn2 = $checking_orders_btns['btn2'];
        }

        return response()->json($table);
    }

    public function get_checking_transfers_table()
    {
        $table = $this->order_model->get_admin_orders_with_extra_culomns_table(['in_review', 'in_progress']);

        $checking_transfers_btns = [
            'btn1' => create_button('', __('home_lng.reject'), 'btn btn-danger', '', 'fas fa-times', '', 'onclick="change_status(this.parentNode.parentNode, \'rejected\')"'),
            'btn2' => create_button('', __('home_lng.accept'), 'btn btn-primary accept', '', 'fa fa-check', '', 'onclick="change_status(this.parentNode.parentNode, \'in_progress\')"'),
            'btn3' => create_button('', __('home_lng.transfer_done'), 'btn btn-success', '', 'fa fa-check-square-o', '', 'onclick="change_status(this.parentNode.parentNode, \'completed\')"')
        ];

        foreach ($table as $row){
            $row->btn1 = $checking_transfers_btns['btn1'];
            $row->btn2 = $checking_transfers_btns['btn2'];
            $row->btn3 = $checking_transfers_btns['btn3'];
        }

        return response()->json($table);
    }

    public function get_chargings_table()
    {
        $table = $this->charging_model->get_admin_chargings_table(['in_waiting']);

        $chargings_btns = [
            'btn1' => create_button('', __('home_lng.reject'), 'btn btn-danger', '', 'fas fa-times', '', 'onclick="change_charging_status(this.parentNode.parentNode, \'rejected\')"'),
            'btn2' => create_button('', __('home_lng.accept'), 'btn btn-success', '', 'fa fa-check', '', 'onclick="change_charging_status(this.parentNode.parentNode, \'accepted\')"'),
        ];

        foreach ($table as $row){
            $row->btn1 = $chargings_btns['btn1'];
            $row->btn2 = $chargings_btns['btn2'];
        }

        return response()->json($table);
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
            $packet = Packet::where('id', $order['selected_packet_id'])->get()[0];
            $user_packet = User_Packet::where('packet_id', $packet['id'])->where('user_id', $user['id'])->get()[0];
            $parent_user_packet = User_Packet::where('packet_id', $packet['id'])->where('user_id', $parent_user['id'])->get()[0];
            $newData['user_id'] = $parent_user['id'];
            $newData['original_order_id'] = $orderId;
            $newData['status'] = 'selecting_packet';
            $newData['admin_price'] = $parent_user_packet['admin_price'];
            $newData['user_price'] = $user_packet['admin_price'];
            Order::create($newData);
        }
    }

    private function make_order_in_transfer_status_in_agent($order_id){
        $regular_order_id = $order_id;
        $agent_order_id = Order::where('original_order_id', $regular_order_id)->get()[0]['id'];

        $agent_order = Order::find($agent_order_id);
        $regular_order = Order::where('id', $regular_order_id)->get()[0];
        $agent_user = User::find($agent_order['user_id']);
        $regular_user = User::find($regular_order['user_id']);

        $packet = Packet::find($regular_order['selected_packet_id']);
        $user_packets = User_Packet::where('user_id', $agent_user->id)->where('packet_id', $regular_order['selected_packet_id'])->select('admin_price', 'user_price')->get()[0];

        $agent_order->selected_packet_id = $regular_order['selected_packet_id'];
        $agent_order->status = 'in_review';
        $agent_order->operator_price = $packet->price;
        $agent_order->admin_price = $user_packets->admin_price;

        $regular_user_packet = User_Packet::where('packet_id', $packet->id)->where('user_id', $regular_user->id)->get()[0];

        $agent_order->user_price = $regular_user_packet['admin_price'];

        $is_fail = false;
        $message = '';
        if($result = $this->transfer_by_api($agent_order)){
            $is_fail = true;
            $message = $result;
        }else{
            $agent_user->balance = $agent_user->balance - $agent_order->admin_price + $agent_order->user_price;

            $agent_user->save();
            $agent_order->save();

            $referenced_to_user_name = $agent_user->name. ' - '. $regular_user->name;
            $selected_packet = Packet::find($agent_order->selected_packet_id);
            $msg_title = 'يوجد طلب تحويل من :'.$referenced_to_user_name.' - '.$selected_packet->name;
            $msg_body = $agent_order->mobile;
            sendMessage($msg_title, $msg_body);
        }

        $data['is_fail'] = $is_fail;
        $data['message'] = $message;

        return $data;
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

    private function transfer_by_api($order){
        $operator = $order->operator;
        $operators_that_have_api = get_operators_that_have_api();

        if (in_array($operator, $operators_that_have_api))
        {
            $packet = Packet::find($order->selected_packet_id);
            $api_data['api_id']         = $packet->api_id;
            $api_data['order_id']       = $order->id;
            $api_data['operator']       = $order->operator;
            $api_data['packet_type']    = $packet->type;
            $api_data['mobile']         = $order->mobile;

            return $this->api_check_number($api_data);
        }
        return [];
    }

    private function api_check_number($api_data){
        $api_url_arr = get_api_data('user_status_check', $api_data);
        $result = CallAPI( $api_url_arr['site_url'], $api_url_arr['api'], $api_url_arr['params_data']);
        $result_arr = explode('|', $result);

        if($result_arr[0] == 'NOK'){
            return __("main_lng.".$result_arr[1]);
        }elseif($result_arr[0] == 'OK'){
            if(count($result_arr) == 4)
                return __("main_lng.".$result_arr[2]);
            else
                return $this->api_transfer($api_data);
        }else
            return $result;
    }

    private function api_transfer($api_data){
        $api_url_arr = get_api_data('send_num_for_transfer', $api_data);
        $result = CallAPI( $api_url_arr['site_url'], $api_url_arr['api'], $api_url_arr['params_data']);
        $result_arr = explode('|', $result);
        if($result_arr[2] == 'Talebiniz İşleme Alınmıştır.')
            return [];
        else
            return __("main_lng.".$result_arr[2]);
    }

    private function check_transfer_status_in_api($user_id){
        $operators_that_have_api = get_operators_that_have_api();
        $in_waiting_statuses = ['in_review', 'in_progress'];
        $api_in_waiting_orders = Order::where('user_id', $user_id)->whereIn('operator', $operators_that_have_api)->whereIn('status', $in_waiting_statuses)->get();
        foreach ($api_in_waiting_orders as $api_in_waiting_order){
            $api_data['order_id']       = $api_in_waiting_order['id'];
            $api_data['operator']       = $api_in_waiting_order['operator'];
            $api_data['packet_type']    = $api_in_waiting_order['type'];
            $api_data['mobile']         = $api_in_waiting_order['mobile'];
            $api_url_arr = get_api_data('transfer_status_check', $api_data);
            $result = CallAPI( $api_url_arr['site_url'], $api_url_arr['api'], $api_url_arr['params_data']);
            $result_arr = explode(':', $result);

            $order_id = $api_in_waiting_order['id'];
            $status = '';
            if($result_arr[0] == '1')
                $status = 'completed';
            elseif ($result_arr[0] == '3')
                $status = 'rejected';

            if($status != '')
                $this->api_change_order_status_by_id($order_id, $status);
        }
    }

    private function api_change_order_status_by_id($order_id, $status){

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
}
