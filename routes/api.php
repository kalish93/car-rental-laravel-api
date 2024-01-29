<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalTransactionController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/login',[UserController::class, 'login']);
Route::post('/register',[UserController::class, 'register']);

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('/profile/{id}', [UserController::class, 'profile']);
    Route::post('/register/admin',[UserController::class, 'registerAdmin']);
    Route::get('/users',[UserController::class, 'allUsers']);
    Route::delete('/users/{id}',[UserController::class, 'deleteUser']);

    Route::get('/cars', [CarController::class, 'index']); // List all cars
    Route::get('/cars/available', [CarController::class, 'availableCars']); // List all cars
    Route::post('/cars', [CarController::class, 'registerCar']); // Register a new car
    Route::get('/cars/{id}', [CarController::class, 'show']); // View a specific car
    Route::put('/cars/{id}', [CarController::class, 'update']); // Update a specific car
    Route::delete('/cars/{id}', [CarController::class, 'destroy']);

    Route::get('/rental-transactions', [RentalTransactionController::class, 'index']);
    Route::get('/rental-transactions/{id}', [RentalTransactionController::class, 'show']);
    Route::post('/rental-transactions', [RentalTransactionController::class, 'store']);
    Route::put('/rental-transactions/{id}', [RentalTransactionController::class, 'update']);
    Route::delete('/rental-transactions/{id}', [RentalTransactionController::class, 'destroy']);
    Route::delete('/rental-transactions/return', [RentalTransactionController::class, 'returnCar']);
    Route::get('/rent-history/{userId}',[RentalTransactionController::class, 'myRentHistory']);
    Route::get('/rent-history',[RentalTransactionController::class, 'RentHistory']);

    // PaymentTransaction routes
    Route::post('/process-payment', [PaymentController::class, 'processPayment']);

});

Route::put('/cars/{id}/change-status', [CarController::class, 'changeCarStatus']);
