<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class HomeController
 *
 * Gère l'affichage de la page d'accueil utilisateur.
 *
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Constructeur - Vérifie que l'utilisateur est authentifié.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche le tableau de bord utilisateur.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
}

