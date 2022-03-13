<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Models\OauthAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    use ApiResponse;
    
    public function index()
    {
        return "please set the request headers";
    }
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken');

        //add clients ip_adress and browser info to tokens table
        $token = OauthAccessToken::findOrfail($accessToken->token->id);
        $browser_info = getBrowser();
        $token->update([
            'ip_address'=>$request->getClientIp(),
            'browser_info'=>$browser_info
        ]);

        return response([ 'user' => (new AuthResource(auth()->user()) ), 'access_token' => $accessToken->accessToken],201);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return $this->error(null,'Invalid Credentials',Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', $request->email)->first();
        $accessToken = $user->createToken('authToken');

        //add clients ip_adress and browser info to tokens table
        $token = OauthAccessToken::findOrfail($accessToken->token->id);
        $browser_info = getBrowser();
        $token->update([
            'ip_address'=>$request->getClientIp(),
            'browser_info'=>$browser_info
        ]);

        //update user table
        $user->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);
        
        return response()->json([
            'message'=>'login successfull',
            'user'=>(new AuthResource(auth()->user()) ),
            'token'=>$accessToken->accessToken,
            'code'=>Response::HTTP_OK
        ],200);

    }

    /**
     * Logout the user.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        Session::flush();
        return $this->success(null, 'log out successful', 200);
    }
}
