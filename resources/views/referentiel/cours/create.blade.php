@extends('layouts.app')

@section('title', 'Nouvelle matière')

@section('content')
    <a href="{{ route('referentiel.cours.index') }}">&larr; Retour</a>
    <h1>Nouvelle matière</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.cours.store') }}">
        @csrf

        <label for="code_cours">Code</label>
        <input type="text" name="code_cours" id="code_cours" value="{{ old('code_cours') }}" required>

        <label for="nom_cours">Nom</label>
        <input type="text" name="nom_cours" id="nom_cours" value="{{ old('nom_cours') }}" required>

        <label for="id_unite_enseignement">Unité d'enseignement</label>
        <select name="id_unite_enseignement" id="id_unite_enseignement" required>
            @foreach ($unites as $unite)
                <option value="{{ $unite->id_unite_enseignement }}" @selected(old('id_unite_enseignement') == $unite->id_unite_enseignement)>{{ $unite->nom_unite_enseignement }}</option>
            @endforeach
        </select>

        <label>Années concernées</label>
        @foreach ([1, 2, 3, 4, 5] as $annee)
            <label style="font-weight:normal">
                <input type="checkbox" name="annees[]" value="{{ $annee }}" @checked(collect(old('annees', []))->contains($annee))>
                Année {{ $annee }}
            </label>
        @endforeach

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
