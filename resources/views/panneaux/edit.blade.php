@extends('layouts.app')

@section('title', 'Modifier panneau')

@section('content')
    <a href="{{ route('panneaux.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $panneau->titre }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('panneaux.update', $panneau) }}">
        @csrf
        @method('PUT')

        <label for="identifiant_panneaux">Identifiant du panneau</label>
        <input type="text" name="identifiant_panneaux" id="identifiant_panneaux" value="{{ old('identifiant_panneaux', $panneau->identifiant_panneaux) }}" required>

        <label for="titre">Titre</label>
        <input type="text" name="titre" id="titre" value="{{ old('titre', $panneau->titre) }}" required>

        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" required>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $panneau->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="annee">Année</label>
        <input type="text" name="annee" id="annee" value="{{ old('annee', $panneau->annee) }}">

        <label for="plage_horaire">Plage horaire affichée (heures)</label>
        <input type="number" name="plage_horaire" id="plage_horaire" value="{{ old('plage_horaire', $panneau->plage_horaire) }}" required>

        <label for="delai_horaire">Délai de rafraîchissement (secondes)</label>
        <input type="number" name="delai_horaire" id="delai_horaire" value="{{ old('delai_horaire', $panneau->delai_horaire) }}" required>

        @php $formationsSelectionnees = old('formations', $panneau->formations->pluck('id_formation')->all()); @endphp
        <label for="formations">Formations</label>
        <select name="formations[]" id="formations" multiple size="6">
            @foreach ($formations as $formation)
                <option value="{{ $formation->id_formation }}" @selected(collect($formationsSelectionnees)->contains($formation->id_formation))>{{ $formation->niveau }}</option>
            @endforeach
        </select>

        @php $classesSelectionnees = old('classes', $panneau->classes->pluck('id_classe')->all()); @endphp
        <label for="classes">Classes</label>
        <select name="classes[]" id="classes" multiple size="6">
            @foreach ($classes as $classe)
                <option value="{{ $classe->id_classe }}" @selected(collect($classesSelectionnees)->contains($classe->id_classe))>{{ $classe->classe }}</option>
            @endforeach
        </select>

        @php $groupesSelectionnes = old('groupes', $panneau->groupes->pluck('id_groupe')->all()); @endphp
        <label for="groupes">Groupes</label>
        <select name="groupes[]" id="groupes" multiple size="6">
            @foreach ($groupes as $groupe)
                <option value="{{ $groupe->id_groupe }}" @selected(collect($groupesSelectionnes)->contains($groupe->id_groupe))>{{ $groupe->nom_groupe }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
