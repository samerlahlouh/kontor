<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\Packet;
use Educators\User;
use Educators\User_Packet;
use View;

class PacketController extends Controller
{
    public function __construct(){
        $this->packet_modal = new Packet();
        $this->user_packet_model = new User_Packet();
    }

    //------------------------------------------------indexes-----------------------------------------------//
    public function index(){
        View::share('page_js', 'packets');
        $packets = $this->packet_modal->get_packets_table(); 

        $operators = $this->getEnumValues('packets', 'operator');
        $types = $this->getEnumValues('packets', 'type');
        $is_global = ['1'=>__('main_lng.private'), '2'=>__('main_lng.global')];
        $is_teens = ['1'=>__('main_lng.no'), '2'=>__('main_lng.yes')];
        $cols = [
            'id',
            __('packets_lng.name'),
            __('packets_lng.operator'),
            __('packets_lng.sms'),
            __('packets_lng.minutes'),
            __('packets_lng.internet'),
            __('packets_lng.type'),
            __('packets_lng.price'),
            __('packets_lng.is_global'),
            __('packets_lng.is_teens'),
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


    //------------------------------------------ Actions --------------------------------------------//
    public function store(Request $request){
        $this->is_validate($request);

        $id = $request->input('id');

        $data = $request->all();
        unset($data['id'], $data['_token']);
        $data['is_global'] -= 1; 
        $data['is_teens'] -= 1;

        if($id){
            $packet = Packet::find($id);
            $packet->fill($data);
            $packet->save();
        }else{
            $newPacket = Packet::create($data);
            $this->creat_new_user_packets($newPacket->id);
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
        
        $user_packet;
        foreach ($idsArr as $id) {
            $user_packet = User_Packet::find($id);
            $user_packet->fill($data);
            $user_packet->save();
        }
        $packet_id = $user_packet->packet_id;
        
        return redirect("/packet_users/$packet_id")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($id){
        $packet = Packet::find($id);
        $packet->delete();
        return redirect("/packets")->with('success',  __('main_lng.done_successfully'));
    }


    //------------------------------------------ Functions --------------------------------------------//
    public function is_validate($request){
        $rules = array(
            'operator'  =>'required',
            'sms'       =>'required',
            'minutes'   =>'required',
            'internet'  =>'required',
            'type'      =>'required',
            'is_global' =>'required',
            'is_teens'  =>'required',
        );
        $this->validate($request ,$rules);
    }

    // Get packet by id
    public function get_packet(Request $request){
        $id = $request->id;
        $packet = Packet::where('id', $id)->get()[0];
        return response()->json($packet); 
    }

    private function creat_new_user_packets($packet_id){
        $users = User::where('type', '<>' , 'admin')->select('id')->get();
        $newUserPacket = [];
        foreach ($users as $user) {
            $newUserPacket['user_id'] = $user->id;
            $newUserPacket['packet_id'] = $packet_id;
            $newUserPacket['is_available'] = false;
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
}