<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Token
{
    public static function createToken($data)
    {
        // the data contain token and user id
        $data['token'] = Hash::make($data['token']);
        return DB::table('tokens')->insert($data);
    }
    public static function deleteToken($deleteId)
    {
        return DB::table('tokens')->where('id', $deleteId)->delete();
    }
}
