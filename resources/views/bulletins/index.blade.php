@extends('layouts.app')

@section('title', 'Conseils de classe & décisions')

@section('content')
@php $filtre = request()->hasAny(['id_etablissement', 'id_referentiel', 'annee', 'semestre']); @endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Conseils de classe &amp; décisions</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Conseils de classe &amp; décisions</h1>
            <span class="badge badge-brand">{{ $bulletins->total() }}</span>
        </div>
        <p class="page-sub">Les décisions de jury enregistrées, et l'état de publication de chaque bulletin.</p>
    </div>
</div>

<form method="get" action="{{ route('bulletins.index') }}" class="filter-card">
    <div class="filter-row">
        <label class="field" style="flex:1 1 240px">
            <span>Établissement</span>
            <select name="id_etablissement">
                <option value="">Tous</option>
                @foreach ($etablissements as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected(request('id_etablissement') == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
        </label>

        <label class="field" style="flex:1 1 200px">
            <span>Classe</span>
            <select name="id_referentiel">
                <option value="">Toutes</option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe->id_classe }}" @selected(request('id_referentiel') == $classe->id_classe)>{{ $classe->classe }}</option>
                @endforeach
            </select>
        </label>

        <label class="field" style="flex:0 1 140px">
            <span>Année</span>
            <input type="number" name="annee" value="{{ request('annee') }}" placeholder="2026">
        </label>

        <label class="field" style="flex:0 1 130px">
            <span>Semestre</span>
            <select name="semestre">
                <option value="">Tous</option>
                @foreach ([1, 2] as $s)
                    <option value="{{ $s }}" @selected(request('semestre') == $s)>S{{ $s }}</option>
                @endforeach
            </select>
        </label>

        <button type="submit" class="btn">Filtrer</button>
        @if ($filtre)
            <a href="{{ route('bulletins.index') }}" class="btn btn-ghost">Réinitialiser</a>
        @endif
    </div>
</form>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Élève</th>
                    <th style="width:150px">Classe</th>
                    <th style="width:220px">Établissement</th>
                    <th style="width:150px">Période</th>
                    <th>Décision du jury</th>
                    <th style="width:130px">Publication</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bulletins as $bulletin)
                    <tr>
                        <td class="cell-name">
                            @if ($bulletin->id_eleve === 0)
                                <span style="color:var(--muted)">Classe entière</span>
                            @else
                                {{ $bulletin->eleve?->contact?->nom_complet ?? '—' }}
                            @endif
                        </td>
                        <td>{{ $bulletin->classe?->classe ?? '—' }}</td>
                        <td>{{ $bulletin->etablissement?->nom_etablissement ?? '—' }}</td>
                        <td>{{ $bulletin->annee }} · S{{ $bulletin->semestre }}</td>
                        <td>{{ $bulletin->decision_jury ?: '—' }}</td>
                        <td>
                            @if ($bulletin->est_publie == '1')
                                <span class="badge badge-success">Publié</span>
                            @else
                                <span class="badge">Brouillon</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">
                            @include('partials.icon', ['n' => 'file', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune décision enregistrée pour ces critères.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">{{ $bulletins->links() }}</div>
</div>
@endsection
