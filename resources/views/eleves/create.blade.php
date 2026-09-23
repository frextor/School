@extends('layouts.app')

@section('title', 'Nouvel élève')

@section('content')
@php
    $civilite = old('civilite', 'M');
    $profilActuel = old('profil', 'eleve');
    $profils = [
        'eleve' => ['Élève', 'Inscrit et présent cette année'],
        'reinscrit' => ['Réinscrit', 'Renouvelle son inscription'],
        'alumni' => ['Ancien élève', 'A terminé sa scolarité'],
        'abandon' => ['Abandon', 'A quitté l\'établissement'],
    ];
@endphp

<div class="crumb">
    <a href="{{ route('eleves.index') }}">Élèves</a>
    <span class="sep">/</span>
    <span class="current">Nouvel élève</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouvel élève</h1>
        <p class="page-sub">Rattachez une fiche famille existante, ou créez-la au passage. Le reste de l'état civil se complète ensuite sur la fiche de l'élève.</p>
    </div>
</div>

<form method="post" action="{{ route('eleves.store') }}" class="form-page">
    @csrf

    <section class="form-card">
        <div class="form-head">
            <h2>La famille</h2>
            <span class="form-sub">Une fiche contact porte les coordonnées et sert d'identifiant.</span>
        </div>

        <div class="form-grid">
            <label class="field field-full">
                <span>Fiche contact</span>
                <select name="id_contact" data-contact>
                    <option value="">Créer une nouvelle fiche</option>
                    @foreach ($contacts as $contact)
                        <option value="{{ $contact->id_contact }}" @selected(old('id_contact') == $contact->id_contact)>{{ $contact->nom }} {{ $contact->prenom }} — {{ $contact->email }}</option>
                    @endforeach
                </select>
                <small class="field-aide">Si la famille est déjà connue du CRM, choisissez-la : l'élève lui sera rattaché.</small>
            </label>
        </div>

        {{-- Les champs de création n'ont de sens que si aucune fiche n'est
             choisie : le contrôleur les exige justement dans ce seul cas. --}}
        <div data-nouveau-contact @if(old('id_contact')) hidden @endif>
            <div class="form-grid" style="padding-top:0">
                <div class="field">
                    <span class="field-label">Civilité</span>
                    <div class="seg-group">
                        @foreach (['M' => 'M.', 'Mme' => 'Mme', 'Melle' => 'Mlle'] as $valeur => $libelle)
                            <label class="seg-opt">
                                <input type="radio" name="civilite" value="{{ $valeur }}" @checked($civilite === $valeur)>
                                <span>{{ $libelle }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <label class="field">
                    <span>Nom</span>
                    <input type="text" name="nom" maxlength="30" value="{{ old('nom') }}">
                </label>

                <label class="field">
                    <span>Prénom</span>
                    <input type="text" name="prenom" maxlength="30" value="{{ old('prenom') }}">
                </label>

                <label class="field">
                    <span>Adresse e-mail</span>
                    <input type="email" name="email" maxlength="60" value="{{ old('email') }}">
                </label>

                <label class="field">
                    <span>Téléphone</span>
                    <input type="text" name="telephone" maxlength="20" value="{{ old('telephone') }}">
                </label>
            </div>
        </div>
    </section>

    <section class="form-card">
        <div class="form-head"><h2>Scolarité</h2></div>

        <div class="form-grid">
            <label class="field">
                <span>Niveau</span>
                <select name="id_niveau" required data-niveau>
                    @foreach ($niveaux as $niveau)
                        <option value="{{ $niveau->id_niveau }}" @selected(old('id_niveau') == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
                    @endforeach
                </select>
            </label>

            <label class="field">
                <span>Classe</span>
                <select name="id_classe" data-classe>
                    <option value="">À affecter plus tard</option>
                    @foreach ($classes as $classe)
                        <option value="{{ $classe->id_classe }}" data-niveau="{{ $classe->id_niveau }}"
                                @selected(old('id_classe') == $classe->id_classe)>{{ $classe->classe }}</option>
                    @endforeach
                </select>
                <small class="field-aide">Seules les classes du niveau choisi sont proposées.</small>
            </label>

            <label class="field">
                <span>Date d'inscription</span>
                <input type="date" name="date_inscription" value="{{ old('date_inscription', date('Y-m-d')) }}">
            </label>

            <label class="field">
                <span>Montant de la scolarité (DH)</span>
                <input type="text" name="montant_formation" value="{{ old('montant_formation') }}">
            </label>
        </div>

        <div class="form-body" style="padding-top:0">
            <span class="field-label">Statut</span>
            <div class="pick-list">
                @foreach ($profils as $valeur => [$libelle, $aide])
                    <label class="seg-opt" title="{{ $aide }}">
                        <input type="radio" name="profil" value="{{ $valeur }}" @checked($profilActuel === $valeur) required>
                        <span>{{ $libelle }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </section>

    <div class="form-actions">
        <a href="{{ route('eleves.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer l'élève</button>
    </div>
</form>

<script>
    (function () {
        var contact = document.querySelector('[data-contact]');
        var bloc = document.querySelector('[data-nouveau-contact]');
        if (contact && bloc) {
            contact.addEventListener('change', function () {
                bloc.hidden = contact.value !== '';
            });
        }

        // La classe dépend du niveau : proposer les dix-sept classes de l'école
        // quand on vient de choisir « 1ère année primaire » n'aide personne.
        var niveau = document.querySelector('[data-niveau]');
        var classe = document.querySelector('[data-classe]');
        if (!niveau || !classe) return;

        var toutes = Array.prototype.slice.call(classe.options);

        function filtrer() {
            var courant = classe.value;
            classe.innerHTML = '';
            toutes.forEach(function (option) {
                if (!option.value || option.dataset.niveau === niveau.value) {
                    classe.appendChild(option);
                }
            });
            classe.value = Array.prototype.some.call(classe.options, function (o) { return o.value === courant; })
                ? courant
                : '';
        }

        niveau.addEventListener('change', filtrer);
        filtrer();
    })();
</script>
@endsection
