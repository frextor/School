@extends('layouts.app')

@section('title', 'Nouvelle classe')

@section('content')
<div class="crumb">
    <a href="{{ route('referentiel.classes.index') }}">Classes</a>
    <span class="sep">/</span>
    <span class="current">Nouvelle classe</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouvelle classe</h1>
        <p class="page-sub">Un groupe d'élèves d'un même niveau, sur un établissement.</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.classes.store') }}" class="form-page">
    @csrf

    <section class="form-card">
        <div class="form-head"><h2>La classe</h2></div>

        <div class="form-grid">
            <label class="field">
                <span>Nom de la classe</span>
                <input type="text" name="classe" required placeholder="1AEP A"
                       value="{{ old('classe') }}">
                <small class="field-aide">Tel qu'il apparaîtra sur les listes et les bulletins.</small>
            </label>

            <label class="field">
                <span>Niveau</span>
                <select name="id_niveau" required>
                    @foreach ($niveaux as $niveau)
                        <option value="{{ $niveau->id_niveau }}" @selected(old('id_niveau') == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
                    @endforeach
                </select>
                <small class="field-aide">Il détermine les matières et les coefficients.</small>
            </label>

            <label class="field">
                <span>Établissement</span>
                <select name="id_etablissement" required>
                    @foreach ($etablissements as $etablissement)
                        <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement') == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                    @endforeach
                </select>
            </label>

            <label class="field">
                <span>Couleur</span>
                {{-- Elle distingue les classes d'un coup d'œil dans l'emploi du temps. --}}
                <input type="color" name="couleur" class="champ-couleur" value="{{ old('couleur', '#4f46e5') }}">
                <small class="field-aide">Utilisée dans le calendrier et sur les panneaux.</small>
            </label>
        </div>
    </section>

    <div class="form-actions">
        <a href="{{ route('referentiel.classes.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer la classe</button>
    </div>
</form>

<style>
    .champ-couleur { height: 42px; padding: 4px; cursor: pointer; }
</style>
@endsection
