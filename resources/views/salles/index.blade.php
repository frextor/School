@extends('layouts.app')

@section('title', 'Salles')

@section('content')
@php $parEcole = $salles->groupBy(fn ($s) => $s->etablissement?->nom_etablissement ?: 'Établissement non précisé'); @endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Salles</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Salles</h1>
            <span class="badge badge-brand">{{ $salles->total() }} salle{{ $salles->total() > 1 ? 's' : '' }}</span>
        </div>
        <p class="page-sub">Les lieux où se tiennent les cours, avec leur capacité. L'emploi du temps y affecte les séances.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('salles.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouvelle salle
        </a>
    </div>
</div>

@forelse ($parEcole as $nomEcole => $lignes)
    <div class="grp-tete">
        <h2>{{ $nomEcole }}</h2>
        <span>{{ $lignes->count() }} salle{{ $lignes->count() > 1 ? 's' : '' }} · {{ $lignes->sum('nombre_place') }} places</span>
    </div>

    <div class="table-card" style="margin-bottom:14px">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:110px">Code</th>
                        <th>Nom</th>
                        <th style="width:150px">Capacité</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lignes as $salle)
                        <tr>
                            <td><span class="badge">{{ $salle->code_salle }}</span></td>
                            <td class="cell-name">{{ $salle->nom_salle }}</td>
                            <td>{{ $salle->nombre_place }} place{{ $salle->nombre_place > 1 ? 's' : '' }}</td>
                            <td class="col-actions">
                                <a href="{{ route('salles.edit', $salle) }}" class="row-btn" title="Modifier">
                                    @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                </a>
                                <form method="post" action="{{ route('salles.destroy', $salle) }}" class="inline-form"
                                      onsubmit="return confirm('Supprimer la salle « {{ $salle->nom_salle }} » ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="row-btn is-danger" title="Supprimer">
                                        @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#b91c1c', 'w' => 2])
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="vide-card">
        @include('partials.icon', ['n' => 'door', 's' => 28, 'c' => '#c9cdd9', 'w' => 1.6])
        <p>Aucune salle enregistrée</p>
        <span>Sans salle, une séance de l'emploi du temps ne peut pas indiquer où elle se tient.</span>
        <a href="{{ route('salles.create') }}">Créer une salle</a>
    </div>
@endforelse

@if ($salles->hasPages())
    <div class="pagination-foot">{{ $salles->links() }}</div>
@endif
@endsection
