@extends('layouts.app')

@section('title', 'Nouvel établissement')

@section('content')
@php $etablissement = new \App\Models\Etablissement(); @endphp

<div class="crumb">
    <a href="{{ route('referentiel.etablissements.index') }}">Établissements</a>
    <span class="sep">/</span>
    <span class="current">Nouvel établissement</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouvel établissement</h1>
        <p class="page-sub">Un campus supplémentaire, avec son nom, son code et son adresse.</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.etablissements.store') }}" class="form-page">
    @csrf

    @include('referentiel.etablissements._form', ['etablissement' => $etablissement])

    <div class="form-actions">
        <a href="{{ route('referentiel.etablissements.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer l'établissement</button>
    </div>
</form>
@endsection
