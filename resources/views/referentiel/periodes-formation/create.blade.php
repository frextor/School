@extends('layouts.app')

@section('title', 'Nouvelle période scolaire')

@section('content')
@php $periode = new \App\Models\PeriodeFormation(); @endphp

<div class="crumb">
    <a href="{{ route('referentiel.periodes-formation.index') }}">Périodes scolaires</a>
    <span class="sep">/</span>
    <span class="current">Nouvelle période</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouvelle période scolaire</h1>
        <p class="page-sub">Le découpage de l'année pour un ensemble de niveaux ou de classes.</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.periodes-formation.store') }}" class="form-page">
    @csrf
    @include('referentiel.periodes-formation._form', ['periode' => $periode])
    <div class="form-actions">
        <a href="{{ route('referentiel.periodes-formation.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer la période</button>
    </div>
</form>
@endsection
