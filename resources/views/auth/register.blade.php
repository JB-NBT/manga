@extends('layouts.app')
@section('title', 'Inscription - MangaLibrary')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h1>Inscription</h1>
        <p class="auth-subtitle">Creez votre compte MangaLibrary</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nom d'utilisateur</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Votre pseudo">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="votre@email.com">
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Minimum 8 caracteres">
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm" class="form-label">Confirmer le mot de passe</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmez votre mot de passe">
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">
                S'inscrire
            </button>
        </form>

        <div class="auth-footer">
            Deja un compte ? <a href="{{ route('login') }}">Connectez-vous</a>
        </div>
    </div>
</div>
@endsection
