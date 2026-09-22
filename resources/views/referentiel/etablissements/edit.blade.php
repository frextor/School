@extends('layouts.app')

@section('title', 'Établissement · '.$etablissement->nom_etablissement)

@section('content')
<div class="crumb">
    <a href="{{ route('referentiel.etablissements.index') }}">Établissements</a>
    <span class="sep">/</span>
    <span class="current">{{ $etablissement->nom_etablissement }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $etablissement->nom_etablissement }}</h1>
            @if ($etablissement->visible)
                <span class="badge badge-success">Actif</span>
            @else
                <span class="badge">Masqué</span>
            @endif
        </div>
        <p class="page-sub">{{ $etablissement->adresse }}</p>
    </div>
</div>

{{-- Ce que le campus porte : un établissement ne se modifie pas à l'aveugle. --}}
<div class="form-page">
    <div class="rf-stats" style="margin-bottom:14px">
        <div class="rf-stat">
            <span class="rf-stat-label">Élèves</span>
            <span class="rf-stat-value">{{ $compteurs['eleves'] }}</span>
            <span class="rf-stat-hint">inscrits sur ce campus</span>
        </div>
        <div class="rf-stat">
            <span class="rf-stat-label">Classes</span>
            <span class="rf-stat-value">{{ $compteurs['classes'] }}</span>
            <span class="rf-stat-hint">rattachées</span>
        </div>
        <div class="rf-stat">
            <span class="rf-stat-label">Salles</span>
            <span class="rf-stat-value">{{ $compteurs['salles'] }}</span>
            <span class="rf-stat-hint">déclarées</span>
        </div>
    </div>
</div>

<form method="post" action="{{ route('referentiel.etablissements.update', $etablissement) }}" class="form-page">
    @csrf
    @method('PUT')

    @include('referentiel.etablissements._form', ['etablissement' => $etablissement])

    <div class="form-actions">
        <a href="{{ route('referentiel.etablissements.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<style>
    /* Les compteurs reprennent les cartes du référentiel pédagogique. */
    .rf-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; }
    .rf-stat { background: var(--surface); border: 1px solid var(--border); border-radius: 13px; padding: 13px 15px; }
    .rf-stat-label { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .rf-stat-value { display: block; margin-top: 5px; font-size: 24px; font-weight: 700; letter-spacing: -.025em; font-variant-numeric: tabular-nums; }
    .rf-stat-hint { display: block; margin-top: 2px; font-size: 11.5px; color: var(--muted); }
</style>
@endsection
