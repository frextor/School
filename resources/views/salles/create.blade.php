@extends('layouts.app')

@section('title', 'Nouvelle salle')

@section('content')
@php $salle = new \App\Models\Salle(); @endphp

<div class="crumb">
    <a href="{{ route('salles.index') }}">Salles</a>
    <span class="sep">/</span>
    <span class="current">Nouvelle salle</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouvelle salle</h1>
        <p class="page-sub">Un lieu de cours, rattaché à un établissement.</p>
    </div>
</div>

<form method="post" action="{{ route('salles.store') }}" class="form-page">
    @csrf
    @include('salles._form', ['salle' => $salle])
    <div class="form-actions">
        <a href="{{ route('salles.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer la salle</button>
    </div>
</form>
@endsection
