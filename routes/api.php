<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Editor\ChildController;
use App\Http\Controllers\Editor\Posyandu;
use App\Http\Controllers\Editor\PregnantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user/login', function (Request $request) {
    return $request->user();
});

Route::get('login', function() {
    return 'Unauthorized';
})->name('login');

Route::post('login', [AuthController::class, 'login']);

Route::name('admin')
        ->middleware(['auth:sanctum', 'role:admin'])
        ->group(function () {
            Route::resource('user', UserController::class);
            Route::resource('role', RoleController::class);
            Route::resource('permission', PermissionController::class);
        });
Route::middleware(['auth:sanctum'])->group( function () {
    Route::resource('data-anak', ChildController::class);
    Route::resource('posyandu', Posyandu::class);
    Route::resource('pregnant', PregnantController::class);
});
