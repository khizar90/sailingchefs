<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\MessageController;

use App\Models\Recipe;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('/register',[UserController::class, 'register']);
Route::post('/login',[UserController::class, 'login']);
Route::post('/resetAccountVerify',[UserController::class, 'resetAccountVerify']);
Route::post('/resetPassword',[UserController::class, 'resetPassword']);
Route::post('/chnagePassword',[UserController::class, 'chnagePassword']);
Route::get('/deleteAccount/{userId}',[UserController::class, 'deleteAccount']);
Route::post('/editProfile',[UserController::class, 'editProfile']);
Route::post('/{userId}/search',[UserController::class, 'search']);
Route::get('/blockUser',[UserController::class, 'blockUser']);
Route::get('/{userId}/blocklist',[UserController::class, 'blocklist']);
Route::get('/follow',[UserController::class, 'followUser']);
Route::get('/{userId}/followersList',[UserController::class, 'followersList']);
Route::get('/{userId}/followingsList',[UserController::class, 'followingsList']);
Route::post('/userVerification',[UserController::class, 'userVerification']);
Route::post('/otpVerification',[UserController::class, 'otpVerification']);


Route::post('/sendOtp',[UserController::class, 'sendOtp']);
Route::post('/verifyOtp',[UserController::class, 'verifyOtp']);


Route::post('/listChef',[UserController::class, 'listChef']);
Route::get('/{userId}/profile',[UserController::class, 'profile']);
Route::post('/{userId}/addRecipe',[UserController::class, 'addRecipe']);
Route::get('/{userId}/recipeList',[UserController::class, 'recipeList']);
Route::get('/userRecipeList/{userId}',[UserController::class, 'userRecipeList']);

Route::post('/addReview',[UserController::class, 'addReview']);
Route::get('/{recipeId}/recipeReview',[UserController::class, 'recipeReview']);


Route::post('/{userId}/notification',[UserController::class, 'notification']);

Route::get('/{userId}/getNotification',[UserController::class, 'getNotification']);
Route::get('/{userId}/readNotification',[UserController::class, 'readNotification']);



Route::post('/sendMessage',[MessageController::class, 'sendMessage']);
Route::post('/inbox/{userId}',[MessageController::class, 'inbox']);
Route::post('/conversation',[MessageController::class, 'conversation']);
Route::post('/readMessage/{userId}',[MessageController::class, 'readMessage']);
Route::get('/messageCount/{userId}',[MessageController::class, 'messageCount']);
Route::get('/deleteMessage/{userId}/{msgId}',[MessageController::class, 'deleteMessage']);







































?>