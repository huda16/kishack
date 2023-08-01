<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterCategoryController;
use App\Http\Controllers\MasterStoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route Users
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/login', [UserController::class, 'authenticate']);
Route::get('/logout', [UserController::class, 'logout']);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::resource('/users', UserController::class)->except(['show', 'create']);
    Route::resource('/roles', RoleController::class)->except(['show', 'create']);
    Route::resource('/master-categories', MasterCategoryController::class)->except(['show', 'create']);
    Route::get('/master-articles/check-slug', [MasterStoryController::class, 'checkSlug']);
    Route::resource('/master-articles', MasterStoryController::class);
});
