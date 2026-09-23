@extends('layouts.app')

@section('title', 'Nouveau créneau')

@section('content')
@php $creneau = new \App\Models\ActiviteIntervenant(); @endphp

<div class="crumb">
    <a href="{{ route('planning.index') }}">Emploi du temps</a>
    <span class="sep">/</span>
    <span class="current">Nouveau créneau</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouveau créneau</h1>
        <p class="page-sub">Une séance de cours, placée dans l'emploi du temps d'une classe.</p>
    </div>
</div>

<form method="post" action="{{ route('planning.store') }}" class="form-page">
    @csrf
    @include('planning._form', ['creneau' => $creneau])
    <div class="form-actions">
        <a href="{{ route('planning.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer le créneau</button>
    </div>
</form>
@endsection
