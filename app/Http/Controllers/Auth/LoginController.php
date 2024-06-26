<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Agent;
use stdClass;


class LoginController extends BaseController
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
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 401);
        }
        $user = User::with(['roles'])->where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;

                $agent = Agent::find($user->id);

                $agentInfo = new stdClass;
                $agentInfo->station = $agent->station();

                $response = [
                    'token' => $token,
                    'user' => $user,
                    'agentInfo' => $agentInfo
                ];

                //in the future use Events ... but now .. i'm under pressure ..
                $agent = Agent::find($user->id);
                if (!$agent){
                    $response = ["message" =>'User does not exist'];
                    return response($response, 401);
                }

                //if user is agent, set login timestamp
                $roles = $user->roles;

                if ($roles && $roles[0]->name === "agent") {
                    $agent->setLoginTimeStamp();
                }

                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 401);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 401);
        }
    }

    public function logout (Request $request) {
        $user = Auth::guard('api')->user();

        $agent = Agent::find($user->id);

        $agent->setLogoutTimestamp();

        $token = $user->token();

        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
