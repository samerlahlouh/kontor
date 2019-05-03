<?php

namespace Educators\Http\Controllers\Auth;

use Educators\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Educators\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
//    protected $maxAttempts = 3;
//    protected $decayMinutes = 1;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request) {
        if(is_numeric($request->get('email')))
            return array_merge(['mobile'=>$request->get('email'),'password'=>$request->get('password')], ['is_active' => 1]);
        return array_merge($request->only($this->username(), 'password'), ['is_active' => 1]);
    }

    protected function authenticated(Request $request, User $user){
        $user->pass_error_counter = 0;
        $user->save();

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];

        // Load user from database
        $user = User::where($this->username(), $request->{$this->username()})->first();

        if($user && $user->type != 'admin' && $user->is_active && !\Hash::check($request->password, $user->password)){
            if(++ $user->pass_error_counter == 3){
                $user->pass_error_counter = 0;
                $user->is_active = 0;
            }
            $user->save();
        }
        // Check if user was successfully loaded, that the password matches
        // and active is not 1. If so, override the default error message.
        if ($user && \Hash::check($request->password, $user->password) && $user->active != 1) {
            $errors = [$this->username() => trans('auth.notactivated')];
        }

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }
}
