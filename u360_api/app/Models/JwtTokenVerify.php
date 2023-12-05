<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JwtTokenVerify extends Model
{
    use HasFactory;
    
    /**
     * tokenVerify : verify token before every request
     *
     * @param  mixed $user
     * @return void
     */
    public function tokenVerify($user)
    {
        try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}
		} catch (TokenExpiredException $e) {
			return response()->json(['token expired']);
		} catch (TokenInvalidException $e) {
			return response()->json(['token invalid']);
		} catch (JWTException $e) {
			return response()->json(['token absent']);
		}

        return false;
    }
}
