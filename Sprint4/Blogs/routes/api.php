<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\WeblogController;
use Illuminate\Support\Facades\Route;

////Auth\\\\
//register
Route::post('/register',[UserAuthController::class,'signup']);
//Login
Route::post('/login',[UserAuthController::class,'signin']);
//logout
Route::post('/logout',[UserAuthController::class , 'logout'])->middleware('auth.token');
//Make prefix 
Route::prefix('/weblog')->group(function (){
////Weblog\\\\
//weblog creation
Route::post('/' , [WeblogController::class,'create'])->middleware('auth.token');
//show all weblogs
Route::get('/',[WeblogController::class,'show']);
//delete Blog
Route::delete('/{id}' , [WeblogController::class , 'destroy'])->middleware('auth.token');
//edit Blog
Route::patch('/{id}',[WeblogController::class,'update'])->middleware('auth.token');
////Search\\\\
Route::get('/search' , [WeblogController::class , 'search']);
////Like\\\\
//add like
Route::post('/like/{id}',[LikeController::class,'addLike'])->middleware('auth.token');
//remove like
Route::delete('/like/{id}',[LikeController::class,'removeLike'])->middleware('auth.token');
//who like specefic blog
Route::get('/like/{id}',[LikeController::class , 'whoLike'])->middleware('auth.token');
////Tags\\\\
//find count of all tags
Route::get('/tag',[TagController::class , 'show']);
////Admin\\\\
//for only admin acess we check admin email for verify the real admin
Route::get('/csv',[AdminController::class , 'csv'])->middleware('auth.admin');
});
