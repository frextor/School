@extends('layouts.app')

@section('title', 'Modifier classe')

@section('content')
    <a href="{{ route('referentiel.classes.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $classe->classe }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.classes.update', $classe) }}">
        @csrf
        @method('PUT')

        <label for="classe">Nom de la classe</label>
        <input type="text" name="classe" id="classe" value="{{ old('classe', $classe->classe) }}" required>

        <label for="id_niveau">Niveau</label>
        <select name="id_niveau" id="id_niveau" required>
            @foreach ($niveaux as $niveau)
                <option value="{{ $niveau->id_niveau }}" @selected(old('id_niveau', $classe->id_niveau) == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
            @endforeach
        </select>

        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" required>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $classe->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="couleur">Couleur</label>
        <input type="color" name="couleur" id="couleur" value="{{ old('couleur', $classe->couleur ?: '#cccccc') }}">

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
