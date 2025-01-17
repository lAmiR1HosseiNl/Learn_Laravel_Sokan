<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    public function signup(Request $request)
    {
        //validate Inputs
        $validated = $request->validate([
            'first_name' => 'required|string|min:3|max:255|regex:/^[a-zA-Z ]+$/',
            'last_name'  => 'required|string|min:3|max:255|regex:/^[a-zA-Z ]+$/',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed'
        ]);
        //save data in db customized class not eloquent 
        User::createUser($validated);
        //user creation log
        Log::channel('custom')->info('user with this email: ' . $validated['email'] . ' signup');
        //find data of user
        $userInfo = User::findUser($validated);
        //Token Created 
        $token = password_hash(Str::random(40) . date('Y-m-d H:i:s') . $validated['email'] . uniqid(), PASSWORD_DEFAULT);
        // the data contain token and user id
        $data['user_id'] = $userInfo->id;
        $data['token']   = $token;
        Token::createToken($data);
        //Respone maked
        return response()->json([
            'token' => $token,
        ]);
    }
    public function signin(Request $request)
    {
        //validate inputs 
        $validated = $request->validate([
            'email'     => 'required|string|email',
            'password'  => 'required|min:8'
        ]);
        //Login db customized class not eloquent 
        $userInfo = User::findUser($validated);
        if ($userInfo === false || !(Hash::check($validated['password'], $userInfo->password))) {
            Log::channel('custom')->warning('someone try to loggin in this email: ' . $validated['email'] . ' but failed');
            return response()->json([
                'message' => 'Invalid User email or password'
            ], 401);
        }
        //Token Created
        Log::channel('custom')->info('user with this email: ' . $validated['email'] . ' login');
        $token = password_hash(Str::random(40) . date('Y-m-d H:i:s') . $validated['email'] . uniqid(), PASSWORD_DEFAULT);
        // the data contain token and user id
        $data['user_id'] = $userInfo->id;
        $data['token']   = $token;
        Token::createToken($data);
        //Respone maked
        return response()->json([
            'token' => $token,
        ]);
    }
    public function logout(Request $request)
    {
        Token::deleteToken($request->id);
        $email = User::findUserEmail($request['currentUserId'])->email;
        Log::channel('custom')->info('user logout with this email: ' . $email);
        return response()->json([
            "message" => "logged out"
        ]);
    }
}
