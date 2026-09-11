@extends('layouts.app')

@section('title', "Modifier l'évaluation")

@section('content')
    <a href="{{ route('evaluations.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $evaluation->nom_evaluation }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('evaluations.update', $evaluation) }}">
        @csrf
        @method('PUT')

        <label for="nom_evaluation">Nom</label>
        <input type="text" name="nom_evaluation" id="nom_evaluation" maxlength="50" value="{{ old('nom_evaluation', $evaluation->nom_evaluation) }}" required>

        <label for="id_campus">Campus</label>
        <select name="id_campus" id="id_campus" required>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_campus', $evaluation->id_campus) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="annee">Année</label>
        <input type="number" name="annee" id="annee" value="{{ old('annee', $evaluation->annee) }}" required>

        <label for="semestre">Semestre</label>
        <input type="number" name="semestre" id="semestre" value="{{ old('semestre', $evaluation->semestre) }}" required>

        <label for="referentiel">Référentiel</label>
        <select name="referentiel" id="referentiel" required>
            <option value="classe" @selected(old('referentiel', $evaluation->referentiel) === 'classe')>Classe</option>
            <option value="groupe" @selected(old('referentiel', $evaluation->referentiel) === 'groupe')>Groupe</option>
        </select>

        <label for="id_referentiel">ID classe ou groupe</label>
        <input type="number" name="id_referentiel" id="id_referentiel" value="{{ old('id_referentiel', $evaluation->id_referentiel) }}" required>

        <label for="id_ue">Unité d'enseignement</label>
        <select name="id_ue" id="id_ue" required>
            @foreach ($unites as $unite)
                <option value="{{ $unite->id_unite_enseignement }}" @selected(old('id_ue', $evaluation->id_ue) == $unite->id_unite_enseignement)>{{ $unite->nom_unite_enseignement }}</option>
            @endforeach
        </select>

        <label for="id_matiere">Cours</label>
        <select name="id_matiere" id="id_matiere" required>
            @foreach ($cours as $cour)
                <option value="{{ $cour->id_cours }}" @selected(old('id_matiere', $evaluation->id_matiere) == $cour->id_cours)>{{ $cour->nom_cours }}</option>
            @endforeach
        </select>

        <label for="id_type_evaluation">Type d'évaluation (configuration)</label>
        <select name="id_type_evaluation" id="id_type_evaluation" required>
            @foreach ($typesEvaluation as $te)
                <option value="{{ $te->id_type_evaluation }}" @selected(old('id_type_evaluation', $evaluation->id_type_evaluation) == $te->id_type_evaluation)>{{ $te->type?->type }} (coef {{ $te->coef }})</option>
            @endforeach
        </select>

        <label for="date_evaluation">Date</label>
        <input type="date" name="date_evaluation" id="date_evaluation" value="{{ old('date_evaluation', $evaluation->date_evaluation->format('Y-m-d')) }}" required>

        <label for="heure_debut">Heure début</label>
        <input type="time" name="heure_debut" id="heure_debut" value="{{ old('heure_debut', $evaluation->heure_debut) }}">

        <label for="heure_fin">Heure fin</label>
        <input type="time" name="heure_fin" id="heure_fin" value="{{ old('heure_fin', $evaluation->heure_fin) }}">

        <label for="type_notation">Type de notation</label>
        <input type="text" name="type_notation" id="type_notation" maxlength="2" value="{{ old('type_notation', $evaluation->type_notation) }}" required>

        <label><input type="checkbox" name="boolean_facultatif" value="1" @checked(old('boolean_facultatif', $evaluation->boolean_facultatif))> Facultatif</label>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
