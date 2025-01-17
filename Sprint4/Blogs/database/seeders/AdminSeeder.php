<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class AdminSeeder extends Seeder
{

    public function run(): void
    {
        //we have two admin users
        DB::table('users')->insert([
            'first_name' => Str::random(10),
            'last_name' => Str::random(10),
            'email'     => 'admin@admin.com',
            'password'  => Hash::make('12345678'),
        ]);
        DB::table('users')->insert([
            'first_name' => Str::random(10),
            'last_name' => Str::random(10),
            'email'     => 'admin1@admin.com',
            'password'  => Hash::make('12345678'),
        ]);
    }
}
