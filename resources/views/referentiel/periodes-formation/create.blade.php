@extends('layouts.app')

@section('title', 'Nouvelle période de formation')

@section('content')
    <a href="{{ route('referentiel.periodes-formation.index') }}">&larr; Retour</a>
    <h1>Nouvelle période de formation</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.periodes-formation.store') }}">
        @csrf

        <label for="annee_scolaire">Année scolaire</label>
        <input type="text" name="annee_scolaire" id="annee_scolaire" placeholder="2025/2026" value="{{ old('annee_scolaire') }}" required>

        <label for="periode">Période</label>
        <input type="text" name="periode" id="periode" value="{{ old('periode') }}" required>

        <label for="nb_heure_annuel">Nombre d'heures annuel</label>
        <input type="number" step="0.01" name="nb_heure_annuel" id="nb_heure_annuel" value="{{ old('nb_heure_annuel') }}">

        <label for="diplome_rncp">Diplôme RNCP</label>
        <input type="text" name="diplome_rncp" id="diplome_rncp" value="{{ old('diplome_rncp') }}">

        <label for="code_diplome">Code diplôme</label>
        <input type="text" name="code_diplome" id="code_diplome" value="{{ old('code_diplome') }}">

        <label for="id_niveau">Niveaux concernés</label>
        <select name="id_niveau[]" id="id_niveau" multiple size="6">
            @foreach ($niveaux as $niveau)
                <option value="{{ $niveau->id_niveau }}" @selected(collect(old('id_niveau', []))->contains($niveau->id_niveau))>{{ $niveau->nom_niveau }}</option>
            @endforeach
        </select>

        <label for="id_classe">Classes concernées</label>
        <select name="id_classe[]" id="id_classe" multiple size="6">
            @foreach ($classes as $classe)
                <option value="{{ $classe->id_classe }}" @selected(collect(old('id_classe', []))->contains($classe->id_classe))>{{ $classe->classe }}</option>
            @endforeach
        </select>

        <label>Périodes trimestrielles</label>
        <div id="trimestres">
            <div>
                <input type="text" name="periode_trimestrielle[]" placeholder="Ex: 01/09/2025 - 31/12/2025">
                <input type="number" step="0.01" name="nb_heure_trimestriel[]" placeholder="Heures">
            </div>
        </div>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
