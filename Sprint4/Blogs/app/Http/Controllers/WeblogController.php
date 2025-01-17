<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use App\Models\Weblog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeblogController
{
    public function create(Request $request)
    {
        $email = User::findUserEmail($request['currentUserId'])->email;
        //validate inputs 
        $validted = $request->validate([
            'topic' => 'required|string|max:255|regex:/^[a-zA-Z ]+$/',
            'body'  => 'required|max:10000',
            'tag'   => 'regex:/^(?:[a-zA-Z]+(?:,[a-zA-Z]+)*)?$/',
        ]);
        //save topic and body in weblogs table
        $currentUserId = $request['currentUserId'];
        Weblog::createWeblog($validted, $currentUserId);
        //get the blog id that we created
        $blog_id = Weblog::findWeblog();
        //save tag in tags table we add tag id in tags
        Tag::createTag($validted, $blog_id);
        //save link between tags and weblogs in pivot table
        Log::channel('custom')->info('user create a blog with this email: ' . $email);
        return response()->json([
            "message" => "Weblog created"
        ]);
    }
    public function show()
    {
        //data is result of show
        $data = Weblog::showAllBlogs();
        return response()->json([
            "Weblogs" => $data,
        ]);
    }
    public function destroy($id, Request $request)
    {
        $email = User::findUserEmail($request['currentUserId'])->email;
        $blogDeleteId = $id;
        $currentUserId = $request['currentUserId'];
        $blog = Weblog::findWeblogById($blogDeleteId);
        //Blog id not exists
        if (empty($blog)) {
            Log::channel('custom')->warning('user try to delete the blog but faild because the blog was not exists with this email: ' . $email);
            return response()->json([
                'message' => 'Blog Not Exists For Delete'
            ], 400);
        }
        $user_id = $blog->user_id;
        //Blog id not yours
        if ($user_id !== $currentUserId) {
            Log::channel('custom')->warning('user try to delete the blog but faild because the blog was not his/her with this email: ' . $email);
            return response()->json([
                'massage' => 'you are not allowed to delete this user blog'
            ], 401);
        }
        //Blog id is yours
        if ($user_id === $currentUserId) {
            Weblog::deleteBlogById($blogDeleteId);
            Log::channel('custom')->info('user delete a blog with this email: ' . $email);
            return response()->json([
                'message' => 'blog delete successfuly'
            ]);
        }
    }
    public function update($id, Request $request)
    {
        $email = User::findUserEmail($request['currentUserId'])->email;
        $blogEditId = $id;
        $currentUserId = $request['currentUserId'];
        $blog = Weblog::findWeblogById($blogEditId);
        //get new topic and body

        //validate new topic or body 3 state
        if (isset($request['topic']) && isset($request['body'])) {
            $validated = $request->validate([
                'topic' => 'required|string|max:255|regex:/^[a-zA-Z ]+$/',
                'body'  => 'required|max:10000',
            ]);
        } elseif (isset($request['body'])) {
            $validated = $request->validate([
                'body'  => 'max:10000',
            ]);
            $validated['topic'] = null;
        } elseif (isset($request['topic'])) {
            $validated = $request->validate([
                'topic' => 'string|max:255|regex:/^[a-zA-Z ]+$/',
            ]);
            $validated['body'] = null;
        } else {
            $validated = $request->validate([]);
            $validated['body'] = null;
            $validated['topic'] = null;
        }

        //Blog id not exists
        if (empty($blog)) {
            Log::channel('custom')->warning('user try to edit the blog but faild because the blog was not exists with this email: ' . $email);
            return response()->json([
                'message' => 'Blog Not Exists For Edit'
            ], 400);
        }
        $user_id = $blog->user_id;
        //Blog id not yours
        if ($user_id !== $currentUserId) {
            Log::channel('custom')->warning('user try to edit the blog but faild because the blog was not his/her with this email: ' . $email);
            return response()->json([
                'massage' => 'you are not allowed to Edit this user blog'
            ], 401);
        }
        //Blog id is yours
        if ($user_id === $currentUserId) {
            Weblog::editBlogById($blogEditId, $validated['topic'], $validated['body']);
            Log::channel('custom')->info('user edit a blog with this email: ' . $email);
            return response()->json([
                'message' => 'blog edited successfuly'
            ]);
        }
    }
    public function search(Request $request)
    {
        //get topic , body , author from user if exists i set 4 state(1-search on all 2-search on topic 3-search on body 4- search on auth)
        //in this project i implement only search on all parts
        if (isset($request['fullsearch'])) {
            //validate inputs
            $validated = $request->validate([
                'fullsearch' => 'string|required',
            ]);
            //send data to model
            $state = 0;
            $data = Weblog::searchBlogs($state, $validated);
            //response the data
            return response()->json([
                "Weblogs" => $data,
            ]);
            //states that i want complete but the first one was enough
        } elseif (isset($request['topic']) && isset($request['body']) && isset($request['author'])) {
            return response()->json([
                'massage' => 'currently we dont search on three items',
            ], 400);
        } elseif ((isset($request['topic']) && isset($request['body'])) || (isset($request['topic']) && isset($request['author'])) || (isset($request['author']) && isset($request['body']))) {
            return response()->json([
                'massage' => 'currently we dont search on two items',
            ], 400);
        } elseif (isset($request['topic'])) {
            return response()->json([
                'massage' => 'currently we dont search on one items',
            ], 400);
        } elseif (isset($request['body'])) {
            return response()->json([
                'massage' => 'currently we dont search on one items',
            ], 400);
        } elseif (isset($request['author'])) {
            return response()->json([
                'massage' => 'currently we dont search on one items',
            ], 400);
        } else {
            //data is result we want show if there isnt any search item => normal show
            $data = Weblog::showAllBlogs();
            return response()->json([
                "Weblogs" => $data,
            ]);
        }
    }
}
