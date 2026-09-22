@extends('layouts.app')

@section('title', 'Enseignant · '.$intervenant->nom.' '.$intervenant->prenom)

@section('content')
<div class="crumb">
    <a href="{{ route('referentiel.intervenants.index') }}">Enseignants</a>
    <span class="sep">/</span>
    <a href="{{ route('referentiel.intervenants.show', $intervenant) }}">{{ $intervenant->nom }} {{ $intervenant->prenom }}</a>
    <span class="sep">/</span>
    <span class="current">Modification</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>{{ $intervenant->civilite === 'M' ? 'M.' : $intervenant->civilite }} {{ $intervenant->nom }} {{ $intervenant->prenom }}</h1>
        <p class="page-sub">{{ $intervenant->email }}</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.intervenants.update', $intervenant) }}" enctype="multipart/form-data" class="form-page">
    @csrf
    @method('PUT')
    @include('referentiel.intervenants._form', ['intervenant' => $intervenant])
    <div class="form-actions">
        <a href="{{ route('referentiel.intervenants.show', $intervenant) }}" class="btn btn-ghost">Retour à la fiche</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>
@endsection
