<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//Admin Auth
Route::match(['get', 'post'], '/admin-login', [App\Http\Controllers\Admin\LoginAdminController::class, 'login'])->name('admin-login');
Route::get('/admin-information', [App\Http\Controllers\Admin\LoginAdminController::class, 'information']);

//Member Auth
Route::post('member-register', [App\Http\Controllers\Member\MemberController::class, 'register']);
Route::post('member-login', [App\Http\Controllers\Member\MemberController::class, 'login']);

//Admin
Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
    Route::resource('information', App\Http\Controllers\Admin\AdminController::class);

    Route::post('update-infor-admin', [App\Http\Controllers\Admin\LoginAdminController::class, 'uploadInformation']);
    Route::resource('member', App\Http\Controllers\Admin\MemberController::class);
    Route::post('delete-all-member', [App\Http\Controllers\Admin\MemberController::class, 'deleteAllMember']);

    Route::resource('quiz', App\Http\Controllers\Admin\QuizController::class);
    Route::resource('result-exams', App\Http\Controllers\Admin\ResultExamsController::class);

    Route::resource('advertise', App\Http\Controllers\Admin\AdvertiseController::class);
    Route::resource('ad-pos', App\Http\Controllers\Admin\AdposController::class);

    Route::post('delete-all-advertise', [App\Http\Controllers\Admin\AdvertiseController::class, 'deleteAll']);
    Route::post('delete-all-ad-pos', [App\Http\Controllers\Admin\AdposController::class, 'deleteAll']);
});

//Member
Route::group(['prefix' => 'member'], function () {
    Route::get('/show-quiz', [App\Http\Controllers\Member\QuizController::class, 'showQuiz']);
    Route::get('/show-quiz-detail/{slug}', [App\Http\Controllers\Member\QuizController::class, 'showDetailQuiz']);
    Route::post('/submit-quiz', [App\Http\Controllers\Member\QuizController::class, 'submitQuiz']);

    //Theory
    Route::resource('theory', App\Http\Controllers\Member\TheoryControler::class);
    Route::delete('theorys/delete', [App\Http\Controllers\Member\TheoryControler::class, 'delete']);
    Route::get('theorys/{friendly_url}', [App\Http\Controllers\Member\TheoryControler::class, 'shows']);
    Route::get('get-newstheory', [App\Http\Controllers\Member\TheoryControler::class, 'take5theory']);

    Route::get('infor-member', [App\Http\Controllers\Member\MemberController::class, 'inforMember']);

    Route::get('show-advertise', [App\Http\Controllers\Member\AdvertiseController::class, 'showAdvertise']);
});

//Theory Category
Route::resource('theory-category', App\Http\Controllers\Admin\TheOryCategoryController::class);
Route::delete('theory-categorys/delete', [App\Http\Controllers\Admin\TheOryCategoryController::class, 'delete']);
Route::get('theory-categories/show', [App\Http\Controllers\Admin\TheOryCategoryController::class, 'showTheoryCategory']);

// Theory
Route::resource('theory', App\Http\Controllers\Admin\TheOryController::class);
Route::delete('theorys/delete', [App\Http\Controllers\Admin\TheoryController::class, 'delete']);

//Gift Admin
Route::resource('gift', App\Http\Controllers\Admin\GiftController::class);
Route::delete('gifts/delete', [App\Http\Controllers\Admin\GiftController::class, 'delete']);
Route::get('gift-history', [App\Http\Controllers\Admin\GifthistoryController::class, 'index']);

//Gift Member
Route::get('gift-member', [App\Http\Controllers\Member\GiftController::class, 'index']);
Route::post('gifts/{id}/redeem', [App\Http\Controllers\Member\GiftController::class, 'redeem']);
Route::get('member/gift-history', [App\Http\Controllers\Member\GiftHiststoryController::class, 'index']);

//Quiz History Member
Route::get('member/quiz-history', [App\Http\Controllers\Member\QuizHistoryController::class, 'index']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
