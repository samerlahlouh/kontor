<?php

namespace Educators\Http\Controllers\Auth;

use Educators\User;
use Educators\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Educators\Http\Middleware\AdminAndAgent;
use Educators\Packet;
use Educators\User_Packet;
use Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(AdminAndAgent::class);
    }

        /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $types = $this->getEnumValues('users', 'type');
        return view('auth.register', ['types'=>$types]);
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
         $validateData = [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'user_name' => 'required|string|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed'
        ];
        if(isset($data['type']))
            $validateData['type'] = 'required';
        return Validator::make($data, $validateData);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \Educators\User
     */
    protected function create(array $data)
    {
        if(!isset($data['type']))
            $data['type'] = 'regular';

        $newUser = User::create([
                        'created_by_user_id' => Auth::user()->id,
                        'name'      => $data['name'],
                        'email'     => $data['email'],
                        'user_name' => $data['user_name'],
                        'mobile'    => $data['mobile'],
                        'password'  => Hash::make($data['password']),
                        'type'      => $data['type']
                    ]);
        $this->creat_new_user_packets($newUser->id);
        return $newUser;
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
}
