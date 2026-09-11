@extends('layouts.app')

@section('title', 'Bulletins (v2)')

@section('content')
    <h1>Bulletins disponibles</h1>
    <a class="btn" href="{{ route('bulletin-v2.create') }}">+ Générer un bulletin</a>

    <table>
        <thead>
            <tr>
                <th>Élève</th>
                <th>Établissement</th>
                <th>Année</th>
                <th>Semestre</th>
                <th>Généré le</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bulletins as $bulletin)
                <tr>
                    <td>{{ $bulletin->eleve?->contact?->nom_complet }}</td>
                    <td>{{ $bulletin->etablissement?->nom_etablissement }}</td>
                    <td>{{ $bulletin->annee }}</td>
                    <td>{{ $bulletin->semestre ?: '-' }}</td>
                    <td>{{ $bulletin->date_insert?->format('d/m/Y H:i') }}</td>
                    <td><a href="{{ route('bulletin-v2.show', $bulletin) }}" target="_blank">Voir le PDF</a></td>
                </tr>
            @empty
                <tr><td colspan="6">Aucun bulletin généré.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
