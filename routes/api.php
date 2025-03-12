<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\Api\ContactUsController;

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
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [UserController::class, 'store'])->name('user.register');

Route::prefix('password')->name('password.')->group(function () {
    Route::post('/email', [AuthController::class, 'sendResetPasswordEmail'])->name('email');
    Route::post('/reset/{token}', [AuthController::class, 'resetPassword'])->name('update');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/get-profile', [AuthController::class, 'getProfile']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/checkout-session', [StripeController::class, 'createCheckoutSession']);
    Route::post('/subscription-status', [StripeController::class, 'checkSubscriptionStatus']);
});

Route::get('/get-subscriptions', [StripeController::class, 'getSubscriptions'])->name('get.subscriptions');

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::apiResource('users', UserController::class);
    Route::post('/users/{id}/block', [UserController::class, 'blockUser'])->name('users.block');
    Route::post('/users/{id}/unblock', [UserController::class, 'unblockUser'])->name('users.unblock');

    // Route::post('/logout', [AuthController::class, 'logout']);
});
// Route::post('/webhook', [StripeController::class, 'webhook']);

//  contact us all resources
Route::apiResource('contact-us', ContactUsController::class);
// need to remove

Route::get('/reminder-subscription-buffer', [StripeController::class, 'reminderSubscriptionBuffer']);
Route::get('/expire-subscription', [StripeController::class, 'expireSubscription']);
