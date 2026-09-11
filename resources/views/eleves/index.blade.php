@extends('layouts.app')

@section('title', ($filtres['archives'] ?? false) ? 'Élèves archivés' : 'Élèves')

@section('content')
    <div class="crumb">
        <a href="{{ route('admin.dashboard') }}">🏠 Accueil</a>
        <span class="sep">/</span>
        <span class="current">{{ ($filtres['archives'] ?? false) ? 'Élèves archivés' : 'Élèves' }}</span>
    </div>

    <div class="page-header-row">
        <h1>{{ ($filtres['archives'] ?? false) ? 'Élèves archivés' : 'Élèves' }}</h1>
        <a class="btn" href="{{ route('eleves.create') }}">+ Nouvel élève</a>
    </div>

    <form method="get" class="filters">
        <input type="text" name="recherche" placeholder="Nom ou prénom..." value="{{ $filtres['recherche'] ?? '' }}">
        <select name="profil">
            <option value="">-- Profil --</option>
            @foreach (['eleve', 'alumni', 'reinscrit', 'abandon'] as $profil)
                <option value="{{ $profil }}" @selected(($filtres['profil'] ?? '') === $profil)>{{ ucfirst($profil) }}</option>
            @endforeach
        </select>
        <label><input type="checkbox" name="archives" value="1" @checked($filtres['archives'] ?? false) onchange="this.form.submit()"> Voir les archivés</label>
        <button type="submit">Filtrer</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Profil</th>
                <th>Niveau</th>
                <th>Classe</th>
                <th>Visible</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @php
                $badgeParProfil = [
                    'eleve' => 'badge-brand',
                    'candidat' => 'badge-warning',
                    'alumni' => 'badge-success',
                    'reinscrit' => 'badge-brand',
                    'abandon' => 'badge-danger',
                ];
            @endphp
            @forelse ($eleves as $eleve)
                <tr>
                    <td>{{ $eleve->id_eleve }}</td>
                    <td>{{ $eleve->contact?->nom_complet ?? '—' }}</td>
                    <td><span class="badge {{ $badgeParProfil[$eleve->profil] ?? '' }}">{{ ucfirst($eleve->profil) }}</span></td>
                    <td>{{ $eleve->niveau?->nom_niveau ?? '—' }}</td>
                    <td>{{ $eleve->classe?->classe ?? '—' }}</td>
                    <td>
                        @if ($eleve->visible)
                            <span class="badge badge-success">Oui</span>
                        @else
                            <span class="badge badge-danger">Non</span>
                        @endif
                    </td>
                    <td><a href="{{ route('eleves.show', $eleve) }}">Voir</a></td>
                </tr>
            @empty
                <tr><td colspan="7">Aucun élève trouvé.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $eleves->links() }}
@endsection
