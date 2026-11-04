<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimkermaController;
use App\Http\Controllers\AuthController;

// Rute Autentikasi Pengguna
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Utama Aplikasi SIMKERMA (Mendukung Menu 1 s.d. 7)
Route::get('/', [SimkermaController::class, 'index'])->name('simkerma.index');

// Rute Aksi Formulir dan FSM
Route::post('/simkerma/change-role', [SimkermaController::class, 'changeRole'])->name('simkerma.change-role');
Route::post('/simkerma/select-doc', [SimkermaController::class, 'selectDocument'])->name('simkerma.select-doc');
Route::post('/simkerma/store-jalur1', [SimkermaController::class, 'storeJalur1'])->name('simkerma.store-jalur1');
Route::post('/simkerma/store-jalur2', [SimkermaController::class, 'storeJalur2'])->name('simkerma.store-jalur2');
Route::post('/simkerma/transition', [SimkermaController::class, 'transition'])->name('simkerma.transition');
Route::post('/simkerma/reset-draft', [SimkermaController::class, 'resetDraft'])->name('simkerma.reset-draft');
Route::post('/simkerma/delete-draft', [SimkermaController::class, 'deleteDraft'])->name('simkerma.delete-draft');
Route::post('/simkerma/reset-all-documents', [SimkermaController::class, 'resetAllDocuments'])->name('simkerma.reset-all-documents');
Route::post('/simkerma/store-activity', [SimkermaController::class, 'storeActivity'])->name('simkerma.store-activity');
Route::post('/simkerma/store-evaluation', [SimkermaController::class, 'storeEvaluation'])->name('simkerma.store-evaluation');
Route::post('/simkerma/store-partner', [SimkermaController::class, 'storePartner'])->name('simkerma.store-partner');
Route::post('/simkerma/update-partner', [SimkermaController::class, 'updatePartner'])->name('simkerma.update-partner');

// Rute Verifikasi Publik QR Code (Bisa diakses via /verify/{reg_id} atau ?action=verify&reg=...)
Route::get('/verify/{reg_id?}', [SimkermaController::class, 'verify'])->name('simkerma.verify');
