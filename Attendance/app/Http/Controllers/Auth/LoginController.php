<?php

namespace attendance\Http\Controllers\Auth;

use attendance\Http\Controllers\Controller;
use attendance\LoginTime;
use attendance\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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

    /**
     * Override the log out method
     */
    public function logout(Request $request)
    {
        $this->updateLoginTime($request);
        //Log out step
        $this->guard()->logout();
        $request->session()->invalidate();

        //Redirect back to the login page
        return redirect('/login');

    }

    /**
     * Update the login time
     * @param $request
     */
    private function updateLoginTime($request)
    {
        if (User::find($request->user()->id)->hasRole('student')) {
            //Save this user logout to true
            $loginTime = LoginTime::where('user_id', '=', $request->user()->id)->first();

            $loginTime->logout = true;
            $loginTime->login_time = Carbon::now();
            $loginTime->timestamps = false;
            $loginTime->save();
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
