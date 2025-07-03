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

Route::post('refresh', [AuthController::class, 'refresh']);
Route::post('unauthorize', [AuthController::class, 'unauthorize'])->name('unauthorize');

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('me', [AuthController::class, 'me']);
});
Route::name('admin')
        ->middleware(['auth:sanctum', 'role:admin'])
        ->group(function () {
            Route::resource('user', UserController::class);
            Route::resource('role', RoleController::class);
            Route::resource('permission', PermissionController::class);
        });
Route::middleware(['auth:api'])->group( function () {
    Route::resource('data-anak', ChildController::class)->only(['store', 'update']);;
    Route::resource('weighing', WeighingController::class)->only(['store', 'update', 'destroy']);;
    Route::resource('immunization', ImmunizationController::class)->only(['store', 'update', 'destroy']);
    Route::resource('stunting', StuntingController::class)->only(['index', 'show', 'destroy']);
    Route::resource('posyandu', Posyandu::class);
    Route::resource('pregnant', PregnantController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('prenetal-checkup', PrenetalCheckupController::class)->only(['store', 'update', 'show', 'destroy']);
    Route::resource('person', PersonController::class);
    Route::get('child-chart', [ChartController::class, 'childChart']);
    Route::get('total-chart', [ChartController::class, 'total']);
});
