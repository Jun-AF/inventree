<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function login(Request $request)
    {
        $validate = $request->validateWithBag("error", [
            "email" => ["required", "string"],
            "password" => ["required", "string"],
        ]);

        if (
            Auth::attempt([
                "email" => strip_tags($request->email),
                "password" => strip_tags($request->password),
            ])
        ) {
            $this->toastNotification(
                "Success",
                "Welcomeback " . Auth::user()->name
            );
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect($this->redirectTo)->with([
                "condition" => $condition,
                "notif" => $notif,
            ]);
        }
        
        return redirect()->back()->withInput()->withErrors($validate);
    }
}
