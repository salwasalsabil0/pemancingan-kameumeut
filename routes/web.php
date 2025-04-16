<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\FieldsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\IkanController;
use App\Http\Controllers\FieldFishController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('index');
Route::get('/storage-link', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    dd('storage link');
});

Route::get('/register', [AuthController::class, 'registerView'])->name('register');
Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::get('/forgot-password', [AuthController::class, 'forgotPasswordView'])->name('forgotPassword');
Route::get('/new-password/{token}', [AuthController::class, 'newPasswordView'])->name('newPassword');
Route::post('/register', [AuthController::class, 'register'])->name('register.auth');
Route::post('/login', [AuthController::class, 'login'])->name('login.auth');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgotPassword.auth');
Route::post('/new-password', [AuthController::class, 'newPassword'])->name('newPassword.auth');
Route::prefix('/berita')->group(function () {
    Route::get('/', [PostController::class, 'getBerita'])->name('getBerita');
    Route::get('/{id}', [PostController::class, 'showBerita'])->name('detailBerita');
});
Route::prefix('/event')->group(function () {
    Route::get('/', [PostController::class, 'getEvent'])->name('getEvent');
    Route::get('/{id}', [PostController::class, 'showEvent'])->name('detailEvent');
});

Route::middleware(['auth', 'inactivityTimeout:1800'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
            //Route::get('/field/data', [FieldFishController::class, 'indexField'])->name('admin.fieldIndex');
            Route::get('/field/data/create', [FieldFishController::class, 'createField'])->name('admin.fieldCreate');
            Route::post('/field/data/store', [FieldFishController::class, 'storeField'])->name('admin.fieldStore');
            Route::get('/field/data/edit/{id}', [FieldFishController::class, 'editField'])->name('admin.fieldEdit');
            Route::put('/field/data/update/{id}', [FieldFishController::class, 'updateField'])->name('admin.fieldUpdate');
            Route::delete('/field/data/delete/{id}', [FieldFishController::class, 'destroyField'])->name('admin.fieldDelete');

            //Route::get('/field/ikan', [IkanController::class, 'index'])->name('admin.indexIkan');
            Route::get('/field/data/createIkan', [FieldFishController::class, 'create'])->name('admin.createIkan');
            Route::post('/field/data/storeIkan', [FieldFishController::class, 'store'])->name('admin.storeIkan');
            Route::get('/field/data/editIkan/{id}', [FieldFishController::class, 'edit'])->name('admin.editIkan');
            Route::put('/field/data/updateIkan/{id}', [FieldFishController::class, 'update'])->name('admin.updateIkan');
            Route::delete('/field/data/deleteIkan/{id}', [FieldFishController::class, 'destroy'])->name('admin.deleteIkan');

            //data lapak(fields) dan data ikan(fishes)
            Route::get('/field/data', [FieldFishController::class, 'indexFields'])->name('admin.fieldsIndex');
            Route::get('/field/data/creates', [FieldFishController::class, 'createFields'])->name('admin.fieldsCreate');
            Route::post('/field/data/stores', [FieldFishController::class, 'storeFields'])->name('admin.fieldsStore');
            Route::get('/field/data/edits/{id}', [FieldFishController::class, 'editFields'])->name('admin.fieldsEdit'); 
            Route::put('/field/data/updates/{id}', [FieldFishController::class, 'updateFields'])->name('admin.fieldsUpdate');
            Route::delete('/field/data/deletes/{id}', [FieldFishController::class, 'destroyFields'])->name('admin.fieldsDelete');


            //admin antrian
            Route::get('/queue', [QueueController::class, 'index'])->name('admin.queue.index');
            Route::post('/queue/store', [QueueController::class, 'store'])->name('admin.queue.store');
            Route::post('/queue/update/{id}', [QueueController::class, 'update'])->name('admin.queue.update');
            Route::delete('/queue/{id}', [QueueController::class, 'destroyField'])->name('admin.queue.delete');


            // Field Schedule
            Route::get('/field/schedule', [FieldFishController::class, 'indexSchedule'])->name('admin.scheduleIndex');
            Route::get('/field/schedule-active', [FieldFishController::class, 'indexScheduleActive'])->name('admin.scheduleActiveIndex');
            Route::put('/field/schedule/update/{id}', [FieldFishController::class, 'updateSchedule'])->name('admin.scheduleUpdate');
            Route::delete('/field/schedule/delete', [FieldFishController::class, 'destroyScheduleActive'])->name('admin.scheduleActiveDelete');

            // Booking
            Route::get('/booking', [BookingController::class, 'index'])->name('admin.bookingIndex');
            Route::get('/booking/choose-field', [BookingController::class, 'chooseField'])->name('admin.chooseField');
            Route::get('/booking/choose-field/{id}', [BookingController::class, 'create'])->name('admin.bookingCreate');
            Route::get('/filter', [BookingController::class, 'checkAvailability'])->name('check.availability');
            Route::post('/booking/get-session', [BookingController::class, 'getSession'])->name('admin.getSession');

            // Booking Transaction
            Route::get('/booking/transaction', [BookingController::class, 'transaction'])->name('admin.transaction');
            Route::get('/booking/transaction/{id}', [BookingController::class, 'paymentTransaction'])->name('admin.paymentTransaction');
            Route::post('/booking/transaction/store', [BookingController::class, 'storeTransaction'])->name('admin.storeTransaction');

            Route::put('/booking/dp-success/{id}', [BookingController::class, 'confirmPaymentDP'])->name('admin.confirmPaymentDP');
            Route::put('/booking/remaining-success/{id}', [BookingController::class, 'confirmPaymentRemaining'])->name('admin.confirmPaymentRemaining');
            Route::put('/booking/canceled/{id}', [BookingController::class, 'canceledBooking'])->name('admin.canceledBooking');
            Route::put('/booking/invalidate/{id}', [BookingController::class, 'invalidatePaymentDP'])->name('admin.invalidatePaymentDP');

            // Transaction
            Route::get('/transaction', [BookingController::class, 'transactionIndex'])->name('admin.transactionIndex');
            Route::get('/transaction/load-transactions', [BookingController::class, 'loadTransactions'])->name('admin.loadTransactions');
            Route::get('/transaction/export-pdf', [BookingController::class, 'transactionExportPDF'])->name('admin.transactionExportPDF');

            // User
            Route::get('/user', [UserController::class, 'index'])->name('admin.userIndex');
            Route::get('/user/create', [UserController::class, 'create'])->name('admin.userCreate');
            Route::post('/user/store', [UserController::class, 'store'])->name('admin.userStore');
            Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('admin.userEdit');
            Route::put('/user/update/{id}', [UserController::class, 'update'])->name('admin.userUpdate');
            Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('admin.userDelete');

            // Payment Method
            Route::get('/payment-method', [PaymentMethodController::class, 'index'])->name('admin.paymentMethodIndex');
            Route::get('/payment-method/create', [PaymentMethodController::class, 'create'])->name('admin.paymentMethodCreate');
            Route::post('/payment-method/store', [PaymentMethodController::class, 'store'])->name('admin.paymentMethodStore');
            Route::get('/payment-method/edit/{id}', [PaymentMethodController::class, 'edit'])->name('admin.paymentMethodEdit');
            Route::put('/payment-method/update/{id}', [PaymentMethodController::class, 'update'])->name('admin.paymentMethodUpdate');
            Route::delete('/payment-method/delete/{id}', [PaymentMethodController::class, 'destroy'])->name('admin.paymentMethodDelete');

            // Post
            Route::get('/post', [PostController::class, 'index'])->name('admin.postIndex');
            Route::get('/post/create', [PostController::class, 'create'])->name('admin.postCreate');
            Route::get('/post/detail/{id}', [PostController::class, 'show'])->name('admin.postDetail');
            Route::get('/post/edit/{id}', [PostController::class, 'edit'])->name('admin.postEdit');
            Route::post('/post/store', [PostController::class, 'store'])->name('admin.postStore');
            Route::put('/post/update/{id}', [PostController::class, 'update'])->name('admin.postUpdate');
            Route::delete('/post/delete/{id}', [PostController::class, 'destroy'])->name('admin.postDelete');

            // Profile
            Route::get('/profile', [AuthController::class, 'profile'])->name('admin.profile');
            Route::get('/profile/edit/{id}', [AuthController::class, 'editProfile'])->name('admin.editProfile');
            Route::put('/profile/update/{id}', [AuthController::class, 'updateProfile'])->name('admin.updateProfile');

            // Index Data
            Route::get('/index-data', [IndexController::class, 'indexData'])->name('admin.indexData');
            Route::put('/index-data/update/{id}', [IndexController::class, 'updateIndexData'])->name('admin.updateIndexData');
        });
    });

    Route::middleware(['auth', 'queue.status'])->group(function () {
        Route::get('/booking', [BookingController::class, 'index'])->name('user.booking');
    });

    
    Route::middleware('role:user')->group(function () {
        Route::prefix('user')->group(function () {
            
            Route::get('/home', [IndexController::class, 'index'])->name('user.index');
            Route::get('/booking', [BookingController::class, 'chooseField'])->name('user.booking');
            Route::get('/booking/choose-field/{id}', [BookingController::class, 'create'])->name('user.bookingCreate');
            Route::get('/user/booking/choose-field/all', [BookingController::class, 'chooseAllKiloJebur'])->name('user.booking.kiloJebur');

            //tambahan route
            //Route::get('/booking/choose-field', [BookingController::class, 'chooseField'])->name('booking.chooseField');
            
            // Rute Antrian
            Route::get('/queue', [QueueController::class, 'index'])->name('user.queue.index');
            Route::post('/queue/store', [QueueController::class, 'store'])->name('user.queue.store');
            Route::post('/queue/update/{id}', [QueueController::class, 'update'])->name('user.queue.update');
            Route::post('/queue/auto-update', [QueueController::class, 'autoUpdateStatus'])->name('user.queue.autoUpdate');

            Route::get('/filter', [BookingController::class, 'checkAvailability'])->name('user.checkAvailability');
            Route::get('/booking/search', [BookingController::class, 'search'])->name('user.search');
            Route::post('/booking/get-session', [BookingController::class, 'getSession'])->name('user.getSession');

            Route::get('/transaction', [BookingController::class, 'transaction'])->name('user.transaction');
            Route::get('/transaction/payment/{id}', [BookingController::class, 'paymentTransaction'])->name('user.paymentTransaction');
            Route::get('/transaction/notice/{id}', [BookingController::class, 'noticeTransaction'])->name('user.noticeTransaction');
            Route::post('/transaction/store', [BookingController::class, 'storeTransaction'])->name('user.storeTransaction');
            Route::put('/transaction/payment-store/{id}', [BookingController::class, 'paymentTransactionStore'])->name('user.paymentTransactionStore');

            // Transaction History
            Route::get('/transaction/history', [TransactionController::class, 'transactionHistory'])->name('user.transactionHistory');
            Route::get('/transaction/history/{id}', [TransactionController::class, 'transactionHistoryShow'])->name('user.transactionHistoryDetail');

            // Profile
            Route::get('/profile', [AuthController::class, 'profile'])->name('user.profile');
            Route::get('/profile/edit/{id}', [AuthController::class, 'editProfile'])->name('user.editProfile');
            Route::put('/profile/update/{id}', [AuthController::class, 'updateProfile'])->name('user.updateProfile');
        });
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
