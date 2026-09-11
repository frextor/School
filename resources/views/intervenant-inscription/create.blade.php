@extends('layouts.app')

@section('title', 'Devenir intervenant')

@section('content')
    <h1>Inscription intervenant</h1>
    <p style="color:var(--muted)">Créez votre compte de l'espace intervenant.</p>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('intervenant-inscription.store') }}" enctype="multipart/form-data">
        @csrf

        <label for="civilite">Civilité</label>
        <select name="civilite" id="civilite" required>
            <option value="M" @selected(old('civilite') === 'M')>M</option>
            <option value="Mme" @selected(old('civilite') === 'Mme')>Mme</option>
        </select>

        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required>

        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>

        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" required minlength="8">

        <label for="date_naissance">Date de naissance</label>
        <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance') }}" required>

        <label for="telephone">Téléphone</label>
        <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}">

        <label for="mobile">Mobile</label>
        <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}">

        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}">

        <label for="code_postal">Code postal</label>
        <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal') }}">

        <label for="ville">Ville</label>
        <input type="text" name="ville" id="ville" value="{{ old('ville') }}">

        <label for="poste_actuel">Poste actuel</label>
        <input type="text" name="poste_actuel" id="poste_actuel" value="{{ old('poste_actuel') }}">

        <label for="raison_sociale">Société (optionnel)</label>
        <input type="text" name="raison_sociale" id="raison_sociale" value="{{ old('raison_sociale') }}">

        <label for="cours">Cours que vous souhaitez enseigner</label>
        <select name="cours[]" id="cours" multiple size="6">
            @foreach ($cours as $c)
                <option value="{{ $c->id_cours }}">{{ $c->nom_cours }}</option>
            @endforeach
        </select>

        <label for="etablissements">Établissements</label>
        <select name="etablissements[]" id="etablissements" multiple size="4">
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}">{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="cv">CV (pdf/doc)</label>
        <input type="file" name="cv" id="cv">

        <label for="photo">Photo</label>
        <input type="file" name="photo" id="photo">

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer mon compte</button>
        </p>
    </form>
@endsection
