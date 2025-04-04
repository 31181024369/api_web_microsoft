<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GifthistoryController;
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

    Route::resource('product', App\Http\Controllers\Admin\ProductController::class);

    Route::post('delete-all-advertise', [App\Http\Controllers\Admin\AdvertiseController::class, 'deleteAll']);
    Route::post('delete-all-ad-pos', [App\Http\Controllers\Admin\AdposController::class, 'deleteAll']);
    Route::post('delete-all-product', [App\Http\Controllers\Admin\ProductController::class, 'deleteAll']);

    Route::get('adminlogs', [App\Http\Controllers\Admin\AdminLogController::class, 'index']);
});

//Member
Route::group(['prefix' => 'member'], function () {
    Route::get('show-product', [App\Http\Controllers\Member\ProductController::class, 'showUser']);

    Route::get('/show-quiz', [App\Http\Controllers\Member\QuizController::class, 'showQuiz']);
    Route::get('/show-quiz-detail/{slug}', [App\Http\Controllers\Member\QuizController::class, 'showDetailQuiz'])->middleware('log.member');
    Route::post('/submit-quiz', [App\Http\Controllers\Member\QuizController::class, 'submitQuiz']);

    //Theory Member
    Route::resource('theory', App\Http\Controllers\Member\TheoryControler::class);
    Route::delete('theorys/delete', [App\Http\Controllers\Member\TheoryControler::class, 'delete']);
    Route::get('theorys/{friendly_url}', [App\Http\Controllers\Member\TheoryControler::class, 'shows'])->middleware('log.member');
    Route::get('get-newstheory', [App\Http\Controllers\Member\TheoryControler::class, 'take5theory']);
    Route::get('/category/{id}', [App\Http\Controllers\Member\TheoryControler::class, 'showCategory']);
    Route::get('infor-member', [App\Http\Controllers\Member\MemberController::class, 'inforMember']);

    Route::get('show-advertise', [App\Http\Controllers\Member\AdvertiseController::class, 'showAdvertise']);
});

Route::group(['middleware' => 'admin'], function () {
    //Theory Category Admin
    Route::resource('theory-category', App\Http\Controllers\Admin\TheOryCategoryController::class);
    Route::delete('theory-categorys/delete', [App\Http\Controllers\Admin\TheOryCategoryController::class, 'delete']);
    Route::get('theory-categories/show', [App\Http\Controllers\Admin\TheOryCategoryController::class, 'showTheoryCategory']);

    // Theory Admin
    Route::resource('theory', App\Http\Controllers\Admin\TheOryController::class);
    Route::delete('theorys/delete', [App\Http\Controllers\Admin\TheOryController::class, 'delete']);

    //Gift Admin
    Route::resource('gift', App\Http\Controllers\Admin\GiftController::class);
    Route::delete('gifts/delete', [App\Http\Controllers\Admin\GiftController::class, 'delete']);

    //Gift History Admin
    Route::get('gift-history', [App\Http\Controllers\Admin\GifthistoryController::class, 'index']);
    Route::get('detail-gift-history/{id}', [App\Http\Controllers\Admin\GifthistoryController::class, 'detail']);
    Route::patch('gifts/{id}/confirm', [App\Http\Controllers\Admin\GifthistoryController::class, 'confirm']);
    Route::get('admin/gift-history/export', [GifthistoryController::class, 'export']);
});
Route::get('admin/gift-history/exports', [GifthistoryController::class, 'export']);
//Gift Member
Route::get('gift-member', [App\Http\Controllers\Member\GiftController::class, 'index'])->middleware('log.member');
Route::post('gifts/{id}/redeem', [App\Http\Controllers\Member\GiftController::class, 'redeem']);
Route::get('member/gift-history', [App\Http\Controllers\Member\GiftHiststoryController::class, 'index'])->middleware('log.member');

//Quiz History Member
Route::get('member/quiz-history', [App\Http\Controllers\Member\QuizHistoryController::class, 'index']);
//Quiz member
Route::post('/send-gift-email', [App\Http\Controllers\Member\GiftController::class, 'sendGiftRedeemEmail']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('take_category_theory', [App\Http\Controllers\Member\TheoryControler::class, 'take_category_theory']);
//API ghi log member
Route::get('admin/member_log', [App\Http\Controllers\Admin\MemberLogController::class, 'index']);
