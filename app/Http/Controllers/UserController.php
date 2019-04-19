<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\User;
use Educators\User_Packet;
use Educators\Packet;
use Educators\Group;
use Auth;
use View;
use Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->passValid = false;
        $this->user_model = new User();
        $this->user_packet_model = new User_Packet();
    }

    //------------------------------------------------indexes-----------------------------------------------//
    public function index_settings(){
        return view('user_settings');
    }

    public function index_users(){
        View::share('page_js', 'users');
        $users = $this->user_model->get_users_table_with_extra_column();
        $typesValues = $this->getEnumValues('users', 'type');
        unset($typesValues['admin']);

        $types = array();
        foreach ($typesValues as $typeValue)
            $types[$typeValue] =  __("main_lng.$typeValue");

        $groups = Group::select("id", 'name')->get();
        $select_groups = [];
        foreach ($groups as $group)
            $select_groups[$group['id']] = $group["name"];

        $cols = [
            'id',
            'is_active_hidden',
            __('users_lng.name'),
            __('users_lng.email'),
            __('users_lng.mobile'),
            __('users_lng.type'),
            __('users_lng.group_name'),
            __('users_lng.balance'),
            __('users_lng.credit'),
            'is_checking_free_hidden',
        ];
        return view('users', ['users'         => $users,
                                    'cols'          => $cols,
                                    'types'         => $types,
                                    'select_groups' => $select_groups
                                    ]);
    }

    public function index_user_packets($user_id){
        View::share('page_js', 'user_packets');
        $user_packets = $this->user_packet_model->get_user_packets_table($user_id);
        
        $cols = [
            'id',
            'packet_id',
            __('regular_packets_lng.packet_name'),
            __('regular_packets_lng.packet_price'),
            __('regular_packets_lng.selling_price'),
            __('regular_packets_lng.is_available')
        ];

        $is_available_select = ['1'=>__('main_lng.no'), '2'=>__('main_lng.yes')];
        $extra_columns = [
            [
                'type'  =>  'checkbox',
                'title' =>  __('users_lng.select'),
                'text'  =>  '-',
                'class' =>  'checked-row'
            ]
        ];
        $userName =  User::where('id', $user_id)->get()[0]['name'];

        return view('user_packets', [
                                        'user_packets'          => $user_packets,
                                        'cols'                  => $cols,
                                        'is_available_select'   => $is_available_select,
                                        'extra_columns'         => $extra_columns,
                                        'userName'              => $userName
                                    ]);
    }

    public function index_change_user_password($user_id){
        $user = User::find($user_id);
        if($user->created_by_user_id != Auth::user()->id)
            return redirect()->back();

        return view('change_user_password', ['user_id'=>$user_id]);
    }

    public function index_all_users(){
        View::share('page_js', 'all_users');
        $users = $this->user_model->get_all_users_table_with_extra_column();
        $cols = [
            'id',
            'is_active_hidden',
            __('users_lng.name'),
            __('users_lng.created_by_user_name'),
            __('users_lng.email'),
            __('users_lng.mobile'),
            __('users_lng.type'),
            __('users_lng.balance'),
            __('users_lng.credit'),
            'is_checking_free_hidden',
        ];
        return view('all_users', ['users'         => $users,
                                        'cols'          => $cols,
        ]);
    }

    //------------------------------------------Actions--------------------------------------------//
    public function update_own_account(Request $request){
        $this->update_validator($request);
        $this->test_update_password($request);
        
        $user = User::find(Auth::user()->id);
        
        $user->name         = $request->input('name');
        $user->email        = $request->input('email');
        $user->user_name    = $request->input('user_name');
        $user->mobile       = $request->input('mobile');
        if($this->passValid)
            $user->password = Hash::make($request->input('new_password'));

        $user->save();
 
        return redirect('/')->with('success', __('main_lng.done_successfully'));
    }

    public function deactivate_user(Request $request){
        $user_id = $request->input('user_id');

        $user = User::find($user_id);
        $user->is_active = false;
        $user->pass_error_counter = 0;
        $user->save();

        return response()->json();
    }

    public function activate_user(Request $request){
        $user_id = $request->input('user_id');

        $user = User::find($user_id);
        $user->is_active = true;
        $user->pass_error_counter = 0;
        $user->save();

        return response()->json();
    }

    public function make_checking_paid(Request $request){
        $user_id = $request->input('user_id');

        $user = User::find($user_id);
        $user->is_checking_free = false;
        $user->save();

        return response()->json();
    }

    public function make_checking_free(Request $request){
        $user_id = $request->input('user_id');

        $user = User::find($user_id);
        $user->is_checking_free = true;
        $user->save();

        return response()->json();
    }

    public function store(Request $request){
        $this->create_validator($request);
        $this->test_create_password($request);

        $data = $request->all();
        unset($data['id'], $data['_token'], $data['confirm_password']);

        if(Auth::user()->type != 'admin')
            $data['type'] = 'regular';

        $data['created_by_user_id'] = Auth::user()->id;
        $data['password'] = Hash::make($data['password']);
        $newUser = User::create($data);

        $group_id = $newUser->group_id;

        $this->creat_new_user_packets($newUser->id, $group_id);

        return redirect("/users")->with('success', __('main_lng.done_successfully'));
    }

    public function store_user_packets(Request $request){
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
        $user_id = $user_packet->user_id;
        
        return redirect("/user_packets/$user_id")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($id){
        $user = User::find($id);
        $user->delete();
        return redirect("/users")->with('success',  __('main_lng.done_successfully'));
    }

    public function synchronize_user(Request $request){
        $user_id = $request->input('user_id');
        $group_id = User::select('group_id')->where('id', $user_id)->get()[0]['group_id'];

        $this->update_user_packet($group_id, $user_id);

        return response()->json();
    }

    public function update_user_password(Request $request){
        $this->update_user_password_validator($request);
        $this->test_update_user_password($request);

        $user_id = $request->input('user_id');
        $user = User::find($user_id);

        $user->password = Hash::make($request->input('new_password'));

        $user->save();

        return redirect("/change_user_password/$user_id")->with('success', __('main_lng.done_successfully'));
    }


    //------------------------------------------Functions--------------------------------------------//
    public function update_validator($request){
        $id = Auth::user()->id;
        $old_password       = $request->input('old_password');
        $new_password       = $request->input('new_password');
        $confirm_password   = $request->input('confirm_password');
        
        $rules = array(
            'name'              =>'required',
            'user_name'         =>"required|unique:users,user_name,$id",
            'email'             =>"required|email|unique:users,email,$id"
        );

        if($old_password!='' || $new_password!='' || $confirm_password!=''){
            $this->passValid = true;
            $rules['old_password'] = 'required';
            $rules['new_password'] = 'required';
            $rules['confirm_password'] = 'required';
        }
        $this->validate($request ,$rules);
    }

    public function update_user_password_validator($request){
        $rules = array(
            'new_password'              =>'required',
            'confirm_password'         =>"required",
        );

        $this->validate($request ,$rules);
    }

    private function test_update_password($request){
        $old_password       = $request->input('old_password');
        $new_password       = $request->input('new_password');
        $confirm_password   = $request->input('confirm_password');

        $isOldSameOfCurrent = password_verify($old_password,  Auth::user()->password);
        $isNewMatchOfOld = password_verify($new_password,  Auth::user()->password);
        $isNewMatchOfConfirm = $new_password == $confirm_password;

        $validator = Validator::make([], []);
        if ($this->passValid && !$isOldSameOfCurrent) {
            $validator->errors()->add('password', __('user_settings_lng.old_password_errror'));
            throw new ValidationException($validator);
        }
        if ($this->passValid && $isNewMatchOfOld) {
            $validator->errors()->add('password', __('user_settings_lng.old_password_matching_errror'));
            throw new ValidationException($validator);
        }
        if ($this->passValid && !$isNewMatchOfConfirm) {
            $validator->errors()->add('password', __('user_settings_lng.confirm_password_errror'));
            throw new ValidationException($validator);
        }
        
    }

    private function test_update_user_password($request){
        $new_password       = $request->input('new_password');
        $confirm_password   = $request->input('confirm_password');

        $isNewMatchOfConfirm = $new_password == $confirm_password;

        $validator = Validator::make([], []);
        if (!$isNewMatchOfConfirm) {
            $validator->errors()->add('password', __('change_user_password_lng.confirm_password_error'));
            throw new ValidationException($validator);
        }

    }

    protected function create_validator($request){
         $validateData = [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'user_name' => 'required|string|max:255|unique:users',
            'password'  => 'required|string|min:6'
        ];
        if(Auth::user()->type == 'admin')
            $validateData['type'] = 'required';
        $this->validate($request, $validateData);
    }

    private function test_create_password($request){
        $new_password       = $request->input('password');
        $confirm_password   = $request->input('confirm_password');

        $isNewMatchOfConfirm = $new_password == $confirm_password;

        $validator = Validator::make([], []);
        if (!$isNewMatchOfConfirm) {
            $validator->errors()->add('password', __('user_settings_lng.confirm_password_errror'));
            throw new ValidationException($validator);
        }
        
    }

    private function creat_new_user_packets($user_id, $group_id=''){
        $current_user = Auth::user();
        $packets = Packet::select('id', 'price')->get();
        $newUserPacket = [];
        foreach ($packets as $packet) {
            $newUserPacket['user_id'] = $user_id;
            $newUserPacket['packet_id'] = $packet->id;

            // Test if current user if agent or admin
            if($current_user->type == 'agent'){
                $current_user_packet = User_Packet::select('admin_price', 'is_available')->where('user_id', $current_user->id)->where('packet_id', $packet->id)->get()[0];
                $newUserPacket['is_available'] = $current_user_packet['is_available'];
                $newUserPacket['admin_price'] = $current_user_packet['admin_price'] + 3;
                $newUserPacket['user_price'] = $current_user_packet['admin_price'] + 5;
            }else{
                $new_is_available = true;
                $new_admin_price  = $packet->price + 3;
                $new_user_price   = $packet->price + 5;
                if($group_id){
                    $group_packet = User_Packet::select('admin_price', 'user_price', 'is_available')->where('group_id', $group_id)->where('packet_id', $packet->id)->get()[0];
                    $new_is_available = $group_packet['is_available'];
                    $new_admin_price  = $group_packet['admin_price'];
                    $new_user_price   = $group_packet['user_price'];
                }

                $newUserPacket['is_available'] = $new_is_available;
                $newUserPacket['admin_price'] =  $new_admin_price;
                $newUserPacket['user_price'] =   $new_user_price;
            }
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
}
