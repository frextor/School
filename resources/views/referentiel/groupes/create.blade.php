@extends('layouts.app')

@section('title', 'Nouveau groupe')

@section('content')
<div class="crumb">
    <a href="{{ route('referentiel.groupes.index') }}">Groupes d'élèves</a>
    <span class="sep">/</span>
    <span class="current">Nouveau groupe</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouveau groupe</h1>
        <p class="page-sub">Un regroupement transversal d'élèves : soutien, langue optionnelle, activité.</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.groupes.store') }}" class="form-page">
    @csrf

    <section class="form-card">
        <div class="form-head"><h2>Le groupe</h2></div>
        <div class="form-grid">
            <label class="field field-full">
                <span>Nom du groupe</span>
                <input type="text" name="nom_groupe" required placeholder="Soutien mathématiques, Anglais renforcé…"
                       value="{{ old('nom_groupe') }}">
                <small class="field-aide">Les élèves s'y rattachent depuis leur fiche.</small>
            </label>
        </div>
    </section>

    <div class="form-actions">
        <a href="{{ route('referentiel.groupes.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer le groupe</button>
    </div>
</form>
@endsection
