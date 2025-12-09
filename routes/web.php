<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});



Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - Lista de Boards
    Route::get('/dashboard', [BoardController::class, 'index'])->name('dashboard');

    // Boards
    Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');
    Route::get('/boards/{board}', [BoardController::class, 'show'])->name('boards.show');
    Route::put('/boards/{board}', [BoardController::class, 'update'])->name('boards.update');
    Route::delete('/boards/{board}', [BoardController::class, 'destroy'])->name('boards.destroy');

    // Lists
    Route::post('/boards/{board}/lists', [ListController::class, 'store'])->name('lists.store');
    Route::put('/lists/{list}', [ListController::class, 'update'])->name('lists.update');
    Route::post('/boards/{board}/lists/positions', [ListController::class, 'updatePositions'])->name('lists.positions');
    Route::delete('/lists/{list}', [ListController::class, 'destroy'])->name('lists.destroy');

    // Cards
    Route::post('/lists/{list}/cards', [CardController::class, 'store'])->name('cards.store');
    Route::put('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::post('/cards/{card}/move', [CardController::class, 'move'])->name('cards.move');
    Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
