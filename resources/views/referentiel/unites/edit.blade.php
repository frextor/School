@extends('layouts.app')

@section('title', 'Modifier UE')

@section('content')
    <a href="{{ route('referentiel.unites.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $unite->nom_unite_enseignement }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @php $anneesSelectionnees = old('annees', $unite->annees->pluck('annee')->all()); @endphp

    <form method="post" action="{{ route('referentiel.unites.update', $unite) }}">
        @csrf
        @method('PUT')

        <label for="code_unite">Code</label>
        <input type="text" name="code_unite" id="code_unite" value="{{ old('code_unite', $unite->code_unite) }}" required>

        <label for="nom_unite_enseignement">Nom</label>
        <input type="text" name="nom_unite_enseignement" id="nom_unite_enseignement" value="{{ old('nom_unite_enseignement', $unite->nom_unite_enseignement) }}" required>

        <label for="id_niveau">Niveau</label>
        <select name="id_niveau" id="id_niveau" required>
            @foreach ($niveaux as $niveau)
                <option value="{{ $niveau->id_niveau }}" @selected(old('id_niveau', $unite->id_niveau) == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
            @endforeach
        </select>

        <label for="couleur">Couleur</label>
        <input type="color" name="couleur" id="couleur" value="{{ old('couleur', $unite->couleur ?: '#cccccc') }}">

        <label>Années concernées</label>
        @foreach ([1, 2, 3, 4, 5] as $annee)
            <label style="font-weight:normal">
                <input type="checkbox" name="annees[]" value="{{ $annee }}" @checked(collect($anneesSelectionnees)->contains($annee))>
                Année {{ $annee }}
            </label>
        @endforeach

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
