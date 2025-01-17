<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController
{
    public function show()
    {
        //retrive data from model
        $result = Tag::countTags();
        //show retrive data in response
        return response()->json([
            'Count Tags' => $result
        ]);
    }
}
