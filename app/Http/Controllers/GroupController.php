<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\Group;
use Educators\Packet;
use Educators\User_Packet;
use Educators\User;
use View;
use Auth;

class GroupController extends Controller
{
    public function __construct(){
        $this->group_model = new Group();
        $this->user_model = new User();
        $this->user_packet_model = new User_Packet();
    }

    //------------------------------------------------indexes-----------------------------------------------//
    public function index(){
        View::share('page_js', 'groups');
        $groups = $this->group_model->get_groups_table();

        $groups_cols = [
            'id',
            __('groups_lng.name'),
            __('groups_lng.description'),
        ];

        return view('groups', [
            'groups' => $groups,
            'groups_cols' => $groups_cols,
        ]);
    }

    public function index_group_packets($group_id){
        View::share('page_js', 'group_packets');
        $group_packets = $this->user_packet_model->get_group_packets_table($group_id);

        $cols = [
            'id',
            'packet_id',
            __('groups_lng.packet_name'),
            __('groups_lng.operator'),
            __('groups_lng.packet_type'),
            __('groups_lng.is_global'),
            __('groups_lng.purchasing_price'),
            __('groups_lng.selling_price'),
            __('groups_lng.is_available')
        ];

        $is_available_select = ['1'=>__('main_lng.no'), '2'=>__('main_lng.yes')];
        $extra_columns = [
            [
                'type'  =>  'checkbox',
                'title' =>  __('groups_lng.select'),
                'text'  =>  '-',
                'class' =>  'checked-row'
            ]
        ];
        $groupName =  Group::where('id', $group_id)->get()[0]['name'];

        return view('group_packets', [
            'group_packets'         => $group_packets,
            'cols'                  => $cols,
            'is_available_select'   => $is_available_select,
            'extra_columns'         => $extra_columns,
            'groupName'             => $groupName,
            'group_id'              => $group_id
        ]);
    }

    public function index_group_users($group_id){
        View::share('page_js', 'group_users');
        $users = $this->user_model->get_users_table($group_id);

        $cols = [
            'id',
            'is_active_hidden',
            __('groups_lng.name'),
            __('groups_lng.email'),
            __('groups_lng.mobile'),
            __('groups_lng.type'),
            __('groups_lng.balance'),
            __('groups_lng.credit')
        ];

        $allUsers = User::select("id", 'name')->where('created_by_user_id', Auth::user()->id)->whereNull('group_id')->get();
        foreach ($allUsers as $user)
            $select_users[$user['id']] = $user["name"];

        $groupName =  Group::where('id', $group_id)->get()[0]['name'];

        return view('group_users', [
            'users'         => $users,
            'cols'          => $cols,
            'group_id'      => $group_id,
            'select_users'  => $select_users,
            'groupName'     => $groupName
        ]);
    }

    //------------------------------------------Actions--------------------------------------------//
    public function store(Request $request){
        $this->is_validate($request);

        $group_id = $request->input('id');
        $post_type = $request->input('post_type');

        $newData = $request->all();
        unset($newData['id'], $newData['_token'], $newData['post_type']);
        $newData['created_by_user_id'] = Auth::user()->id;

        if($post_type == 'add'){
            $newGroup = Group::create($newData);
            $this->creat_new_group_packets($newGroup->id);
        }elseif($post_type == 'edit'){
            $group = Group::find($group_id);
            $group->fill($newData);
            $group->save();
        }

        return redirect("/groups")->with('success', __('main_lng.done_successfully'));
    }

