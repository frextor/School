@extends('layouts.app')

@section('title', 'Nouvelle matière')

@section('content')
@php $cours = new \App\Models\Cours(); @endphp

<div class="crumb">
    <a href="{{ route('referentiel.cours.index') }}">Matières</a>
    <span class="sep">/</span>
    <span class="current">Nouvelle matière</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouvelle matière</h1>
        <p class="page-sub">Une matière du catalogue, à rattacher ensuite aux niveaux qui l'enseignent.</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.cours.store') }}" class="form-page">
    @csrf
    @include('referentiel.cours._form', ['cours' => $cours])
    <div class="form-actions">
        <a href="{{ route('referentiel.cours.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer la matière</button>
    </div>
</form>
@endsection
