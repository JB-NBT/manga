@extends('layouts.app')
@section('title', 'Connexion - MangaLibrary')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h1>Connexion</h1>
        <p class="auth-subtitle">Connectez-vous a votre compte</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="votre@email.com">
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Votre mot de passe">
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--accent);">
                <label for="remember" style="margin: 0; font-weight: normal; color: var(--text-secondary);">Se souvenir de moi</label>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">
                Se connecter
            </button>

            @if (Route::has('password.request'))
                <p style="text-align: center; margin-top: 1rem;">
                    <a href="{{ route('password.request') }}" style="color: var(--accent-light); text-decoration: none;">Mot de passe oublie ?</a>
                </p>
            @endif
        </form>

        <div class="auth-footer">
            Pas encore de compte ? <a href="{{ route('register') }}">Inscrivez-vous</a>
        </div>
    </div>
</div>
@endsection
