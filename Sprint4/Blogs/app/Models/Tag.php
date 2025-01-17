<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Tag
{
    public static function createTag($data, $blogid)
    {
        //data is full tags that we break it to one tag
        if (!empty($data['tag'])) {
            //Add tag or new one to table
            $tags = explode(',', $data['tag']);
            for ($i = 0; $i < count($tags); $i++) {
                $data = $tags[$i];
                DB::table('tags')->updateOrInsert([
                    'tag_name' => $data
                ]);
                $findTag = DB::table('tags')
                    ->where('tag_name', $data)
                    ->first();
                DB::table('weblogs_tags')->updateOrInsert([
                    'blog_id' => $blogid->id,
                    'tag_id'  => $findTag->id,
                ]);
            }
        }
    }
    public static function countTags()
    {
        $result = DB::table('tags')
            ->leftJoin('weblogs_tags', 'tags.id', '=', 'weblogs_tags.tag_id')
            ->select('tags.tag_name', DB::raw('COUNT(weblogs_tags.tag_id) AS Count'))
            ->groupBy('tags.tag_name')
            ->get();
        return $result;
    }
}
