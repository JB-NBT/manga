@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs - Admin')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <h1 style="color: var(--accent); margin-bottom: 2rem;">
        Gestion des utilisateurs
        <span style="color: var(--text-secondary); font-size: 0.8em;">({{ $users->total() }})</span>
    </h1>

    <table style="width: 100%; border-collapse: collapse; background-color: var(--bg-card); border-radius: 10px; overflow: hidden;">
        <thead>
            <tr style="background-color: var(--bg-hover);">
                <th style="padding: 1rem; text-align: left; color: var(--text-primary);">ID</th>
                <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Nom</th>
                <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Email</th>
                <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Rôle</th>
                <th style="padding: 1rem; text-align: center; color: var(--text-primary);">Mangas</th>
                <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Inscrit le</th>
                <th style="padding: 1rem; text-align: center; color: var(--text-primary);">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr style="border-bottom: 1px solid var(--bg-hover);">
                    <td style="padding: 1rem; color: var(--text-secondary);">
                        {{ $user->id }}
                    </td>
                    <td style="padding: 1rem; color: var(--text-primary); font-weight: 600;">
                        {{ $user->name }}
                        @if($user->id === Auth::id())
                            <span style="color: var(--accent); font-size: 0.8rem;">(vous)</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; color: var(--text-secondary);">
                        {{ $user->email }}
                    </td>
                    <td style="padding: 1rem;">
                        @php
                            $role = $user->getRoleNames()->first() ?? 'user';
                        @endphp
                        <span class="badge" style="background-color:
                            @if($role == 'admin') var(--warning)
                            @elseif($role == 'moderator') var(--info, #3498db)
                            @else var(--bg-hover)
                            @endif;
                            @if($role == 'admin') color: #000; @endif">
                            {{ ucfirst($role) }}
                        </span>
                    </td>
                    <td style="padding: 1rem; text-align: center; color: var(--text-secondary);">
                        {{ $user->mangas_count }}
                    </td>
                    <td style="padding: 1rem; color: var(--text-secondary);">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                Voir
                            </a>
                            @if($user->id !== Auth::id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;"
                                            onclick="return confirm('Supprimer cet utilisateur et tous ses mangas ?')">
                                        Supprimer
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination" style="margin-top: 2rem;">
        {{ $users->links() }}
    </div>
</div>
@endsection
