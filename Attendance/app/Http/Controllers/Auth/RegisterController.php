<?php

namespace attendance\Http\Controllers\Auth;

use attendance\EmailRequestModule;
use attendance\User;
use attendance\Role;
use attendance\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \attendance\User
     */
    protected function create(array $data)
    {
         $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        //Set default when register an account, it will always be student role
        $user
        ->roles()
        ->attach(Role::where("name", "student")->first());

        //Check if the user has any modules that need to be added
        $emailRequestModule = EmailRequestModule::where('email','=',$user->email)->get();
        if($emailRequestModule != null && sizeof($emailRequestModule) > 0){
            foreach($emailRequestModule as $module){
                //Attach the module ID
                $user->modules()->attach($module->module_id);
            }
        }

        return $user;
    }
}
