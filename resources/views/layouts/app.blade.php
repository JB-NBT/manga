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
                    
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer;">
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
