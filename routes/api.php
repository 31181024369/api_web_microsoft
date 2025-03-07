<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/admin-login', [App\Http\Controllers\Admin\LoginAdminController::class, 'login'])->name('admin-login');
Route::get('/admin-information', [App\Http\Controllers\Admin\LoginAdminController::class, 'information']);

Route::post('member-register', [App\Http\Controllers\Member\MemberController::class, 'register']);
Route::post('member-login', [App\Http\Controllers\Member\MemberController::class, 'login']);
Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
    Route::resource('information', App\Http\Controllers\Admin\AdminController::class);

    Route::post('update-infor-admin', [App\Http\Controllers\Admin\LoginAdminController::class, 'uploadInformation']);
    Route::resource('member', App\Http\Controllers\Admin\MemberController::class);
    Route::post('delete-all-member',[App\Http\Controllers\Admin\MemberController::class,'deleteAllMember']);

    Route::resource('quiz', App\Http\Controllers\Admin\QuizController::class);
    Route::resource('result-exams', App\Http\Controllers\Admin\ResultExamsController::class);
});
Route::group(['prefix' => 'member'], function () {
    Route::get('/show-quiz', [App\Http\Controllers\Member\QuizController::class, 'showQuiz']);
    Route::get('/show-quiz-detail/{slug}', [App\Http\Controllers\Member\QuizController::class, 'showDetailQuiz']);
    Route::post('/submit-quiz', [App\Http\Controllers\Member\QuizController::class, 'submitQuiz']);

    //Theory
    Route::resource('theory', App\Http\Controllers\Member\TheoryControler::class);
    Route::delete('theorys/delete', [App\Http\Controllers\Member\TheoryControler::class, 'delete']);

    Route::get('infor-member', [App\Http\Controllers\Member\MemberController::class, 'inforMember']);
});

//Theory Category
Route::resource('theory-category', App\Http\Controllers\Admin\TheOryCategoryController::class);
Route::delete('theory-categorys/delete', [App\Http\Controllers\Admin\TheOryCategoryController::class, 'delete']);
Route::get('theory-categories/show', [App\Http\Controllers\Admin\TheOryCategoryController::class, 'showTheoryCategory']);

// Theory
Route::resource('theory', App\Http\Controllers\Admin\TheOryController::class);
Route::delete('theorys/delete', [App\Http\Controllers\Admin\TheoryController::class, 'delete']);

//Gift Category
Route::resource('gift-category', App\Http\Controllers\Admin\GiftCategoryController::class);
Route::delete('gift-categories/delete', [App\Http\Controllers\Admin\GiftCategoryController::class, 'delete']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
