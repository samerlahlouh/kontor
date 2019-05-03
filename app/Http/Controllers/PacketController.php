<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\Packet;
use Educators\User;
use Educators\User_Packet;
use Educators\Group;
use Auth;
use View;

class PacketController extends Controller
{
    public function __construct(){
        $this->packet_model = new Packet();
        $this->user_packet_model = new User_Packet();
        $this->user_model = new User();
    }

    //------------------------------------------------indexes-----------------------------------------------//
    public function index(){
        View::share('page_js', 'packets');
        $packets = $this->packet_model->get_packets_table();

        $operators = $this->getEnumValues('packets', 'operator');
        $types = $this->get_types_for_select('turkcell');

        $is_global = ['1'=>__('main_lng.private'), '2'=>__('main_lng.global')];
        $is_teens = ['1'=>__('main_lng.no'), '2'=>__('main_lng.yes')];
        $cols = [
            'id',
            __('packets_lng.name'),
            __('packets_lng.api_id'),
            __('packets_lng.operator'),
            __('packets_lng.sms'),
            __('packets_lng.minutes'),
            __('packets_lng.internet'),
            __('packets_lng.type'),
            __('packets_lng.price'),
            __('packets_lng.is_global'),
            __('packets_lng.is_teens')
        ];

        return view('packets', ['packets' => $packets,
                                'cols' => $cols,
                                'operators' => $operators,
                                'types' => $types,
                                'is_global' => $is_global,
                                'is_teens' => $is_teens]);
    }

    public function index_packet_users($packet_id){
        View::share('page_js', 'packet_users');
        $packet_users = $this->user_packet_model->get_packet_users_table($packet_id);

        $cols = [
            'id',
            'user_id',
            __('packets_lng.user'),
            __('packets_lng.packet_price'),
            __('packets_lng.admin_price'),
            __('packets_lng.is_available')
        ];

        $is_available_select = ['1'=>__('main_lng.no'), '2'=>__('main_lng.yes')];
        $extra_columns = [
            [
                'type'  =>  'checkbox',
                'title' =>  __('packets_lng.select'),
                'text'  =>  '-',
                'class' =>  'checked-row'
            ]
        ];
        $packet_name =  Packet::where('id', $packet_id)->get()[0]['name'];

        return view('packet_users', [
                                        'packet_users'          => $packet_users,
                                        'cols'                  => $cols,
                                        'is_available_select'   => $is_available_select,
                                        'extra_columns'         => $extra_columns,
                                        'packet_name'           => $packet_name
                                    ]);
    }

    public function index_regular_packets(){
        View::share('page_js', 'regular_packets');
        $regular_packets = $this->packet_model->get_regular_packets_table(Auth::user()->id);

        $cols = [
            'id',
            __('packets_lng.packet_name'),
            __('packets_lng.purchasing_price'),
            __('packets_lng.selling_price'),
            'packet_id',
        ];

        $extra_columns = [
            [
                'type'  =>  'checkbox',
                'title' =>  __('packets_lng.select'),
                'text'  =>  '-',
                'class' =>  'checked-row'
            ]
        ];

        return view('regular_packets', [
                                        'regular_packets'   => $regular_packets,
                                        'cols'              => $cols,
                                        'extra_columns'     => $extra_columns,
                                    ]);
    }


    //------------------------------------------ Actions --------------------------------------------//
    public function store(Request $request){
        $this->is_validate($request);

        $id = $request->input('id');

        $data = $request->all();
        unset($data['id'], $data['_token']);
        $data['is_global'] -= 1; 
        $data['is_teens'] -= 1;

        if($id){
            unset($data['is_available_for_all']);
            $packet = Packet::find($id);
            $packet->fill($data);
            $packet->save();
        }else{
            $newPacket = Packet::create($data);

            $is_available_for_all = $request->input('is_available_for_all');
            $is_available_for_all = $is_available_for_all?true:false;
            $this->creat_new_group_packets($newPacket->id, $newPacket->price);
            $this->creat_new_user_packets($newPacket->id, $is_available_for_all, $newPacket->price);
        }

        return redirect("/packets")->with('success', __('main_lng.done_successfully'));
    }

    public function store_packet_users(Request $request){
        $ids = $request->input('ids');
        $ids = substr($ids, 1);
        $idsArr = explode('_', $ids);

        $admin_price = $request->input('admin_price');
        $is_available = $request->input('is_available');
        $data = [];
        if(isset($admin_price))
            $data['admin_price'] = $request->input('admin_price');
        if(isset($is_available)){
            $data['is_available'] = $request->input('is_available');
            $data['is_available'] -= 1; 
            
            $this->set_is_available_for_children_of_agent($idsArr, $data['is_available']);
        }
        
        $user_packet = '';
        foreach ($idsArr as $id) {
            $user_packet = User_Packet::find($id);
            $user_packet->fill($data);
            $user_packet->save();
        }
        $packet_id = $user_packet->packet_id;
        
        return redirect("/packet_users/$packet_id")->with('success', __('main_lng.done_successfully'));
    }

