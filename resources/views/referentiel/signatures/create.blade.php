@extends('layouts.app')

@section('title', 'Nouvelle signature')

@section('content')
    <a href="{{ route('referentiel.signatures.index') }}">&larr; Retour</a>
    <h1>Nouvelle signature</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.signatures.store') }}" enctype="multipart/form-data">
        @csrf

        <label for="civilite">Civilité</label>
        <select name="civilite" id="civilite" required>
            <option value="M" @selected(old('civilite') === 'M')>M</option>
            <option value="Mme" @selected(old('civilite') === 'Mme')>Mme</option>
        </select>

        <label for="nom_directeur">Nom du directeur</label>
        <input type="text" name="nom_directeur" id="nom_directeur" value="{{ old('nom_directeur') }}" required>

        <label for="fonction">Fonction</label>
        <input type="text" name="fonction" id="fonction" value="{{ old('fonction') }}" required>

        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" required>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement') == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="signature">Image de la signature (jpg)</label>
        <input type="file" name="signature" id="signature">

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
