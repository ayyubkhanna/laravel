<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\Editor\Child\ChildCheckup;
use App\Http\Controllers\Editor\Child\ImmunizationController;
use App\Http\Controllers\Editor\Child\WeighingController;
use App\Http\Controllers\Editor\ChildController;
use App\Http\Controllers\Editor\PersonController;
use App\Http\Controllers\Editor\Posyandu;
use App\Http\Controllers\Editor\Pregnant\CheckupController;
use App\Http\Controllers\Editor\Pregnant\PrenetalCheckupController;
use App\Http\Controllers\Editor\PregnantController;
use App\Http\Controllers\Editor\StuntingController;
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
    $user =  $request->user();

    $user->roles;

    return $user;
});

Route::post('login', [AuthController::class, 'login']);
Route::get('unauthorize', [AuthController::class, 'unauthorize'])->name('unauthorize');
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::name('admin')
        ->middleware(['auth:sanctum', 'role:admin'])
        ->group(function () {
            Route::resource('user', UserController::class);
            Route::resource('role', RoleController::class);
            Route::resource('permission', PermissionController::class);
        });
Route::middleware(['auth:sanctum'])->group( function () {
    Route::resource('data-anak', ChildController::class)->only(['store', 'update']);;
    Route::resource('weighing', WeighingController::class)->only(['store', 'update', 'destroy']);;
    Route::resource('immunization', ImmunizationController::class)->only(['store', 'update', 'destroy']);
    Route::resource('stunting', StuntingController::class)->only(['index', 'show', 'destroy']);
    Route::resource('posyandu', Posyandu::class);
    Route::resource('pregnant', PregnantController::class);
    Route::resource('prenetal-checkup', PrenetalCheckupController::class)->only(['store', 'update', 'show', 'destroy']);
    Route::resource('person', PersonController::class);
    Route::get('child-chart', [ChartController::class, 'childChart']);
    Route::get('total-chart', [ChartController::class, 'total']);
});
