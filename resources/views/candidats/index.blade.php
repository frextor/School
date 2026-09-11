@extends('layouts.app')

@section('title', 'Candidats')

@section('content')
    <h1>Candidats</h1>

    <form method="get" class="filters">
        <input type="text" name="recherche" placeholder="Nom, prénom..." value="{{ $filtres['recherche'] ?? '' }}">
        <label><input type="checkbox" name="sans_epreuve" value="1" @checked($filtres['sans_epreuve'] ?? false)> Sans épreuve programmée</label>
        <label><input type="checkbox" name="archives" value="1" @checked($filtres['archives'] ?? false) onchange="this.form.submit()"> Voir les archivés</label>
        <button type="submit">Filtrer</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Niveau</th>
                <th>Email</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($candidats as $candidat)
                <tr>
                    <td>{{ $candidat->contact?->nom_complet }}</td>
                    <td>{{ $candidat->niveau?->nom_niveau }}</td>
                    <td>{{ $candidat->contact?->email }}</td>
                    <td><a href="{{ route('candidats.show', $candidat) }}">Voir</a></td>
                </tr>
            @empty
                <tr><td colspan="4">Aucun candidat.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $candidats->links() }}
@endsection
