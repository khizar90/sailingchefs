<?php

use Illuminate\Support\Facades\Route;
use App\Models\Recipe;
use App\Http\Controllers\AdminController;

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

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/', function () {
//     $photos = Recipe::select('image')->get();
//     return view('photo' , compact('photos'));
// });


Route::get('/catgory',[AdminController::class, 'listCatgory'])->name('catgory');
Route::get('/catgory/{cat_id}',[AdminController::class, 'deleteCatgory'])->name('deleteCatgory');
Route::post('/catgory',[AdminController::class, 'addCatgory'])->name('catgory');


Route::get('/allergies',[AdminController::class, 'listAllergies'])->name('allergies');
Route::get('/allergies/{all_id}',[AdminController::class, 'deleteAllergies'])->name('deleteAllergies');
Route::post('/allergies',[AdminController::class, 'addAllergies'])->name('addAllergies');


Route::get('/dietary',[AdminController::class, 'listDietary'])->name('dietary');
Route::get('/dietary/{dt_id}',[AdminController::class, 'deleteDietary'])->name('deleteDietary');
Route::post('/dietary',[AdminController::class, 'addDietary'])->name('Dietary');

Route::get('/userList',[AdminController::class, 'userList'])->name('userList');

Route::post('/{cat_id}/updateCatgory',[AdminController::class, 'updateCatgory'])->name('updateCatgory');
Route::post('/{all_id}/updateAllergy',[AdminController::class, 'updateAllergy'])->name('updateAllergy');
Route::post('/{all_id}/updateDietary',[AdminController::class, 'updateDietary'])->name('updateDietary');






// Route::get('/allergies', function () {
//     return view('allergies');
// })->name('allergies');

// Route::get('/dietary', function () {
//     return view('dietary');
// })->name('dietary');


