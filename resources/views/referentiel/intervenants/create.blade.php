@extends('layouts.app')

@section('title', 'Nouvel enseignant')

@section('content')
@php $intervenant = new \App\Models\Intervenant(); @endphp

<div class="crumb">
    <a href="{{ route('referentiel.intervenants.index') }}">Enseignants</a>
    <span class="sep">/</span>
    <span class="current">Nouvel enseignant</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouvel enseignant</h1>
        <p class="page-sub">Son identité, ses coordonnées et les matières qu'il enseigne.</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.intervenants.store') }}" enctype="multipart/form-data" class="form-page">
    @csrf
    @include('referentiel.intervenants._form', ['intervenant' => $intervenant])
    <div class="form-actions">
        <a href="{{ route('referentiel.intervenants.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer l'enseignant</button>
    </div>
</form>
@endsection
