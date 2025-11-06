@extends('layouts.app')

@section('title', 'Contact')

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="color: var(--accent); margin-bottom: 2rem;">Contactez-nous</h1>
        
        <div style="background-color: var(--bg-card); padding: 2rem; border-radius: 10px;">
            <form method="POST" action="{{ route('contact.send') }}">
                @csrf
                
                <div class="form-group">
                    <label for="name" class="form-label">Nom *</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="subject" class="form-label">Sujet *</label>
                    <input type="text" name="subject" id="subject" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">Message *</label>
                    <textarea name="message" id="message" class="form-control" rows="6" required></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Envoyer</button>
            </form>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--bg-hover);">
                <h3 style="color: var(--accent);">Autres moyens de contact</h3>
                <p>
                    <strong>Email :</strong> <a href="mailto:contact@mangacollection.fr" style="color: var(--accent);">contact@mangacollection.fr</a><br>
                    <strong>Téléphone :</strong> [Ton téléphone]
                </p>
            </div>
        </div>
    </div>
@endsection
