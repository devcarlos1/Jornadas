<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

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
Route::get('/', function () {
    return view('auth.register'); // Cambia según la ubicación del archivo
});
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');



Route::middleware('auth:sanctum')->group(function () {
    // Rutas protegidas para admins

Route::middleware(['admin'])->group(function () {
Route::get('/admin/speakers', [SpeakerController::class, 'viewSpeaker'])->name('admin.speakers');
Route::get('/admin/event', [EventController::class, 'viewEvent'])->name('admin.event');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

});
Route::middleware(['user'])->group(function () {
Route::get('/users/eventList', [EventController::class, 'showEventList'])->name('users.eventList');
Route::get('/users/eventUser', [EventController::class, 'showEventUser'])->name('users.eventUser');
Route::post('/paypal/pay', [PayPalController::class, 'createPayment'])->name('paypal.pay');
Route::get('/paypal/success', [PayPalController::class, 'successPayment'])->name('paypal.success');
Route::get('/paypal/cancel', [PayPalController::class, 'cancelPayment'])->name('paypal.cancel');
Route::post('/users/logout', [AuthController::class, 'logout'])->name('logout');

});

});