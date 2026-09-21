@extends('layouts.app')

@section('title', 'Modifier contact')

@section('content')
    <a href="{{ route('contacts.show', $contact) }}">&larr; Retour à la fiche</a>
    <h1>Modifier {{ $contact->nom_complet }}</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('contacts.update', $contact) }}">
        @csrf
        @method('PUT')

        <label for="civilite">Civilité</label>
        <select name="civilite" id="civilite" required>
            @foreach (['M', 'Mme', 'Melle'] as $civ)
                <option value="{{ $civ }}" @selected(old('civilite', $contact->civilite) === $civ)>{{ $civ }}</option>
            @endforeach
        </select>

        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="{{ old('nom', $contact->nom) }}" required>

        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $contact->prenom) }}" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $contact->email) }}" required>

        <label for="telephone">Téléphone</label>
        <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $contact->telephone) }}">

        <label for="sexe">Sexe</label>
        <select name="sexe" id="sexe">
            <option value="">—</option>
            <option value="m" @selected(old('sexe', $contact->sexe) === 'm')>Masculin</option>
            <option value="f" @selected(old('sexe', $contact->sexe) === 'f')>Féminin</option>
        </select>

        <label for="date_naissance">Date de naissance</label>
        <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance', $contact->date_naissance) }}">

        <label for="lieu_naissance">Lieu de naissance</label>
        <input type="text" name="lieu_naissance" id="lieu_naissance" maxlength="25" value="{{ old('lieu_naissance', $contact->lieu_naissance) }}">

        <label for="pays_naissance">Pays de naissance</label>
        <input type="text" name="pays_naissance" id="pays_naissance" maxlength="20" value="{{ old('pays_naissance', $contact->pays_naissance) }}">

        <label for="nationalite">Nationalité</label>
        <input type="text" name="nationalite" id="nationalite" maxlength="20" value="{{ old('nationalite', $contact->nationalite) }}">

        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $contact->adresse) }}">

        <label for="code_postal">Code postal</label>
        <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal', $contact->code_postal) }}">

        <label for="ville">Ville</label>
        <input type="text" name="ville" id="ville" value="{{ old('ville', $contact->ville) }}">

        <label for="pays">Pays</label>
        <input type="text" name="pays" id="pays" maxlength="30" value="{{ old('pays', $contact->pays) }}">

        <label for="id_formation">Formation</label>
        <select name="id_formation" id="id_formation">
            <option value="">--</option>
            @foreach ($formations as $formation)
                <option value="{{ $formation->id_formation }}" @selected(old('id_formation', $contact->id_formation) == $formation->id_formation)>{{ $formation->niveau }}</option>
            @endforeach
        </select>

        <label for="id_reunion_information">Réunion d'information</label>
        <select name="id_reunion_information" id="id_reunion_information">
            <option value="">--</option>
            @foreach ($reunions as $reunion)
                <option value="{{ $reunion->id_reunion_information }}">{{ $reunion->lieu }} — {{ $reunion->date->format('d/m/Y H:i') }}</option>
            @endforeach
        </select>

        <label><input type="checkbox" name="newsletter" value="1" @checked(old('newsletter', $contact->newsletter))> Newsletter</label>
        <label><input type="checkbox" name="offres_partenaires" value="1" @checked(old('offres_partenaires', $contact->offres_partenaires))> Offres partenaires</label>

        @php $etablissementsSelectionnes = old('etablissements', $contact->ecoles->pluck('etablissement')->all()); @endphp
        <label for="etablissements">Établissements candidatés</label>
        <select name="etablissements[]" id="etablissements" multiple size="6">
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->nom_etablissement }}" @selected(collect($etablissementsSelectionnes)->contains($etablissement->nom_etablissement))>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="annotation">Annotation</label>
        <textarea name="annotation" id="annotation" rows="4">{{ old('annotation', $contact->annotation) }}</textarea>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
