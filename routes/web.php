<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login',function(){
    return redirect('/');
});
Route::get('/',[UserController::class,'loadLogin']);
Route::post('/login',[UserController::class,'userLogin'])->name('userLogin');

Route::get('/register',[UserController::class,'loadRegister']);
Route::post('/register',[UserController::class,'userRegister'])->name('userRegister');

Route::get('/logout',[UserController::class,'logout']);

Route::get('/home',[UserController::class,'home']);
Route::post('/home',[ReportController::class,'addReport'])->name('addReport');