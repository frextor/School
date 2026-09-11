@extends('layouts.app')

@section('title', 'Mes bulletins')

@section('content')
    <h1>Mes bulletins</h1>

    <h2>Bulletins PDF</h2>
    <table>
        <thead><tr><th>Année</th><th>Semestre</th><th>Généré le</th><th></th></tr></thead>
        <tbody>
            @forelse ($bulletinsPdf as $bulletin)
                <tr>
                    <td>{{ $bulletin->annee }}</td>
                    <td>{{ $bulletin->semestre ?: '-' }}</td>
                    <td>{{ $bulletin->date_insert?->format('d/m/Y') }}</td>
                    <td><a href="{{ route('espace-eleve.bulletins.download', $bulletin) }}" target="_blank">Télécharger</a></td>
                </tr>
            @empty
                <tr><td colspan="4">Aucun bulletin PDF disponible.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Décisions de jury</h2>
    <table>
        <thead><tr><th>Année</th><th>Semestre</th><th>Décision</th><th>Commentaire</th></tr></thead>
        <tbody>
            @forelse ($bulletinsAdmin as $bulletin)
                <tr>
                    <td>{{ $bulletin->annee }}</td>
                    <td>{{ $bulletin->semestre }}</td>
                    <td>{{ $bulletin->decision_jury ?: '-' }}</td>
                    <td>{{ $bulletin->commentaire ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Aucune décision publiée.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
