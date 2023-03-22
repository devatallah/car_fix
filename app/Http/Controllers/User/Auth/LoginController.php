<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
    protected $redirectTo = '/user';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:web')->except('logout');
    }

    public function showLoginForm()
    {
        return view('portals.user.auth.login');
    }
    protected function validator(array $data)
    {
        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        return Validator::make($data, $rules);
    }

    public function login(Request $request)
    {
        $this->validator($request->all())->validate();


        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $macAddr = exec('getmac');
        $mac = explode(' ',$macAddr);
        $user = User::where('email', $request->email)->first();
        if ($user->mac_address != null && $user->mac_address != $mac[0]) {
            return back()->with('faild', 'Your device does not exist on our system, try another one.');
        } else {
            $user->update(['mac_address' => $mac[0]]);
        }


        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        auth()->guard('web')->logout();

        $request->session()->invalidate();

        return redirect('/user');
    }
    protected function attemptLogin(Request $request)
    {
        return auth()->guard('web')->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }
}
