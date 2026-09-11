@extends('layouts.app')

@section('title', 'Nouvel élève')

@section('content')
    <a href="{{ route('eleves.index') }}">&larr; Retour à la liste</a>
    <h1>Nouvel élève</h1>

    @if ($errors->any())
        <div class="status error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('eleves.store') }}">
        @csrf

        <label for="id_contact">Fiche contact</label>
        <select name="id_contact" id="id_contact" onchange="document.getElementById('nouveau-contact').hidden = this.value !== ''">
            <option value="">— Créer une nouvelle fiche contact —</option>
            @foreach ($contacts as $contact)
                <option value="{{ $contact->id_contact }}" @selected(old('id_contact') == $contact->id_contact)>
                    {{ $contact->nom }} {{ $contact->prenom }} ({{ $contact->email }})
                </option>
            @endforeach
        </select>
        <p style="margin:0.3rem 0 0;color:var(--muted);font-size:0.8rem">
            Choisissez une fiche contact déjà existante, ou laissez sur « Créer une nouvelle fiche contact » pour la générer automatiquement à partir des informations ci-dessous.
        </p>

        <div id="nouveau-contact" @if(old('id_contact')) hidden @endif>
            <label for="civilite">Civilité</label>
            <select name="civilite" id="civilite">
                <option value="M" @selected(old('civilite') === 'M')>M</option>
                <option value="Mme" @selected(old('civilite') === 'Mme')>Mme</option>
                <option value="Melle" @selected(old('civilite') === 'Melle')>Melle</option>
            </select>

            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="{{ old('nom') }}">

            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}">

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}">

            <label for="telephone">Téléphone</label>
            <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}">
        </div>

        <label for="id_niveau">Niveau</label>
        <select name="id_niveau" id="id_niveau" required>
            @foreach ($niveaux as $niveau)
                <option value="{{ $niveau->id_niveau }}" @selected(old('id_niveau') == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
            @endforeach
        </select>

        <label for="id_classe">Classe</label>
        <select name="id_classe" id="id_classe">
            <option value="">--</option>
            @foreach ($classes as $classe)
                <option value="{{ $classe->id_classe }}" @selected(old('id_classe') == $classe->id_classe)>{{ $classe->classe }}</option>
            @endforeach
        </select>

        <label for="profil">Profil</label>
        <select name="profil" id="profil" required>
            @foreach (['eleve', 'alumni', 'reinscrit', 'abandon'] as $profil)
                <option value="{{ $profil }}" @selected(old('profil', 'eleve') === $profil)>{{ ucfirst($profil) }}</option>
            @endforeach
        </select>

        <label for="date_inscription">Date d'inscription</label>
        <input type="date" name="date_inscription" id="date_inscription" value="{{ old('date_inscription') }}">

        <label for="montant_formation">Montant formation</label>
        <input type="text" name="montant_formation" id="montant_formation" value="{{ old('montant_formation') }}">

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
