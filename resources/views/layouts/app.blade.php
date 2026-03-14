<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MangaLibrary')</title>
    @vite(['resources/css/style.css'])
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="{{ route('home') }}" class="navbar-brand">
                <span class="logo-text">Manga<span class="accent">Library</span></span>
            </a>

            <button class="navbar-toggle" id="navbarToggle" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="navbar-menu" id="navbarMenu">
                <!-- Barre de recherche (uniquement sur accueil et ma collection) -->
                @if(request()->routeIs('home') || request()->routeIs('mangas.my-collection'))
                <form action="{{ request()->routeIs('mangas.my-collection') ? route('mangas.my-collection') : route('home') }}" method="GET" class="search-form">
                    <input type="text" name="search" class="search-input" placeholder="Rechercher un manga..." value="{{ request('search') }}">
                    <button type="submit" class="search-btn">Rechercher</button>
                </form>
                @endif

                @guest
                    <div class="navbar-auth">
                        <a href="{{ route('login') }}" class="nav-link">Connexion</a>
                        <a href="{{ route('register') }}" class="btn-primary">Inscription</a>
                    </div>
                @else
                    <div class="navbar-links">
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a>
                        <a href="{{ route('mangas.my-collection') }}" class="nav-link {{ request()->routeIs('mangas.my-collection') ? 'active' : '' }}">Ma Collection</a>
                        @can('create manga')
                            <a href="{{ route('mangas.create') }}" class="nav-link {{ request()->routeIs('mangas.create') ? 'active' : '' }}">Ajouter</a>
                        @endcan
                        <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">Support</a>
                    </div>

                    @if(Auth::user()->hasAnyRole(['admin', 'moderator']))
                        <div class="navbar-admin">
                            <div class="dropdown">
                                <button class="dropdown-toggle">
                                    @if(Auth::user()->hasRole('admin'))
                                        <span class="role-badge admin">Admin</span>
                                    @else
                                        <span class="role-badge moderator">Mod</span>
                                    @endif
                                    <span class="dropdown-arrow">▼</span>
                                </button>
                                <div class="dropdown-menu">
                                    @can('approve publications')
                                        <a href="{{ route('admin.publication.index') }}" class="dropdown-item" style="border-left: 3px solid var(--warning); padding-left: 0.75rem;">
                                            📋 Demandes publication
                                        </a>
                                    @endcan
                                    @can('moderate avis')
                                        <a href="{{ route('admin.avis.index') }}" class="dropdown-item" style="border-left: 3px solid #60a5fa; padding-left: 0.75rem;">
                                            ⭐ Avis à modérer
                                        </a>
                                    @endcan
                                    <a href="{{ route('admin.tickets.index') }}" class="dropdown-item">Tickets</a>
                                    <a href="{{ route('admin.mangas-interdits.index') }}" class="dropdown-item">Interdits</a>
                                    <a href="{{ route('admin.copyright.management') }}" class="dropdown-item">Copyright</a>
                                    @if(Auth::user()->hasRole('admin'))
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('admin.users.index') }}" class="dropdown-item">Utilisateurs</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="navbar-user">
                        <div class="dropdown">
                            <button class="dropdown-toggle user-toggle">
                                <span class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                <span class="user-name">{{ Auth::user()->name }}</span>
                                <span class="dropdown-arrow">▼</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="{{ route('publication.my-requests') }}" class="dropdown-item">Mes demandes</a>
                                <a href="{{ route('tickets.index') }}" class="dropdown-item">Mes tickets</a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item dropdown-item-danger">Deconnexion</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="main-content">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="{{ route('mentions-legales') }}">Mentions legales</a>
                <a href="{{ route('politique-confidentialite') }}">Confidentialite</a>
                <a href="{{ route('cgv') }}">CGV</a>
                <a href="{{ route('contact') }}">Contact</a>
            </div>
            <p class="footer-copyright">MangaLibrary {{ date('Y') }}</p>
            @auth
                @if(Auth::user()->hasRole('admin'))
                <div class="footer-admin-section">
                    <div class="footer-admin-title">Outils Administration</div>
                    <div class="footer-admin-buttons">
                        <a href="/phpmyadmin" target="_blank" class="footer-admin-btn">
                            <span class="admin-icon">🗄️</span>
                            <span>phpMyAdmin</span>
                        </a>
                        <a href="http://glpi.local" target="_blank" class="footer-admin-btn">
                            <span class="admin-icon">🎫</span>
                            <span>GLPI</span>
                        </a>
                    </div>
                </div>
                @endif
            @endauth
        </div>
    </footer>

    <!-- Cookie Overlay (bloque le site) -->
    <div class="cookie-overlay" id="cookieOverlay"></div>

    <!-- Cookie Banner -->
    <div class="cookie-banner" id="cookieBanner">
        <div class="cookie-header">
            <h3>Gestion des cookies</h3>
            <p>Ce site utilise des cookies pour ameliorer votre experience. Veuillez accepter ou refuser pour continuer.</p>
        </div>
        <div class="cookie-actions">
            <button class="btn-cookie-accept" onclick="acceptCookies()">Accepter</button>
            <button class="btn-cookie-reject" onclick="rejectCookies()">Refuser</button>
            <button class="btn-cookie-settings" onclick="showCookieSettings()">Parametres</button>
        </div>
    </div>

    <!-- Cookie Settings Modal -->
    <div class="cookie-modal" id="cookieModal">
        <div class="cookie-modal-content">
            <h3>Parametres des cookies</h3>
            <div class="cookie-option">
                <label>
                    <input type="checkbox" checked disabled>
                    <span>Cookies essentiels</span>
                </label>
                <p>Necessaires au fonctionnement du site.</p>
            </div>
            <div class="cookie-option">
                <label>
                    <input type="checkbox" id="cookieAnalytics">
                    <span>Cookies analytiques</span>
                </label>
                <p>Nous aident a comprendre l'utilisation du site.</p>
            </div>
            <div class="cookie-option">
                <label>
                    <input type="checkbox" id="cookiePreferences">
                    <span>Cookies de preferences</span>
                </label>
                <p>Memorisent vos choix.</p>
            </div>
            <div class="cookie-modal-actions">
                <button class="btn-secondary" onclick="closeCookieModal()">Annuler</button>
                <button class="btn-primary" onclick="saveCookieSettings()">Enregistrer</button>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu
        document.getElementById('navbarToggle')?.addEventListener('click', function() {
            document.getElementById('navbarMenu').classList.toggle('show');
        });

        // Cookie management
        function checkCookieConsent() {
            const consent = localStorage.getItem('cookieConsent');
            if (!consent) {
                document.getElementById('cookieOverlay').classList.add('show');
                document.getElementById('cookieBanner').classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function acceptCookies() {
            localStorage.setItem('cookieConsent', JSON.stringify({
                essential: true,
                analytics: true,
                preferences: true,
                date: new Date().toISOString()
            }));
            hideCookieBanner();
        }

        function rejectCookies() {
            localStorage.setItem('cookieConsent', JSON.stringify({
                essential: true,
                analytics: false,
                preferences: false,
                date: new Date().toISOString()
            }));
            hideCookieBanner();
        }

        function hideCookieBanner() {
            document.getElementById('cookieOverlay').classList.remove('show');
            document.getElementById('cookieBanner').classList.remove('show');
            document.body.style.overflow = '';
        }

        function showCookieSettings() {
            document.getElementById('cookieModal').classList.add('show');
        }

        function closeCookieModal() {
            document.getElementById('cookieModal').classList.remove('show');
        }

        function saveCookieSettings() {
            localStorage.setItem('cookieConsent', JSON.stringify({
                essential: true,
                analytics: document.getElementById('cookieAnalytics').checked,
                preferences: document.getElementById('cookiePreferences').checked,
                date: new Date().toISOString()
            }));
            closeCookieModal();
            hideCookieBanner();
        }

        document.addEventListener('DOMContentLoaded', checkCookieConsent);
    </script>
</body>
</html>
