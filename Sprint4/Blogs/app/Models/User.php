<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User
{
    public static function createUser($data)
    {
        $data['password'] = Hash::make($data['password']);
        return DB::table('users')->insert($data);
    }
    public static function findUser($data)
    {
        //data is array contain personal info such as email
        $userInfo = DB::table('users')->where('email' , $data['email'])->first();
        if ($userInfo) {
            return  $userInfo;
        } else {
            return false;
        }
    }
    public static function findUserEmail($currentUserId)
    {
        $email = DB::table('users')
        ->where('id' , $currentUserId)
        ->get()
        ->first()
        ;
        return $email;
    }
}
