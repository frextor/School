@extends('layouts.app')

@section('title', 'Évaluation · '.$evaluation->nom_evaluation)

@section('content')
@php
    $referentiel = old('referentiel', $evaluation->referentiel) === 'groupe' ? 'groupe' : 'classe';
    $bareme = old('type_notation', $evaluation->type_notation ?: '20');
@endphp

<div class="crumb">
    <a href="{{ route('evaluations.index') }}">Évaluations</a>
    <span class="sep">/</span>
    <span class="current">{{ $evaluation->nom_evaluation }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $evaluation->nom_evaluation }}</h1>
            <span class="badge badge-brand">{{ $evaluation->date_evaluation->format('d/m/Y') }}</span>
        </div>
        <p class="page-sub">{{ $evaluation->matiere?->nom_cours }} · {{ $evaluation->campus?->nom_etablissement }}</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-ghost" href="{{ route('notes.index', $evaluation) }}">
            @include('partials.icon', ['n' => 'pencil', 's' => 15, 'w' => 2])Saisir les notes
        </a>
    </div>
</div>

<form method="post" action="{{ route('evaluations.update', $evaluation) }}" class="form-page">
    @csrf
    @method('PUT')

    <section class="form-card">
        <div class="form-head"><h2>Identification</h2></div>
        <div class="form-grid">
            <label class="field field-full">
                <span>Nom de l'évaluation</span>
                <input type="text" name="nom_evaluation" maxlength="50" required
                       value="{{ old('nom_evaluation', $evaluation->nom_evaluation) }}">
            </label>

            <label class="field">
                <span>Campus</span>
                <select name="id_campus" required>
                    @foreach ($etablissements as $etablissement)
                        <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_campus', $evaluation->id_campus) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                    @endforeach
                </select>
            </label>

            <label class="field">
                <span>Année</span>
                <input type="number" name="annee" required value="{{ old('annee', $evaluation->annee) }}">
            </label>

            <div class="field">
                <span class="field-label">Semestre</span>
                <div class="seg-group">
                    @foreach ([1, 2] as $s)
                        <label class="seg-opt">
                            <input type="radio" name="semestre" value="{{ $s }}" @checked((int) old('semestre', $evaluation->semestre) === $s) required>
                            <span>S{{ $s }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="form-card">
        <div class="form-head">
            <h2>Élèves concernés</h2>
            <span class="form-sub">Classe entière, ou sous-ensemble regroupé.</span>
        </div>

        <div class="form-grid">
            <div class="field">
                <span class="field-label">Référentiel</span>
                <div class="seg-group">
                    @foreach (['classe' => 'Classe', 'groupe' => 'Groupe'] as $valeur => $libelle)
                        <label class="seg-opt">
                            <input type="radio" name="referentiel" value="{{ $valeur }}"
                                   @checked($referentiel === $valeur) required data-referentiel>
                            <span>{{ $libelle }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- L'écran demandait l'identifiant numérique de la classe à la main :
                 un select par référentiel, seul l'actif est visible et soumis. --}}
            <label class="field" data-cible="classe" @unless($referentiel === 'classe') hidden @endunless>
                <span>Classe</span>
                <select name="id_referentiel" required @disabled($referentiel !== 'classe')>
                    @foreach ($classes as $classe)
                        <option value="{{ $classe->id_classe }}" @selected(old('id_referentiel', $evaluation->id_referentiel) == $classe->id_classe)>{{ $classe->classe }}</option>
                    @endforeach
                </select>
            </label>

            <label class="field" data-cible="groupe" @unless($referentiel === 'groupe') hidden @endunless>
                <span>Groupe</span>
                <select name="id_referentiel" required @disabled($referentiel !== 'groupe')>
                    @foreach ($groupes as $groupe)
                        <option value="{{ $groupe->id_groupe }}" @selected(old('id_referentiel', $evaluation->id_referentiel) == $groupe->id_groupe)>{{ $groupe->nom_groupe }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </section>

    <section class="form-card">
        <div class="form-head"><h2>Rattachement pédagogique</h2></div>
        <div class="form-grid">
            <label class="field">
                <span>Matière</span>
                <select name="id_matiere" required>
                    @foreach ($cours as $matiere)
                        <option value="{{ $matiere->id_cours }}" @selected(old('id_matiere', $evaluation->id_matiere) == $matiere->id_cours)>{{ $matiere->nom_cours }}</option>
                    @endforeach
                </select>
            </label>

            <label class="field">
                <span>Type d'évaluation</span>
                <select name="id_type_evaluation" required>
                    @foreach ($typesEvaluation as $te)
                        <option value="{{ $te->id_type_evaluation }}" @selected(old('id_type_evaluation', $evaluation->id_type_evaluation) == $te->id_type_evaluation)>{{ $te->type?->type ?? 'Type #'.$te->id_type_evaluation }} — coefficient {{ $te->coef }}</option>
                    @endforeach
                </select>
            </label>

            @if ($unites->isNotEmpty())
                <label class="field">
                    <span>Unité d'enseignement</span>
                    <select name="id_ue">
                        <option value="">Aucune</option>
                        @foreach ($unites as $unite)
                            <option value="{{ $unite->id_unite_enseignement }}" @selected(old('id_ue', $evaluation->id_ue) == $unite->id_unite_enseignement)>{{ $unite->nom_unite_enseignement }}</option>
                        @endforeach
                    </select>
                    <small class="field-aide">Facultatif : découpage du supérieur, sans objet en K-12.</small>
                </label>
            @endif
        </div>
    </section>

    <section class="form-card">
        <div class="form-head"><h2>Déroulé et notation</h2></div>
        <div class="form-grid">
            <label class="field">
                <span>Date</span>
                <input type="date" name="date_evaluation" required
                       value="{{ old('date_evaluation', $evaluation->date_evaluation->format('Y-m-d')) }}">
            </label>

            <label class="field">
                <span>Heure de début</span>
                <input type="time" name="heure_debut" value="{{ old('heure_debut', $evaluation->heure_debut) }}">
            </label>

            <label class="field">
                <span>Heure de fin</span>
                <input type="time" name="heure_fin" value="{{ old('heure_fin', $evaluation->heure_fin) }}">
            </label>

            <div class="field">
                <span class="field-label">Barème</span>
                <div class="seg-group">
                    @foreach (['20', '10', '5'] as $valeur)
                        <label class="seg-opt">
                            <input type="radio" name="type_notation" value="{{ $valeur }}" @checked($bareme === $valeur) required>
                            <span>/ {{ $valeur }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form-body" style="padding-top:0">
            <label class="check-card">
                <input type="checkbox" name="boolean_facultatif" value="1" @checked(old('boolean_facultatif', $evaluation->boolean_facultatif))>
                <span>
                    <strong>Évaluation facultative</strong>
                    Elle ne pénalise pas l'élève qui n'y participe pas : seuls les points au-dessus de la moyenne comptent.
                </span>
            </label>
        </div>
    </section>

    <div class="form-actions">
        <a href="{{ route('evaluations.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<form method="post" action="{{ route('evaluations.destroy', $evaluation) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer l\'évaluation « {{ $evaluation->nom_evaluation }} » ? Les notes saisies seront perdues.')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer cette évaluation
    </button>
</form>

<script>
    (function () {
        // Deux selects portent le même nom : on n'active que celui du
        // référentiel choisi, sinon les deux valeurs partent ensemble.
        var choix = document.querySelectorAll('[data-referentiel]');
        var cibles = document.querySelectorAll('[data-cible]');

        choix.forEach(function (radio) {
            radio.addEventListener('change', function () {
                cibles.forEach(function (bloc) {
                    var actif = bloc.dataset.cible === radio.value;
                    bloc.hidden = ! actif;
                    bloc.querySelector('select').disabled = ! actif;
                });
            });
        });
    })();
</script>
@endsection
