@extends('layouts.app')

@section('title', 'Modifier · '.$contact->nom_complet)

@section('content')
@php
    $civilite = old('civilite', $contact->civilite ?: 'M');
    $sexe = old('sexe', $contact->sexe);
    $ecolesChoisies = collect(old('etablissements', $contact->ecoles->pluck('etablissement')->all()));
@endphp

<div class="crumb">
    <a href="{{ route('contacts.index') }}">Familles &amp; prospects</a>
    <span class="sep">/</span>
    <a href="{{ route('contacts.show', $contact) }}">{{ $contact->nom_complet }}</a>
    <span class="sep">/</span>
    <span class="current">Modification</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>{{ $contact->nom_complet }}</h1>
        <p class="page-sub">{{ $contact->email ?: 'Aucune adresse e-mail' }}</p>
    </div>
</div>

<form method="post" action="{{ route('contacts.update', $contact) }}" class="form-page">
    @csrf
    @method('PUT')

    <section class="form-card">
        <div class="form-head"><h2>Identité</h2></div>
        <div class="form-grid">
            <div class="field">
                <span class="field-label">Civilité</span>
                <div class="seg-group">
                    @foreach (['M' => 'M.', 'Mme' => 'Mme', 'Melle' => 'Mlle'] as $valeur => $libelle)
                        <label class="seg-opt">
                            <input type="radio" name="civilite" value="{{ $valeur }}" @checked($civilite === $valeur) required>
                            <span>{{ $libelle }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <label class="field">
                <span>Nom</span>
                <input type="text" name="nom" required value="{{ old('nom', $contact->nom) }}">
            </label>

            <label class="field">
                <span>Prénom</span>
                <input type="text" name="prenom" required value="{{ old('prenom', $contact->prenom) }}">
            </label>

            <div class="field">
                <span class="field-label">Sexe</span>
                <div class="seg-group">
                    @foreach (['m' => 'Masculin', 'f' => 'Féminin'] as $valeur => $libelle)
                        <label class="seg-opt">
                            <input type="radio" name="sexe" value="{{ $valeur }}" @checked($sexe === $valeur)>
                            <span>{{ $libelle }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <label class="field">
                <span>Date de naissance</span>
                <input type="date" name="date_naissance" value="{{ old('date_naissance', $contact->date_naissance) }}">
            </label>

            <label class="field">
                <span>Lieu de naissance</span>
                <input type="text" name="lieu_naissance" maxlength="25" value="{{ old('lieu_naissance', $contact->lieu_naissance) }}">
            </label>

            <label class="field">
                <span>Pays de naissance</span>
                <input type="text" name="pays_naissance" maxlength="20" value="{{ old('pays_naissance', $contact->pays_naissance) }}">
            </label>

            <label class="field">
                <span>Nationalité</span>
                <input type="text" name="nationalite" maxlength="20" value="{{ old('nationalite', $contact->nationalite) }}">
            </label>
        </div>
    </section>

    <section class="form-card">
        <div class="form-head"><h2>Coordonnées</h2></div>
        <div class="form-grid">
            <label class="field">
                <span>Adresse e-mail</span>
                <input type="email" name="email" required value="{{ old('email', $contact->email) }}">
            </label>

            <label class="field">
                <span>Téléphone</span>
                <input type="text" name="telephone" value="{{ old('telephone', $contact->telephone) }}">
            </label>

            <label class="field field-full">
                <span>Adresse</span>
                <input type="text" name="adresse" value="{{ old('adresse', $contact->adresse) }}">
            </label>

            <label class="field">
                <span>Code postal</span>
                <input type="text" name="code_postal" value="{{ old('code_postal', $contact->code_postal) }}">
            </label>

            <label class="field">
                <span>Ville</span>
                <input type="text" name="ville" value="{{ old('ville', $contact->ville) }}">
            </label>

            <label class="field">
                <span>Pays</span>
                <input type="text" name="pays" maxlength="30" value="{{ old('pays', $contact->pays) }}">
            </label>
        </div>
    </section>

    <section class="form-card">
        <div class="form-head">
            <h2>Candidature</h2>
            <span class="form-sub">Ce que la famille vise, et par quel canal elle vous a connu.</span>
        </div>

        <div class="form-grid">
            <label class="field">
                <span>Cycle visé</span>
                <select name="id_formation">
                    <option value="">Non précisé</option>
                    @foreach ($formations as $formation)
                        <option value="{{ $formation->id_formation }}" @selected(old('id_formation', $contact->id_formation) == $formation->id_formation)>{{ $formation->niveau }}</option>
                    @endforeach
                </select>
            </label>

            <label class="field">
                <span>Inscrire à une réunion d'information</span>
                <select name="id_reunion_information">
                    <option value="">Aucune</option>
                    @foreach ($reunions as $reunion)
                        <option value="{{ $reunion->id_reunion_information }}">{{ $reunion->lieu }} — {{ $reunion->date->format('d/m/Y H:i') }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div class="form-body" style="padding-top:0">
            <span class="field-label">Établissements candidatés</span>
            <div class="pick-list">
                @foreach ($etablissements as $etablissement)
                    <label class="pick">
                        <input type="checkbox" name="etablissements[]" value="{{ $etablissement->nom_etablissement }}"
                               @checked($ecolesChoisies->contains($etablissement->nom_etablissement))>
                        <span>{{ $etablissement->nom_etablissement }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </section>

    <section class="form-card">
        <div class="form-head"><h2>Communication et suivi</h2></div>
        <div class="form-body">
            <label class="check-card">
                <input type="checkbox" name="newsletter" value="1" @checked(old('newsletter', $contact->newsletter))>
                <span>
                    <strong>Newsletter</strong>
                    La famille accepte de recevoir les actualités de l'école.
                </span>
            </label>

            <label class="check-card" style="margin-top:10px">
                <input type="checkbox" name="offres_partenaires" value="1" @checked(old('offres_partenaires', $contact->offres_partenaires))>
                <span>
                    <strong>Offres des partenaires</strong>
                    La famille accepte les propositions de nos partenaires.
                </span>
            </label>

            <label class="field" style="margin-top:16px">
                <span>Annotation</span>
                <textarea name="annotation" rows="4">{{ old('annotation', $contact->annotation) }}</textarea>
                <small class="field-aide">Visible sur la fiche, au-dessus du fil de suivi.</small>
            </label>
        </div>
    </section>

    <div class="form-actions">
        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-ghost">Retour à la fiche</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>
@endsection
