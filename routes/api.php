<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\FoundAndLostController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\WarningController;

Route::get('/ping', function () {
    return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/validate', [AuthController::class, 'validatetoken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/walls', [WallController::class, 'getAll']);
    Route::post('/wall/{id}/like', [WallController::class, 'like']);

    Route::get('/billets', [BilletController::class, 'getAll']);

    Route::get('/docs', [DocController::class, 'getAll']);

    Route::get('/warnings', [WarningController::class, 'getMyWarnings']);
    Route::post('/warning', [WarningController::class, 'setWarnings']);
    Route::post('/warning/file', [WarningController::class, 'addWarningFile']);

    Route::get('/billets', [DocController::class, 'getAll']);

    Route::get('/foundandlost', [FoundAndLostController::class, 'getAll']);
    Route::post('/foundandlost', [FoundAndLostController::class,  'insert']);
    Route::put('/foundandlost', [FoundAndLostController::class, 'update']);

    Route::get('/unit/{id}', [UnitController::class, 'getInfo']);
    Route::post('/unit/{id}/addperson', [UnitController::class, 'addPerson']);
    Route::post('/unit/{id}/addvehicule', [UnitController::class, 'addVehicule']);
    Route::post('/unit/{id}/addpet', [UnitController::class, 'addPet']);
    Route::post('/unit/{id}/removeperson', [UnitController::class, 'removePerson']);
    Route::post('/unit/{id}/removevehicule', [UnitController::class, 'removeVehicule']);
    Route::post('/unit/{id}/removepet', [UnitController::class, 'removePet']);

    Route::get('/reservations', [ReservationController::class, 'getReservations']);
    Route::get('/myreservations', [ReservationController::class, 'getMyReservations']);

    Route::get('/reservation/{id}/disableddates', [ReservationController::class, 'DisabledDates']);
    Route::get('/reservation/{id}/times', [ReservationController::class, 'getTimes']);

    Route::delete('/myreservation/{id}', [ReservationController::class, 'delReservation']);
    Route::post('/reservation/{id}', [ReservationController::class, 'setReservation']);


});
