@extends('layouts.app')

@section('title', 'Groupe · '.$groupe->nom_groupe)

@section('content')
<div class="crumb">
    <a href="{{ route('referentiel.groupes.index') }}">Groupes d'élèves</a>
    <span class="sep">/</span>
    <span class="current">{{ $groupe->nom_groupe }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>{{ $groupe->nom_groupe }}</h1>
        <p class="page-sub">Regroupement transversal d'élèves.</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.groupes.update', $groupe) }}" class="form-page">
    @csrf
    @method('PUT')

    <section class="form-card">
        <div class="form-head"><h2>Le groupe</h2></div>
        <div class="form-grid">
            <label class="field field-full">
                <span>Nom du groupe</span>
                <input type="text" name="nom_groupe" required
                       value="{{ old('nom_groupe', $groupe->nom_groupe) }}">
            </label>
        </div>
    </section>

    <div class="form-actions">
        <a href="{{ route('referentiel.groupes.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<form method="post" action="{{ route('referentiel.groupes.destroy', $groupe) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer le groupe « {{ $groupe->nom_groupe }} » ?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer ce groupe
    </button>
</form>
@endsection
