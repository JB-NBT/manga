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
                <li><span class="datetime" id="datetime"></span></li>
                
                @guest
                    <li><a href="{{ route('login') }}" class="nav-link">Connexion</a></li>
                    <li><a href="{{ route('register') }}" class="btn-primary">Inscription</a></li>
                @else
                    <li><a href="{{ route('home') }}" class="nav-link">Accueil</a></li>
                    <li><a href="{{ route('mangas.my-collection') }}" class="nav-link">Ma Collection</a></li>
                    
                    @can('create manga')
                        <li><a href="{{ route('mangas.create') }}" class="nav-link">Ajouter un Manga</a></li>
                    @endcan

                    {{-- Mes demandes de publication --}}
                    <li><a href="{{ route('publication.my-requests') }}" class="nav-link">Mes demandes</a></li>

                    {{-- ADMIN --}}
                    @if(Auth::user()->hasRole('admin'))
                        <li>
                            <a href="{{ route('admin.publication.index') }}" class="nav-link" style="color: var(--warning);">
                                👑 Admin
                            </a>
                        </li>
                    @endif
                    
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link">
                                Déconnexion ({{ Auth::user()->name }})
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
