@extends('layouts.app')

@section('title', 'Entreprises')

@section('content')
    <h1>Entreprises</h1>
    <a class="btn" href="{{ route('entreprises.create') }}">+ Nouvelle entreprise</a>

    <form method="get" class="filters">
        <input type="text" name="recherche" placeholder="Nom..." value="{{ $filtres['recherche'] ?? '' }}">
        <button type="submit">Filtrer</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Ville</th>
                <th>Secteur</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entreprises as $entreprise)
                <tr>
                    <td>{{ $entreprise->nom_entreprise }}</td>
                    <td>{{ $entreprise->email }}</td>
                    <td>{{ $entreprise->ville }}</td>
                    <td>{{ $entreprise->secteur?->nom_secteur }}</td>
                    <td><a href="{{ route('entreprises.show', $entreprise) }}">Voir</a></td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune entreprise.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $entreprises->links() }}
@endsection
