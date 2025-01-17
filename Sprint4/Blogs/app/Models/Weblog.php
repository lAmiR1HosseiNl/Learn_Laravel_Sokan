<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Weblog
{
    public static function createWeblog($data, $currentUserId)
    {
        //data is contain topic and body of blog
        return DB::table('weblogs')->insert(
            [
                'topic' => $data['topic'],
                'body' => $data['body'],
                'user_id' => $currentUserId,
            ]
        );
    }
    public static function findWeblog()
    {
        return (DB::table('weblogs')->orderBy('id', 'desc')->first());
    }
    public static function showAllBlogs()
    {
        $result = DB::table('weblogs')
            ->join('users', 'weblogs.user_id', '=', 'users.id')
            ->leftJoin('likes', 'weblogs.id', '=', 'likes.blog_id')
            ->select('weblogs.id', 'weblogs.topic', 'weblogs.body', DB::raw("CONCAT(users.first_name,' ',users.last_name) AS Author"), DB::raw("COUNT(likes.blog_id) AS likes"))
            ->groupBy('weblogs.id', 'weblogs.topic', 'weblogs.body', 'users.first_name', 'users.last_name')
            ->get();
        return $result;
    }
    public static function findWeblogById($id)
    {
        return (DB::table('weblogs')->where('id', $id)->first());
    }
    public static function deleteBlogById($blogDeleteId)
    {
        return DB::table('weblogs')->where('id', $blogDeleteId)->delete();
    }
    public static function editBlogById($blogEditId, $newTopic, $newBody)
    {
        if (isset($newTopic) && isset($newBody)) {
            return DB::table('weblogs')->where('id', $blogEditId)->update(['topic' => $newTopic, 'body' => $newBody]);
        } elseif (isset($newBody)) {
            return DB::table('weblogs')->where('id', $blogEditId)->update(['body' => $newBody]);
        } elseif (isset($newTopic)) {
            return DB::table('weblogs')->where('id', $blogEditId)->update(['topic' => $newTopic]);
        }
    }
    public static function searchBlogs($state, $validated)
    {
        if ($state === 0) {
            $result = DB::table('weblogs')
                ->select('id', 'topic', 'body', 'user_id')
                ->join('users', 'weblogs.user_id', '=', 'users.id')
                ->leftJoin('likes', 'weblogs.id', '=', 'likes.blog_id')
                ->select('weblogs.id', 'weblogs.topic', 'weblogs.body', DB::raw("CONCAT(users.first_name,' ',users.last_name) AS Author"), DB::raw("COUNT(likes.blog_id) AS likes"))
                ->where('weblogs.topic', 'LIKE', '%' . $validated["fullsearch"] . '%')
                ->orWhere('weblogs.body', 'LIKE', '%' . $validated["fullsearch"] . '%')
                ->orWhere(DB::raw("CONCAT(users.first_name,' ',users.last_name)"), 'LIKE', '%' . $validated["fullsearch"] . '%')
                ->groupBy('weblogs.id', 'weblogs.topic', 'weblogs.body', 'users.first_name', 'users.last_name')
                ->get();
            return $result;
        }
    }
}
