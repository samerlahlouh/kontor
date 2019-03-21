<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\User;
use Educators\User_Packet;
use Educators\Packet;
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
        $users = $this->user_model->get_users_table();
        $typesValues = $this->getEnumValues('users', 'type');

        $types = array();
        foreach ($typesValues as $typeValue) {
            $types[$typeValue] =  __("main_lng.$typeValue");
        }

        $cols = [
            'id',
            'is_active_hidden',
            __('users_lng.name'),
            __('users_lng.email'),
            __('users_lng.mobile'),
            __('users_lng.type'),
            __('users_lng.balance'),
            __('users_lng.credit')
        ];
        return view('users', [  'users' => $users,
                                'cols'  => $cols,
                                'types' => $types
                            ]);
    }

    public function index_user_packets($user_id){
        View::share('page_js', 'user_packets');
        $user_packets = $this->user_packet_model->get_user_packets_table($user_id);
        // dd($user_packets);
        
        $cols = [
            'id',
            'packet_id',
            __('users_lng.packet_name'),
            __('users_lng.packet_price'),
            __('users_lng.selling_price'),
            __('users_lng.is_available')
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
        $user->save();
    }

    public function activate_user(Request $request){
        $user_id = $request->input('user_id');

        $user = User::find($user_id);
        $user->is_active = true;
        $user->save();
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

        $this->creat_new_user_packets($newUser->id);

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
        
        $user_packet;
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

    private function creat_new_user_packets($user_id){
        $packets = Packet::select('id')->get();
        $newUserPacket = [];
        foreach ($packets as $packet) {
            $newUserPacket['user_id'] = $user_id;
            $newUserPacket['packet_id'] = $packet->id;
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
