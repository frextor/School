@extends('layouts.app')

@section('title', 'Modifier créneau')

@section('content')
    <a href="{{ route('planning.index') }}">&larr; Retour</a>
    <h1>Modifier le créneau</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('planning.update', $creneau) }}">
        @csrf
        @method('PUT')

        <label for="id_intervenant">Intervenant</label>
        <select name="id_intervenant" id="id_intervenant" required>
            @foreach ($intervenants as $intervenant)
                <option value="{{ $intervenant->id_intervenant }}" @selected(old('id_intervenant', $creneau->id_intervenant) == $intervenant->id_intervenant)>{{ $intervenant->nom }} {{ $intervenant->prenom }}</option>
            @endforeach
        </select>

        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" required>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $creneau->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="id_cours">Cours</label>
        <select name="id_cours" id="id_cours" required>
            @foreach ($cours as $cour)
                <option value="{{ $cour->id_cours }}" @selected(old('id_cours', $creneau->id_cours) == $cour->id_cours)>{{ $cour->nom_cours }}</option>
            @endforeach
        </select>

        <label for="id_classe">Classe</label>
        <select name="id_classe" id="id_classe">
            <option value="">--</option>
            @foreach ($classes as $classe)
                <option value="{{ $classe->id_classe }}" @selected(old('id_classe', $creneau->id_classe) == $classe->id_classe)>{{ $classe->classe }}</option>
            @endforeach
        </select>

        <label for="id_salle">Salle</label>
        <input type="text" name="id_salle" id="id_salle" value="{{ old('id_salle', $creneau->id_salle) }}">

        <label for="date_debut">Début</label>
        <input type="datetime-local" name="date_debut" id="date_debut" value="{{ old('date_debut', $creneau->date_debut->format('Y-m-d\TH:i')) }}" required>

        <label for="date_fin">Fin</label>
        <input type="datetime-local" name="date_fin" id="date_fin" value="{{ old('date_fin', $creneau->date_fin->format('Y-m-d\TH:i')) }}" required>

        <label for="semestre">Semestre</label>
        <select name="semestre" id="semestre" required>
            <option value="1" @selected(old('semestre', $creneau->semestre) == 1)>1</option>
            <option value="2" @selected(old('semestre', $creneau->semestre) == 2)>2</option>
        </select>

        <label for="annee">Année</label>
        <input type="number" name="annee" id="annee" value="{{ old('annee', $creneau->annee) }}" required>

        <label for="annotation">Annotation</label>
        <textarea name="annotation" id="annotation" rows="3">{{ old('annotation', $creneau->annotation) }}</textarea>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
