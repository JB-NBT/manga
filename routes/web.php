<?php

use App\Http\Controllers\MangaController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\PublicationRequestController;
use App\Http\Controllers\TomeController;
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
    
    // CRUD mangas - CREATE AVANT SHOW
    Route::get('/mangas/create', [MangaController::class, 'create'])->name('mangas.create');
    Route::post('/mangas', [MangaController::class, 'store'])->name('mangas.store');
    Route::get('/mangas/{manga}/edit', [MangaController::class, 'edit'])->name('mangas.edit');
    Route::put('/mangas/{manga}', [MangaController::class, 'update'])->name('mangas.update');
    Route::delete('/mangas/{manga}', [MangaController::class, 'destroy'])->name('mangas.destroy');
    
    // ========================================
    // ROUTES AVIS
    // ========================================
    Route::post('/mangas/{manga}/avis', [AvisController::class, 'store'])->name('avis.store');
    Route::put('/avis/{avis}', [AvisController::class, 'update'])->name('avis.update');
    Route::delete('/avis/{avis}', [AvisController::class, 'destroy'])->name('avis.destroy');
    
    // Modération (admin uniquement)
    Route::post('/avis/{avis}/moderate', [AvisController::class, 'moderate'])
        ->name('avis.moderate')
        ->middleware('role:admin');
    
    // ========================================
    // ROUTES DEMANDES DE PUBLICATION
    // ========================================
    Route::post('/mangas/{manga}/demande-publication', [PublicationRequestController::class, 'store'])
        ->name('publication.request');
    
    Route::get('/mes-demandes', [PublicationRequestController::class, 'myRequests'])
        ->name('publication.my-requests');
    
    // Routes admin pour les demandes de publication
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/demandes-publication', [PublicationRequestController::class, 'index'])
            ->name('admin.publication.index');
        
        Route::post('/admin/demandes-publication/{publicationRequest}/approve', [PublicationRequestController::class, 'approve'])
            ->name('admin.publication.approve');
        
        Route::post('/admin/demandes-publication/{publicationRequest}/reject', [PublicationRequestController::class, 'reject'])
            ->name('admin.publication.reject');
    });
    
    // ========================================
    // ROUTES TOMES
    // ========================================
    Route::get('/mangas/{manga}/tomes', [TomeController::class, 'index'])->name('tomes.index');
    Route::post('/mangas/{manga}/tomes/generate', [TomeController::class, 'generateTomes'])->name('tomes.generate');
    Route::post('/tomes/{tome}/toggle', [TomeController::class, 'togglePossede'])->name('tomes.toggle');
    Route::put('/tomes/{tome}', [TomeController::class, 'update'])->name('tomes.update');
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
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);
    
    return redirect()->route('contact')
        ->with('success', 'Votre message a été envoyé avec succès !');
})->name('contact.send');
