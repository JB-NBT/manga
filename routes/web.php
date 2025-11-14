<?php

use App\Http\Controllers\MangaController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\PublicationRequestController;
use App\Http\Controllers\TomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// ========================================
// PAGE D'ACCUEIL (Bibliothèque publique)
// ========================================
Route::get('/', [MangaController::class, 'index'])->name('home');

// ========================================
// AUTHENTIFICATION (login, register, logout)
// ========================================
Auth::routes();

// ========================================
// ROUTES AUTHENTIFIÉES
// ========================================
Route::middleware(['auth'])->group(function () {

    // ========================================
    // COLLECTION PERSONNELLE
    // ========================================
    Route::get('/ma-collection', [MangaController::class, 'myCollection'])->name('mangas.my-collection');

    // ========================================
    // CRUD MANGAS
    // ========================================
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

    // Modération des avis (Admin uniquement)
    Route::post('/avis/{avis}/moderate', [AvisController::class, 'moderate'])
        ->name('avis.moderate')
        ->middleware('role:admin');

    // ========================================
    // DEMANDES DE PUBLICATION
    // ========================================
    Route::post('/mangas/{manga}/demande-publication', [PublicationRequestController::class, 'store'])
        ->name('publication.request');

    Route::get('/mes-demandes', [PublicationRequestController::class, 'myRequests'])
        ->name('publication.my-requests');

    // ---------- ADMIN : gestion des demandes ----------
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/demandes-publication', [PublicationRequestController::class, 'index'])
            ->name('admin.publication.index');

        Route::post('/demandes-publication/{publicationRequest}/approve', [PublicationRequestController::class, 'approve'])
            ->name('admin.publication.approve');

        Route::post('/demandes-publication/{publicationRequest}/reject', [PublicationRequestController::class, 'reject'])
            ->name('admin.publication.reject');
    });

    // ========================================
    // TOMES
    // ========================================
    Route::get('/mangas/{manga}/tomes', [TomeController::class, 'index'])->name('tomes.index');
    Route::post('/mangas/{manga}/tomes/generate', [TomeController::class, 'generateTomes'])->name('tomes.generate');
    Route::post('/tomes/{tome}/toggle', [TomeController::class, 'togglePossede'])->name('tomes.toggle');
    Route::put('/tomes/{tome}', [TomeController::class, 'update'])->name('tomes.update');
});

// ========================================
// ROUTES PUBLIQUES AVEC PARAMÈTRES (toujours après les authentifiées)
// ========================================
Route::get('/mangas/{manga}', [MangaController::class, 'show'])->name('mangas.show');

// ========================================
// PAGES LÉGALES
// ========================================
Route::get('/mentions-legales', fn() => view('legal.mentions-legales'))->name('mentions-legales');
Route::get('/politique-confidentialite', fn() => view('legal.politique-confidentialite'))->name('politique-confidentialite');
Route::get('/cgv', fn() => view('legal.cgv'))->name('cgv');

// ========================================
// CONTACT
// ========================================
Route::get('/contact', fn() => view('legal.contact'))->name('contact');

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

