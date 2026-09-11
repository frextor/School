@extends('layouts.app')

@section('title', 'Modifier signature')

@section('content')
    <a href="{{ route('referentiel.signatures.index') }}">&larr; Retour</a>
    <h1>Modifier la signature de {{ $signature->nom_directeur }}</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if ($signature->cheminFichier())
        <p><img src="{{ Storage::disk('public')->url($signature->cheminFichier()) }}" style="max-height:80px"></p>
    @endif

    <form method="post" action="{{ route('referentiel.signatures.update', $signature) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="civilite">Civilité</label>
        <select name="civilite" id="civilite" required>
            <option value="M" @selected(old('civilite', $signature->civilite) === 'M')>M</option>
            <option value="Mme" @selected(old('civilite', $signature->civilite) === 'Mme')>Mme</option>
        </select>

        <label for="nom_directeur">Nom du directeur</label>
        <input type="text" name="nom_directeur" id="nom_directeur" value="{{ old('nom_directeur', $signature->nom_directeur) }}" required>

        <label for="fonction">Fonction</label>
        <input type="text" name="fonction" id="fonction" value="{{ old('fonction', $signature->fonction) }}" required>

        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" required>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $signature->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="signature">Remplacer l'image de signature (jpg)</label>
        <input type="file" name="signature" id="signature">

        @if ($signature->cheminFichier())
            <label style="font-weight:normal"><input type="checkbox" name="supprimer_signature" value="1"> Supprimer l'image actuelle</label>
        @endif

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
