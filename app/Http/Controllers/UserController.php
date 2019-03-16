<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\User;
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
    }
    public function index()
    {
        return view('user_settings');
    }

    public function update(Request $request){
        $this->is_validate($request);
        $this->testPassword($request);
        
        $user = User::find(Auth::user()->id);
        
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->user_name = $request->input('user_name');
        if($this->passValid)
            $user->password = Hash::make($request->input('new_password'));

        $user->save();
 
        return redirect('/')->with('success', __('main_lng.done_successfully'));
    }

    public function is_validate($request){
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

    private function testPassword($request){
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
}
