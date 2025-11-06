<?php

use App\Http\Controllers\MangaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Page d'accueil - Bibliothèque publique
Route::get('/', [MangaController::class, 'index'])->name('home');

// Routes d'authentification (login, register, logout)
Auth::routes();

// Routes authentifiées
Route::middleware(['auth'])->group(function () {
    // Collection personnelle
    Route::get('/ma-collection', [MangaController::class, 'myCollection'])->name('mangas.my-collection');
    
    // CRUD mangas - METTRE CREATE AVANT SHOW
    Route::get('/mangas/create', [MangaController::class, 'create'])->name('mangas.create');
    Route::post('/mangas', [MangaController::class, 'store'])->name('mangas.store');
    Route::get('/mangas/{manga}/edit', [MangaController::class, 'edit'])->name('mangas.edit');
    Route::put('/mangas/{manga}', [MangaController::class, 'update'])->name('mangas.update');
    Route::delete('/mangas/{manga}', [MangaController::class, 'destroy'])->name('mangas.destroy');
});

// Routes publiques avec paramètres dynamiques - TOUJOURS EN DERNIER
Route::get('/mangas/{manga}', [MangaController::class, 'show'])->name('mangas.show');

// Routes des pages légales
Route::get('/mentions-legales', function () {
    return view('legal.mentions-legales');
})->name('mentions-legales');

Route::get('/politique-confidentialite', function () {
    return view('legal.politique-confidentialite');
})->name('politique-confidentialite');

Route::get('/cgv', function () {
    return view('legal.cgv');
})->name('cgv');

// Routes de contact
Route::get('/contact', function () {
    return view('legal.contact');
})->name('contact');

Route::post('/contact/send', function (Request $request) {
    // Validation
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);
    
    // Pour l'instant, on redirige avec un message de succès
    // Plus tard, tu pourras ajouter l'envoi d'email
    return redirect()->route('contact')
        ->with('success', 'Votre message a été envoyé avec succès !');
})->name('contact.send');
