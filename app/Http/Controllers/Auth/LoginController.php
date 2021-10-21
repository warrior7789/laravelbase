<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {   
        
        $this->middleware('guest')->except('logout');
    }


    /*protected function redirectTo()
    {           
        if ( Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Sub Admin')) {
            return '/admin'; 
        }        
        return '/';
    }*/

    /**
     * Check user's role and redirect user based on their role
     * @return 
     */
    public function authenticated()
    {
        if(Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Sub Admin'))
        {
            return redirect('/admin');
        } 

        return redirect('/');
    }

}