    public function store_regular_packets(Request $request){
        $this->is_regular_validate($request);

        $ids = $request->input('ids');
        $ids = substr($ids, 1);
        $idsArr = explode('_', $ids);

        $user_price = $request->input('user_price');
        $data['user_price'] = $request->input('user_price');
            
        $user_packet= '';
        foreach ($idsArr as $id) {
            $user_packet = User_Packet::find($id);
            $user_packet->fill($data);
            $user_packet->save();
        }
        
        return redirect("/regular_packets")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($id){
        $packet = Packet::find($id);
        $packet->delete();
        return redirect("/packets")->with('success',  __('main_lng.done_successfully'));
    }

    public function get_types_by_operator(Request $request){
        $operator = $request->operator;
        $select_types = $this->get_types_for_select($operator);
        return response()->json($select_types);
    }

    //------------------------------------------ Functions --------------------------------------------//
    public function is_validate($request){
        $rules = array(
            'operator'  =>'required',
            'type'      =>'required',
            'is_global' =>'required',
            'is_teens'  =>'required',
            'price'     =>'required',
            'api_id'     =>'required|unique:packets',
        );
        $this->validate($request ,$rules);
    }

    public function is_regular_validate($request){
        $rules = array(
            'user_price'  =>'required',
        );
        $this->validate($request ,$rules);
    }

    // Get packet by id
    public function get_packet(Request $request){
        $id = $request->id;
        $packet = Packet::where('id', $id)->get()[0];
        $select_types = $this->get_types_for_select($packet['operator']);

        $response['packet'] = $packet;
        $response['select_types'] = $select_types;
        return response()->json($response);
    }

    private function creat_new_user_packets($packet_id, $is_available_for_all, $packet_operator_price){
        $users = User::where('type', 'agent')->select('id')->get();
        $newUserPacket = [];
        foreach ($users as $user) {
            $newUserPacket['user_id'] = $user->id;
            $newUserPacket['packet_id'] = $packet_id;
            $newUserPacket['is_available'] = $is_available_for_all;
            $newUserPacket['admin_price'] = $packet_operator_price + 3;
            $newUserPacket['user_price'] = $packet_operator_price + 5;
            User_Packet::create($newUserPacket);
        }

        $users = User::where('type', 'regular')->select('id')->get();
        $newUserPacket = [];
        foreach ($users as $user) {
            $newUserPacket['user_id'] = $user->id;
            $newUserPacket['packet_id'] = $packet_id;
            $newUserPacket['is_available'] = $is_available_for_all;
            $newUserPacket['admin_price'] = $packet_operator_price + 5;
            $newUserPacket['user_price'] = $packet_operator_price + 7;
            User_Packet::create($newUserPacket);
        }
    }

    private function creat_new_group_packets($packet_id, $packet_operator_price){
        $groups = Group::all();
        $newUserPacket = [];
        foreach ($groups as $group) {
            $newUserPacket['group_id'] = $group->id;
            $newUserPacket['packet_id'] = $packet_id;
            $newUserPacket['admin_price'] = $packet_operator_price + 3;
            $newUserPacket['user_price'] = $packet_operator_price + 5;
            User_Packet::create($newUserPacket);
        }
    }

    private function set_is_available_for_children_of_agent($ids, $is_available){
        if(!$is_available){
            foreach ($ids as $id) {
                $user_packet = User_Packet::find($id);
                $user = User::find($user_packet->user_id);
                if($user->type == 'agent'){
                    $children_users = User::where('created_by_user_id', $user->id)->get();
                    foreach ($children_users as $children_user) {
                        $user_packet_for_child_id = User_Packet::where('user_id', $children_user['id'])->where('packet_id', $user_packet->packet_id)->get()[0]['id'];
                        $up = User_Packet::find($user_packet_for_child_id);
                        $up->is_available = $is_available;
                        $up->save();
                    }
                }
            }
        }
    }

    public function get_notes_of_packet(Request $request)
    {
        $packet_id = $request->packet_id;
        $notes = Packet::where('id', $packet_id)->get()[0]['notes'];
        return response()->json($notes); 
    }

    private function  get_types_for_select($operator){
        $types = get_operator_types($operator);

        $select_types = [];
        foreach ($types as $key=>$type)
            $select_types[$key] = __($key);

        return $select_types;
    }
}
