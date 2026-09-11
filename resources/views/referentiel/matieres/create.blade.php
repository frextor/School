@extends('layouts.app')

@section('title', 'Nouvelle matière')

@section('content')
    <a href="{{ route('referentiel.matieres.index') }}">&larr; Retour</a>
    <h1>Nouvelle matière</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.matieres.store') }}">
        @csrf

        <label for="code_matiere">Code</label>
        <input type="text" name="code_matiere" id="code_matiere" maxlength="5" value="{{ old('code_matiere') }}" required>

        <label for="nom_matiere">Nom</label>
        <input type="text" name="nom_matiere" id="nom_matiere" value="{{ old('nom_matiere') }}" required>

        <label for="id_unite_enseignement">Unité d'enseignement</label>
        <select name="id_unite_enseignement" id="id_unite_enseignement" required>
            @foreach ($unites as $unite)
                <option value="{{ $unite->id_unite_enseignement }}" @selected(old('id_unite_enseignement') == $unite->id_unite_enseignement)>{{ $unite->nom_unite_enseignement }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
