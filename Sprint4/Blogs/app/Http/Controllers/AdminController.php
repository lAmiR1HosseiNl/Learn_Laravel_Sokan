<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Weblog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class AdminController
{
    public function csv(Request $request)
    {
        $email = User::findUserEmail($request['currentUserId'])->email;
        //get Blogs data in json 
        $data = Weblog::showAllBlogs();
        //if no blog exists
        if ($data->isEmpty()) {
            return response()->json([
                'massage' => 'No blogs created'
            ]);
        }
        //create csv out of json
        $csvName = 'Blogs.csv';
        $csvFile = fopen($csvName, 'w');
        $columns = array_keys((array) $data[0]);
        fputcsv($csvFile, $columns);
        foreach ($data as $row) {
            fputcsv($csvFile, (array)$row);
        }
        fclose($csvFile);
        //response that the csv created
        Log::channel('custom')->info('Admin get a csv with this email: ' . $email);
        return Response::download(public_path($csvName))->deleteFileAfterSend(true);
    }
}
