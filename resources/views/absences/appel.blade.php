@extends('layouts.app')

@section('title', 'Appel')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <a href="{{ route('absences.index') }}">Assiduité</a>
    <span class="sep">/</span>
    <span class="current">Appel</span>
</div>

<div class="page-head">
    <div>
        <h1>Faire l'appel</h1>
        <p class="page-sub">Choisissez la classe et la séance, puis marquez les élèves absents ou en retard.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('absences.index') }}" class="btn btn-ghost">
            @include('partials.icon', ['n' => 'file', 's' => 15, 'w' => 2])Suivi des absences
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

{{-- Sélection de la séance : rechargement en GET pour afficher la liste --}}
<form method="get" class="filter-card">
    <div class="filter-row">
        <label class="stack" style="flex:1 1 200px">
            <span>Classe</span>
            <select name="classe" required onchange="this.form.submit()">
                <option value="">-- Choisir une classe --</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id_classe }}" @selected($classe?->id_classe === $c->id_classe)>{{ $c->classe }}</option>
                @endforeach
            </select>
        </label>
        <label class="stack">
            <span>Date</span>
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()">
        </label>
        <label class="stack">
            <span>Heure</span>
            <input type="time" name="heure" value="{{ $heure }}" onchange="this.form.submit()">
        </label>
        <label class="stack" style="flex:1 1 180px">
            <span>Matière (optionnel)</span>
            <select name="id_cours" onchange="this.form.submit()">
                <option value="">--</option>
                @foreach ($cours as $c)
                    <option value="{{ $c->id_cours }}" @selected($idCours === $c->id_cours)>{{ $c->nom_cours }}</option>
                @endforeach
            </select>
        </label>
    </div>
</form>

@if (! $classe)
    <div class="table-card">
        <div class="empty-cell">
            @include('partials.icon', ['n' => 'users', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
            <span>Choisissez une classe pour afficher la liste des élèves.</span>
        </div>
    </div>
@elseif ($eleves->isEmpty())
    <div class="table-card">
        <div class="empty-cell">
            @include('partials.icon', ['n' => 'users', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
            <span>Aucun élève actif dans la classe « {{ $classe->classe }} ».</span>
        </div>
    </div>
@else
    <form method="post" action="{{ route('absences.appel.store') }}">
        @csrf
        <input type="hidden" name="classe" value="{{ $classe->id_classe }}">
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="heure" value="{{ $heure }}">
        <input type="hidden" name="id_cours" value="{{ $idCours }}">

        <div class="table-card">
            <div class="table-head">
                <span class="table-count">
                    {{ $classe->classe }} · {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }} à {{ $heure }}
                    · {{ $eleves->count() }} élève(s)
                </span>
                <div class="bulk-actions">
                    <button type="button" class="btn btn-ghost" data-tous="present">Tous présents</button>
                    <button type="submit" class="btn">Enregistrer l'appel</button>
                </div>
            </div>

            @include('partials.appel-liste', ['eleves' => $eleves, 'existantes' => $existantes])

            <div class="table-foot" style="display:flex;justify-content:flex-end;padding:14px 16px">
                <button type="submit" class="btn">Enregistrer l'appel</button>
            </div>
        </div>
    </form>
@endif

<style>
    .filter-card .stack { margin: 0; }
    .filter-card .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .filter-card .stack input, .filter-card .stack select { width: 100%; max-width: none; }

</style>

@endsection
