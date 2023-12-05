<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware extends BaseMiddleware
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
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                $message = config('message.auth_message.invalid_token');
                return response()->json(apiResponse(401, false, $message, null), 401);
            }
            if ($request->bearerToken() != $user->api_token) {
                $message = config('message.auth_message.invalid_token');
                return response()->json(apiResponse(401, false, $message, null), 401);
            }
        } catch (TokenExpiredException $e) {
            $message = config('message.auth_message.token_expired');
            return response()->json(apiResponse(401, false, $message, null), 401);
        } catch (TokenInvalidException $e) {
            $message = config('message.auth_message.invalid_token');
            return response()->json(apiResponse(401, false, $message, null), 401);
        } catch (JWTException $e) {
            $message = config('message.auth_message.token_not_found');
            return response()->json(apiResponse(401, false, $message, null), 401);
        }
        return $next($request);
    }
}
