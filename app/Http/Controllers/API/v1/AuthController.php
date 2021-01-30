<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\v1\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'email'     => 'required',
                'password'  => 'required'
            ]
        );

        if ($validator->fails()) {
            return $this->error_response($validator);
        }

        $user = User::where('email',$request->email)->first();
        if(!$user){
            $this->code = 404;
            $this->response->status = false;
            $this->response->message = __('auth.user_not_found');
            return $this->response_api();
        } else if ($user && Hash::check($request->password, $user->password) && $token = auth('api')->fromUser($user)) {
            if($user->jwt_token){
                try {
                    JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($user->jwt_token), $forceForever = false);
                } catch(\Exception $e){
                    // Possible caught for TokenExpiredException;
                }
            }
            auth('api')->login($user);

            $user->jwt_token = $token;
            $user->save();

            $this->response->message = __('auth.success');
            return $this->respondWithToken($token);
        }

        $this->code = 401;
        $this->response->status = false;
        $this->response->message = __('auth.failed');

        return $this->response_api();
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name'      => 'required',
                'email'     => 'required|unique:users,email',
                'password'  => 'required|confirmed',
                'password_confirmation'  => 'required',
            ]
        );

        if ($validator->fails()) {
            return $this->error_response($validator);
        }

        DB::beginTransaction();
        $user = User::create([
            'name'  => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        DB::commit();
    
        if($token = auth('api')->fromUser($user)){
            auth('api')->login($user);
    
            $user->jwt_token = $token;
            $user->save();
            
            $this->response->message = __('auth.success_register');
            $expired = auth('api')->payload();
            $this->response->data = [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $expired('exp'),
                'user' => user()->toArray()
            ];
    
            return response()->json($this->response);
        }
        $this->code = 400;
        $this->response->status = false;
        $this->response->message = __('auth.failed_register');

        return $this->response_api();
    }

    private function respondWithToken($token)
    {
        $expired = auth('api')->payload();
        $this->response->data = [
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => $expired('exp'),
            'user' => auth('api')->user()->toArray()
        ];

        return response()->json($this->response);
    }

    public function me()
    {
        $user = auth('api')->user();
        return response()->json([
            'user'=> $user->toArray()
        ]);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();
        $user = auth('api')->user();
        $user->jwt_token = $token;
        $user->save();

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        $user = auth('api')->user();
        $user->save();

        auth('api')->logout();

        $this->response = [
            'success' => true,
            'message' => 'Berhasil Logout',
        ];

        return response()->json($this->response);
    }
}