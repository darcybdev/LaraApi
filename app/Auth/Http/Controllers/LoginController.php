<?php

namespace App\Auth\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use JWTAuth;

use App\Base\Http\Controllers\Controller;
use App\Common\Response;
use App\Users\Http\Transformers\UserTransformer;

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

    protected $usernameField = 'email';

    public function login(Request $request)
    {

        try {
            $this->validate($request, [
                'username' => 'required|string',
                'password' => 'required|string'
            ]);
        } catch (\Exception $e) {
            return $this->response->error($e->errors());
        }

        // Needed to check by username or email
        $request->merge([
            'email' => $request->username
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // Attempt by email
        $credentials = $request->only('username', 'password');
        $token = JWTAuth::attempt($credentials);
        if ($token) {
            return $this->sendLoginResponse($request, $token);
        }

        // Attempt by username
        $this->usernameField = 'username';
        $token = JWTAuth::attempt($credentials);
        if ($token) {
            return $this->sendLoginResponse($request, $token);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        try {
            return $this->sendFailedLoginResponse($request);
        } catch (\Exception $e) {
            return $this->response->error($e->errors());
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->response->data(['success' => 'OK']);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request, $token)
    {
        // Successful login, return user object with token
        $user = auth()->user();
        $data = $this->transform($user, new UserTransformer);
        $data['token'] = $token;
        return $this->response->data($data);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return $this->usernameField;
    }
}
