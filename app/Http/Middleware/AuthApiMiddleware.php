<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class AuthApiMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = auth('api')->userOrFail();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    'status'    => false,
                    'message'   => 'Token is Invalid'
                ],401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json([
                    'status'    => false,
                    'message'   => 'Token is Expired'
                ],401);
            }else{
                return response()->json([
                    'status'    => false,
                    'message'   => 'Authorization Token not found'
                ],401);
            }
        }
        return $next($request);
    }
}