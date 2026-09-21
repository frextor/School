@extends('layouts.app')

@section('title', 'Récapitulatif global')

@section('content')
    <div class="page-head">
        <div>
            <h1>Récapitulatif global des heures</h1>
            <p class="page-sub">Toutes les heures déclarées, tous intervenants confondus — la table héritée ne rattache pas les lignes à leur auteur.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('recapitulatif.index') }}" class="btn">Déclarer des heures</a>
        </div>
    </div>

    <form method="get" class="filters filter-card">
        <div class="filter-row">
            <select name="id_etablissement" onchange="this.form.submit()">
                <option value="">Tous les établissements</option>
                @foreach ($etablissementsDisponibles as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected($etablissementSelectionne == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="table-card"><div class="table-scroll">
    <table class="data-table">
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
                <tr>
                    <td colspan="8" class="empty-cell">
                        @include('partials.icon', ['n' => 'clock', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
                        <span>Aucune heure déclarée pour ce filtre.</span>
                        <a href="{{ route('recapitulatif.index') }}">Déclarer des heures</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div></div>
@endsection
