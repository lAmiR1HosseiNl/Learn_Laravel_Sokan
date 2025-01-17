<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthAdminTokenMiddleware
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
        $email = User::findUserEmail($currentUserId)->email;
        $currentUserId = $currentUserId;
        $findTheEmailOfUser = User::findUserEmail($currentUserId)->email;
        $listOfAdminsEmails = ['admin1@admin.com' , 'admin@admin.com'];
        //Check if user is admin or not
        if(!in_array($findTheEmailOfUser , $listOfAdminsEmails))
        {
            Log::channel('custom')->warning('user try to get csv of blog but failed because he isnt admin with this email: '. $email );
            return response()->json([
                'massage' => 'You are not admin'
            ],403);
        }
        $request->merge(['id' => $currentId , 'currentUserId' => $currentUserId]);
        return $next($request);
    }
}
