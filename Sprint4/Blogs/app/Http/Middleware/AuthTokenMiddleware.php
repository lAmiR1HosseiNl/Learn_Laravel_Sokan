<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        //token from Auth
        $token = $request->bearerToken();
        if(!$token)
        {
            return response()->json(['message'=>'Auth Failed No Token For Private Page'],401);
        }
        //get all tokens to search for my current token 
        $tokens = DB::table('tokens')->get();
        $currentToken = null;
        $currentUserId = null ;
        $currentUserId = null ;
        $currentExpireTime = null;
        foreach($tokens as $tokenrow)
        {
            if(Hash::check($token , $tokenrow->token))
            {
                $currentToken = $tokenrow->token;
                $currentId = $tokenrow->id;
                $currentUserId = $tokenrow->user_id;
                $currentExpireTime = $tokenrow->expire_at;
                break;
            }
        }
        //check if token is expired or not
        $d1 = new DateTime($currentExpireTime);
        $d2 = new DateTime(date('Y-m-d H:i:s'));
        if($d2 > $d1)
        {
            DB::table('tokens')
            ->where('id' , $currentId)
            ->delete()
            ;
            return response()->json(['message'=>'Your Token is Expired'],401);

        }
        // validation of token
        if(!isset($currentToken))
        {
            return response()->json(['message'=>'Invalid Token'],401);
        }
        $request->merge(['id' => $currentId , 'currentUserId' => $currentUserId]);
        return $next($request);
    }
}
