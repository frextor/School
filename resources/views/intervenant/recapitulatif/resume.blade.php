@extends('layouts.app')

@section('title', 'Récapitulatif global')

@section('content')
    <a href="{{ route('recapitulatif.index') }}">&larr; Nouvelle saisie</a>
    <h1>Récapitulatif global des heures</h1>

    <form method="get" class="filters">
        <select name="id_etablissement" onchange="this.form.submit()">
            <option value="">-- Tous les établissements --</option>
            @foreach ($etablissementsDisponibles as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected($etablissementSelectionne == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>
    </form>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Établissement</th>
                <th>Classe</th>
                <th>Type</th>
                <th>Cours</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Volume</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lignes as $ligne)
                <tr>
                    <td>{{ $ligne->date_recapitulatif?->format('d/m/Y') }}</td>
                    <td>{{ $ligne->etablissement?->nom_etablissement }}</td>
                    <td>{{ $ligne->classe?->classe }}</td>
                    <td>{{ $ligne->typeCours?->type_cours }}</td>
                    <td>{{ $ligne->cours?->nom_cours }}</td>
                    <td>{{ $ligne->hdebut }}</td>
                    <td>{{ $ligne->hfin }}</td>
                    <td>{{ $ligne->volume_horaire }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Aucun récapitulatif.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
