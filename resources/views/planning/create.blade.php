@extends('layouts.app')

@section('title', 'Nouveau créneau')

@section('content')
    <a href="{{ route('planning.index') }}">&larr; Retour</a>
    <h1>Nouveau créneau de planning</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('planning.store') }}">
        @csrf

        <label for="id_intervenant">Intervenant</label>
        <select name="id_intervenant" id="id_intervenant" required>
            @foreach ($intervenants as $intervenant)
                <option value="{{ $intervenant->id_intervenant }}" @selected(old('id_intervenant') == $intervenant->id_intervenant)>{{ $intervenant->nom }} {{ $intervenant->prenom }}</option>
            @endforeach
        </select>

        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" required>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement') == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="id_cours">Cours</label>
        <select name="id_cours" id="id_cours" required>
            @foreach ($cours as $cour)
                <option value="{{ $cour->id_cours }}" @selected(old('id_cours') == $cour->id_cours)>{{ $cour->nom_cours }}</option>
            @endforeach
        </select>

        <label for="id_classe">Classe</label>
        <select name="id_classe" id="id_classe">
            <option value="">--</option>
            @foreach ($classes as $classe)
                <option value="{{ $classe->id_classe }}" @selected(old('id_classe') == $classe->id_classe)>{{ $classe->classe }}</option>
            @endforeach
        </select>

        <label for="id_salle">Salle</label>
        <input type="text" name="id_salle" id="id_salle" value="{{ old('id_salle') }}">

        <label for="date_debut">Début</label>
        <input type="datetime-local" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" required>

        <label for="date_fin">Fin</label>
        <input type="datetime-local" name="date_fin" id="date_fin" value="{{ old('date_fin') }}" required>

        <label for="semestre">Semestre</label>
        <select name="semestre" id="semestre" required>
            <option value="1" @selected(old('semestre') == 1)>1</option>
            <option value="2" @selected(old('semestre') == 2)>2</option>
        </select>

        <label for="annee">Année</label>
        <input type="number" name="annee" id="annee" value="{{ old('annee', date('Y')) }}" required>

        <label for="annotation">Annotation</label>
        <textarea name="annotation" id="annotation" rows="3">{{ old('annotation') }}</textarea>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
