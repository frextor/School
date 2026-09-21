@extends('layouts.app')

@section('title', "Appel · ".($creneau->cours?->nom_cours ?: 'Cours'))

@section('content')
<div class="crumb">
    <a href="{{ route('espace-intervenant.appel-jour', ['date' => $creneau->date_debut->format('Y-m-d')]) }}">Faire l'appel</a>
    <span class="sep">/</span>
    <span class="current">{{ $creneau->cours?->nom_cours ?: 'Cours' }}</span>
</div>

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

<div class="page-head">
    <div>
        <h1>{{ $creneau->cours?->nom_cours ?: 'Cours' }}</h1>
        <p class="page-sub">
            {{ ucfirst($creneau->date_debut->translatedFormat('l j F Y')) }}
            · {{ $creneau->date_debut->format('H:i') }} – {{ $creneau->date_fin->format('H:i') }}
            @if ($creneau->classe?->classe) · classe {{ $creneau->classe->classe }} @endif
            @if ($creneau->salle?->nom_salle) · salle {{ $creneau->salle->nom_salle }} @endif
        </p>
    </div>
</div>

<form method="post" action="{{ route('espace-intervenant.appel.store', $creneau) }}">
    @csrf

    <div class="table-card">
        <div class="table-head">
            <div class="bulk-bar">
                <span class="table-count">{{ $eleves->count() }} élève{{ $eleves->count() > 1 ? 's' : '' }}</span>
                <span class="bulk-actions">
                    <button type="button" class="btn btn-ghost" data-tous="present">Tous présents</button>
                    <button type="submit" class="btn">Enregistrer l'appel</button>
                </span>
            </div>
        </div>

        @if ($eleves->isEmpty())
            <div class="ap-vide">
                @include('partials.icon', ['n' => 'users', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
                <p>Aucun élève dans cette classe</p>
                <span>Il n'y a personne à pointer pour cette séance.</span>
            </div>
        @else
            @include('partials.appel-liste', ['eleves' => $eleves, 'existantes' => $existantes])

            <div class="table-foot ap-pied">
                <span class="ap-aide">Refaire l'appel corrige la saisie : un élève repassé « présent » voit sa ligne retirée.</span>
                <button type="submit" class="btn">Enregistrer l'appel</button>
            </div>
        @endif
    </div>
</form>

<style>
    .ap-pied { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; border-top: 1px solid var(--border-soft); }
    .ap-aide { font-size: 11.5px; color: var(--muted); }
    .ap-pied .btn { margin-left: auto; }
    .ap-vide { text-align: center; padding: 44px 20px; }
    .ap-vide p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .ap-vide span { display: block; margin-top: 4px; font-size: 13px; color: var(--muted); }
</style>
@endsection