    public function store_group_packets(Request $request){
        $ids = $request->input('ids');
        $ids = substr($ids, 1);
        $idsArr = explode('_', $ids);

        $admin_price = $request->input('admin_price');
        $is_available = $request->input('is_available');
        $group_id = $request->input('group_id');
        $data = [];
        if(isset($admin_price))
            $data['admin_price'] = $request->input('admin_price');
        if(isset($is_available)){
            $data['is_available'] = $request->input('is_available');
            $data['is_available'] -= 1;
        }

        $is_update_on_all_users = $request->input('is_update_on_all_users');
        $is_update_on_all_users = $is_update_on_all_users?true:false;

        $group_packet = '';
        $group_users = '';
        if($is_update_on_all_users)
            $group_users = User::select("id")->where('created_by_user_id', Auth::user()->id)->where('group_id', $group_id)->get();
        foreach ($idsArr as $id) {
            $group_packet = User_Packet::find($id);
            $group_packet->fill($data);
            $group_packet->save();

            if($is_update_on_all_users)
                foreach ($group_users as $group_user){
                    $user_packet = User_Packet::where('user_id', $group_user['id'])
                                                ->where('packet_id', $group_packet->packet_id);
                    $user_packet->update([
                                            'admin_price'   => $group_packet->admin_price,
                                            'user_price'    => $group_packet->user_price,
                                            'is_available'  => $group_packet->is_available
                                        ]);
                    $user_packet = $user_packet->get()[0];
                    $this->set_is_available_for_children_of_agent($user_packet['id'], $user_packet['is_available']);
                }
        }

        $group_id = $group_packet->group_id;
        return redirect("/group_packets/$group_id")->with('success', __('main_lng.done_successfully'));
    }

    public function store_group_user(Request $request){
        $this->is_validate_group_user($request);

        $group_id = $request->input('group_id');

        $user_id = $request->input('user_id');

        $user = User::find($user_id);
        $user->group_id = $group_id;
        $user->save();

        $this->update_user_packet($group_id, $user_id);

        return redirect("/group_users/$group_id")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($id){
        $group = Group::find($id);
        $group->delete();
        return redirect("/groups")->with('success',  __('main_lng.done_successfully'));
    }

    public function destroy_group_user($user_id){
        $user = User::find($user_id);
        $group_id = $user->group_id;
        $user->group_id = null;
        $user->save();

        return redirect("/group_users/$group_id")->with('success', __('main_lng.done_successfully'));
    }

    public function synchronize_users(Request $request){
        $group_id = $request->input('group_id');
        $user_ids = User::select('id')->where('group_id', $group_id)->get();
        foreach ($user_ids as $user_id)
            $this->update_user_packet($group_id, $user_id['id']);

        return response()->json();
    }

    //------------------------------------------Functions--------------------------------------------//
    private function is_validate($request){
        $rules = array(
            'name'  =>'required'
        );
        $this->validate($request ,$rules);
    }

    private function is_validate_group_user($request){
        $rules = array(
            'user_id'  =>'required'
        );
        $this->validate($request ,$rules);
    }

    private function creat_new_group_packets($newGroupId){
        $packets = Packet::select('id', 'price')->get();
        $newUserPacket = [];
        foreach ($packets as $packet) {
            $newUserPacket['group_id'] = $newGroupId;
            $newUserPacket['packet_id'] = $packet->id;

            $newUserPacket['is_available'] = true;
            $newUserPacket['admin_price'] = $packet->price + 3;
            $newUserPacket['user_price'] = $packet->price + 5;
            User_Packet::create($newUserPacket);
        }
    }

    // Update user packets based on group packets
    private  function update_user_packet($group_id, $user_id){
        $group_packets = User_Packet::where('group_id', $group_id)->get();
        foreach ($group_packets as $group_packet){
            $user_packet = User_Packet::where('user_id', $user_id)
                                        ->where('packet_id', $group_packet['packet_id']);
            $user_packet->update([
                'admin_price'   => $group_packet['admin_price'],
                'user_price'    => $group_packet['user_price'],
                'is_available'  => $group_packet['is_available']
            ]);
            $user_packet = $user_packet->get()[0];
            $this->set_is_available_for_children_of_agent($user_packet['id'], $user_packet['is_available']);
        }
    }

    private function set_is_available_for_children_of_agent($user_packet_id, $is_available){
        if(!$is_available){
            $user_packet = User_Packet::find($user_packet_id);
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
