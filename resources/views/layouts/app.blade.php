<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manga Library')</title>
    @vite(['resources/css/style.css'])
</head>
<body>
    <!-- Header -->
    <nav class="navbar">
        <div class="container">
            <a href="{{ route('home') }}" class="navbar-brand">
                <span class="logo">📚</span>
                <span>Manga Library</span>
            </a>
            
            <ul class="navbar-nav">
                <!-- Date et heure -->
                <li class="datetime-wrapper">
                    <span class="datetime" id="datetime"></span>
                </li>
                
                @guest
                    <li><a href="{{ route('login') }}" class="nav-link">Connexion</a></li>
                    <li><a href="{{ route('register') }}" class="btn-primary">Inscription</a></li>
                @else
                    <!-- Navigation principale -->
                    <li><a href="{{ route('home') }}" class="nav-link">Accueil</a></li>
                    <li><a href="{{ route('mangas.my-collection') }}" class="nav-link">Ma Collection</a></li>
                    
                    @can('create manga')
                        <li><a href="{{ route('mangas.create') }}" class="nav-link">Ajouter</a></li>
                    @endcan

                    {{-- Mes demandes de publication --}}
                    <li><a href="{{ route('publication.my-requests') }}" class="nav-link">Mes demandes</a></li>

                    {{-- ADMIN - Séparateur visuel --}}
                    @if(Auth::user()->hasRole('admin'))
                        <li class="nav-separator"></li>
                        <li>
                            <a href="{{ route('admin.publication.index') }}" class="nav-link nav-link-admin" title="Panel Admin">
                                <span class="admin-badge">👑 Admin</span>
                            </a>
                        </li>
                    @endif
                    
                    <!-- Déconnexion -->
                    <li class="nav-separator"></li>
                    <li class="user-menu">
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link nav-link-logout">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                                <span class="logout-icon">🚪</span>
                            </button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer style="
        background-color: var(--bg-card);
        border-top: 1px solid var(--bg-hover);
        margin-top: 4rem;
        padding: 2rem 0;
        text-align: center;
    ">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; margin-bottom: 1rem;">
                <a href="{{ route('mentions-legales') }}" style="color: var(--text-secondary); text-decoration: none; transition: color 0.3s;">
                    Mentions légales
                </a>
                <a href="{{ route('politique-confidentialite') }}" style="color: var(--text-secondary); text-decoration: none; transition: color 0.3s;">
                    Politique de confidentialité
                </a>
                <a href="{{ route('cgv') }}" style="color: var(--text-secondary); text-decoration: none; transition: color 0.3s;">
                    CGV
                </a>
                <a href="{{ route('contact') }}" style="color: var(--text-secondary); text-decoration: none; transition: color 0.3s;">
                    Contact
                </a>
            </div>
            
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">
                © {{ date('Y') }} MangaCollection - Tous droits réservés
            </p>
        </div>
    </footer>

    <style>
    /* Footer */
    footer a:hover {
        color: var(--accent);
    }
    
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    main {
        flex: 1;
    }

    /* Navbar améliorée */
    .navbar-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .datetime-wrapper {
        margin-right: 1rem;
        padding-right: 1rem;
        border-right: 1px solid var(--border);
    }

    .nav-separator {
        width: 1px;
        height: 30px;
        background-color: var(--border);
        margin: 0 0.5rem;
    }

    .nav-link-admin {
        position: relative;
        animation: glow 2s ease-in-out infinite;
    }

    .admin-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, var(--warning), var(--accent));
        color: #000;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
        box-shadow: 0 2px 8px rgba(255, 209, 102, 0.3);
    }

    .nav-link-admin:hover .admin-badge {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(255, 209, 102, 0.5);
    }

    @keyframes glow {
        0%, 100% { 
            filter: brightness(1); 
        }
        50% { 
            filter: brightness(1.2); 
        }
    }

    .user-menu {
        margin-left: auto;
    }

    .nav-link-logout {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
    }

    .user-name {
        font-weight: 600;
    }

    .logout-icon {
        font-size: 1.1rem;
    }

    .nav-link-logout:hover {
        background-color: var(--accent);
        color: var(--text-primary);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar .container {
            flex-direction: column;
            gap: 1rem;
        }
        
        .navbar-nav {
            flex-wrap: wrap;
            justify-content: center;
        }

        .datetime-wrapper {
            width: 100%;
            text-align: center;
            border-right: none;
            border-bottom: 1px solid var(--border);
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .nav-separator {
            display: none;
        }
    }
    </style>

    <!-- Script pour la date/heure -->
    <script>
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('datetime').textContent = now.toLocaleDateString('fr-FR', options);
        }
        
        updateDateTime();
        setInterval(updateDateTime, 60000);
    </script>
</body>
</html>
