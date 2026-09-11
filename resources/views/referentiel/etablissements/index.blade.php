@extends('layouts.app')

@section('title', 'Établissements')

@section('content')
    <h1>Établissements</h1>
    <a class="btn" href="{{ route('referentiel.etablissements.create') }}">+ Nouvel établissement</a>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Ville</th>
                <th>Adresse</th>
                <th>Statut</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($etablissements as $etablissement)
                <tr>
                    <td>{{ $etablissement->nom_etablissement }}</td>
                    <td>{{ $etablissement->code_ville }}</td>
                    <td>{{ $etablissement->adresse }}</td>
                    <td>{{ $etablissement->visible ? 'Affiché' : 'Caché' }}</td>
                    <td><a href="{{ route('referentiel.etablissements.edit', $etablissement) }}">Modifier</a></td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun établissement.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $etablissements->links() }}
@endsection
