@extends('layouts.app')

@section('title', 'Période · '.$periode->periode)

@section('content')
<div class="crumb">
    <a href="{{ route('referentiel.periodes-formation.index') }}">Périodes scolaires</a>
    <span class="sep">/</span>
    <span class="current">{{ $periode->periode }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $periode->periode }}</h1>
            <span class="badge badge-brand">{{ $periode->annee_scolaire }}</span>
        </div>
        <p class="page-sub">{{ $periode->nb_heure_annuel ? $periode->nb_heure_annuel.' heures sur l\'année' : 'Volume horaire non renseigné' }}</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.periodes-formation.update', $periode) }}" class="form-page">
    @csrf
    @method('PUT')
    @include('referentiel.periodes-formation._form', ['periode' => $periode])
    <div class="form-actions">
        <a href="{{ route('referentiel.periodes-formation.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<form method="post" action="{{ route('referentiel.periodes-formation.destroy', $periode) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer la période « {{ $periode->periode }} » ?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer cette période
    </button>
</form>
@endsection
