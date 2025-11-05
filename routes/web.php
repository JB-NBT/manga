<?php
use App\Http\Controllers\MangaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Page d'accueil - Bibliothèque publique
Route::get('/', [MangaController::class, 'index'])->name('home');

// Routes d'authentification (login, register, logout)
Auth::routes();

// Routes pour les mangas (accessible à tous pour voir)
Route::get('/mangas/{manga}', [MangaController::class, 'show'])->name('mangas.show');

// Routes authentifiées
Route::middleware(['auth'])->group(function () {
    // Collection personnelle
    Route::get('/ma-collection', [MangaController::class, 'myCollection'])->name('mangas.my-collection');
    
    // CRUD mangas (sauf index et show qui sont publics)
    Route::get('/mangas/create', [MangaController::class, 'create'])->name('mangas.create');
    Route::post('/mangas', [MangaController::class, 'store'])->name('mangas.store');
    Route::get('/mangas/{manga}/edit', [MangaController::class, 'edit'])->name('mangas.edit');
    Route::put('/mangas/{manga}', [MangaController::class, 'update'])->name('mangas.update');
    Route::delete('/mangas/{manga}', [MangaController::class, 'destroy'])->name('mangas.destroy');
});
