<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\User;
use App\Models\Weblog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LikeController
{
    public function addLike($id, Request $request)
    {
        $email = User::findUserEmail($request['currentUserId'])->email;
        //get my curent user id and id of the blog i want to like
        $currentUserId = $request['currentUserId'];
        $blogToLike = $id;
        //check if blog is available or not
        $existsBlogToLike = Like::index($blogToLike);
        if (empty($existsBlogToLike)) {
            Log::channel('custom')->warning('user try to like the blog but faild because the blog was not exists with this email: ' . $email);
            return response()->json([
                'massage' => 'The Blog you are trying to like not exists'
            ], 400);
        }
        //like the blog
        //we have two option i choose second one
        //1-look in like table if there is exist a like so we dont repeat the like action
        //2-we like and if there is like there already we ignore it
        Like::insertLike($currentUserId, $blogToLike);
        //response show we like
        Log::channel('custom')->info('user like a blog with this email: ' . $email  . ' liked blog_id: ' . $blogToLike);
        return response()->json([
            'massage' => 'your like suceccfully add'
        ], 200);
    }
    public function removeLike($id, Request $request)
    {
        $email = User::findUserEmail($request['currentUserId'])->email;
        //get my curent user id and id of the blog i want to like
        $currentUserId = $request['currentUserId'];
        $blogToRemoveLike = $id;
        //check if blog is available or not
        $existsBlogToLike = Like::index($blogToRemoveLike);
        if (empty($existsBlogToLike)) {
            Log::channel('custom')->warning('user try to remove like the blog but faild because the blog was not exists with this email: ' . $email);
            return response()->json([
                'massage' => 'The Blog you are trying to like not exists'
            ], 400);
        }
        //delete
        $deleteBlog = Like::deleteLike($currentUserId, $blogToRemoveLike);
        //response if not exist the like
        if (empty($deleteBlog)) {
            Log::channel('custom')->warning('user try to remove like the blog but faild because the user not liked with this email: ' . $email);
            return response()->json([
                'massage' => 'You are not like the current blog so you cant remove nothing'
            ], 400);
        }
        //respone  if we remove like sucess
        if ($deleteBlog === 1) { {
                Log::channel('custom')->info('user remove like a blog with this email: ' . $email  . ' removed liked blog_id: ' . $blogToRemoveLike);
                return response()->json([
                    'massage' => 'You remove like sucessfully'
                ], 200);
            }
        }
    }
    public function whoLike($id)
    {
        $blogToCheck = $id;
        //Blog id not exists
        $blog = Weblog::findWeblogById($blogToCheck);
        if (empty($blog)) {
            return response()->json([
                'message' => 'Blog Not Exists For see who like it'
            ]);
        }
        //now get the blogs info for who liked
        $result = Like::searchLikeUsers($blogToCheck);
        //return the result
        return response()->json([
            'users who like this blog' => $result
        ], 200);
    }
}
