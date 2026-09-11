@extends('layouts.app')

@section('title', 'Nouvelle entreprise')

@section('content')
    <a href="{{ route('entreprises.index') }}">&larr; Retour</a>
    <h1>Nouvelle entreprise</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('entreprises.store') }}">
        @csrf

        <label for="nom_entreprise">Nom</label>
        <input type="text" name="nom_entreprise" id="nom_entreprise" value="{{ old('nom_entreprise') }}" required>

        <label for="type_entreprise">Type</label>
        <select name="type_entreprise" id="type_entreprise">
            <option value="Entreprise">Entreprise</option>
            <option value="Entreprise mère">Entreprise mère</option>
            <option value="OPCO">OPCO</option>
        </select>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>

        <label for="telephone">Téléphone</label>
        <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}" required>

        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}">

        <label for="code_postal">Code postal</label>
        <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal') }}">

        <label for="ville">Ville</label>
        <input type="text" name="ville" id="ville" value="{{ old('ville') }}">

        <label for="site_web">Site web</label>
        <input type="url" name="site_web" id="site_web" value="{{ old('site_web') }}">

        <label for="siret">SIRET</label>
        <input type="text" name="siret" id="siret" value="{{ old('siret') }}">

        <label for="id_secteur">Secteur d'activité</label>
        <select name="id_secteur" id="id_secteur">
            <option value="">--</option>
            @foreach ($secteurs as $secteur)
                <option value="{{ $secteur->id_entreprises_secteurs_activites }}" @selected(old('id_secteur') == $secteur->id_entreprises_secteurs_activites)>{{ $secteur->nom_secteur }}</option>
            @endforeach
        </select>

        <label for="id_etablissement">Établissement (campus référent)</label>
        <select name="id_etablissement" id="id_etablissement">
            <option value="">--</option>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement') == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="id_parent">Entreprise mère (si filiale)</label>
        <select name="id_parent" id="id_parent">
            <option value="">--</option>
            @foreach ($entreprisesMeres as $mere)
                <option value="{{ $mere->id_entreprise }}" @selected(old('id_parent') == $mere->id_entreprise)>{{ $mere->nom_entreprise }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
