<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Middleware\AdminMiddleware;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostcardController;
use App\Http\Controllers\InvitationController; // Pastikan ini ada
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. ROUTE PUBLIK ---
Route::get('/', function () {
    return view('home');
});

// Route Darurat
Route::get('/admin', function () {
    $user = User::where('email', 'admin@gmail.com')->first();
    if (!$user) return 'User tidak ditemukan!';
    $user->role = 'admin';
    $user->save();
    return 'admin berhasil dibuat';
});

// --- 2. ROUTE USER (Harus Login) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard User (Menampilkan Postcards)
    Route::get('/dashboard', function () {
        $postcards = \App\Models\Postcard::latest()->get();
        return view('dashboard', compact('postcards'));
    })->name('dashboard');

    // === FITUR UNDANGAN (INVITATION) ===
    // Ini menggantikan rute '/user/undangan' yang error sebelumnya.
    // Menggunakan Controller agar data template & kontak terkirim ke view.
    
    // 1. Halaman Utama Undangan
    Route::get('/invitations', [InvitationController::class, 'index'])->name('invitation.index');
    
    // 2. Logic Template
    Route::post('/invitations/template', [InvitationController::class, 'store'])->name('invitation.store');
    Route::delete('/invitations/template/{invitation}', [InvitationController::class, 'destroy'])->name('invitation.destroy');
    Route::put('/invitations/template/{invitation}', [InvitationController::class, 'update'])->name('invitation.update');
    // 3. Logic Kontak (Recipients)
    Route::put('/recipients/{recipient}', [InvitationController::class, 'updateRecipient'])->name('recipient.update');
    Route::post('/recipients', [InvitationController::class, 'storeRecipient'])->name('recipient.store');
    Route::delete('/recipients/{recipient}', [InvitationController::class, 'destroyRecipient'])->name('recipient.destroy');

    // 4. Logic Kirim (AJAX)
    Route::post('/invitations/{invitation}/send', [InvitationController::class, 'send'])->name('invitation.send');
    // ===================================

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Route Lainnya
    Route::get('/user/About Us', function () {
        return view('user/about');
    })->name('about');

    Route::get('/postcard/{postcard}', [PostcardController::class, 'show'])->name('postcard.show');

    // Cek PHP (Bisa dihapus nanti)
    Route::get('/cek-php', function () {
        return [
            'Config' => php_ini_loaded_file(),
            'Upload Max' => ini_get('upload_max_filesize'),
            'Post Max' => ini_get('post_max_size'),
        ];
    });
});

// --- 3. ROUTE ADMIN ---
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    // Pilih satu dashboard admin yang aktif (PostcardController)
    Route::get('/dashboard', [PostcardController::class, 'adminIndex'])->name('admin.dashboard');

    // CRUD Postcard
    Route::post('/postcard', [PostcardController::class, 'store'])->name('admin.postcard.store');
    Route::put('/postcard/{postcard}', [PostcardController::class, 'update'])->name('admin.postcard.update');
    Route::delete('/postcard/{postcard}', [PostcardController::class, 'destroy'])->name('admin.postcard.destroy');
});

require __DIR__.'/auth.php';