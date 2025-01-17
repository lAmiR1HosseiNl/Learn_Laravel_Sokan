<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Like
{
    public static function index($blog_id)
    {
        $userInfo = DB::table('weblogs')->where('id', $blog_id)->first();
        return $userInfo;
    }
    public static function insertLike($currentUserId, $blog_id)
    {
        DB::table('likes')
            ->updateOrInsert(
                [
                    'blog_id' => $blog_id,
                    'user_id' => $currentUserId
                ]
            );
    }
    public static function deletelike($currentUserId, $blog_id)
    {
        $data = DB::table('likes')->where('user_id', $currentUserId)->where('blog_id', $blog_id)->delete();
        return ($data);
    }
    public static function searchLikeUsers($blogToCheck)
    {
        $usersWhoLike = DB::table('likes')
            ->where('blog_id', $blogToCheck)
            ->leftJoin('users', 'users.id', '=', 'likes.user_id')
            ->select(DB::raw("CONCAT(users.first_name , ' ' , users.last_name) AS full_name"), 'users.email')
            ->get();
        return $usersWhoLike;
    }
}
