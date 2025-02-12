<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// Autenticación
Route::post('register/create', [AuthController::class, 'register'])->name('register.create');
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
Route::post('login', [AuthController::class, 'login']);
Route::post('me', [AuthController::class, 'me']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

Route::post('/refresh', [AuthController::class, 'refreshToken']);

Route::post('/free/registration', [RegistrationController::class, 'free']);
Route::get('/user/registration', [RegistrationController::class, 'getUserEvents']);



// Rutas protegidas con autenticación
Route::post('/speakers/store', [SpeakerController::class, 'store']);
Route::post('/speakers/destroy/{id}', [SpeakerController::class, 'destroy']);
Route::get('/speakers/spakersList', [SpeakerController::class, 'spakersList']);

Route::post('/events/store', [EventController::class, 'store']);
Route::post('/events/destroy/{id}', [EventController::class, 'destroy']);
Route::get('/events/eventsList', [EventController::class, 'eventList']);


