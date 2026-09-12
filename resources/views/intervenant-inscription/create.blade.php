@extends('layouts.app')

@section('title', 'Devenir intervenant')

@section('content')
<div class="auth-split">
    @include('auth._panel', [
        'panelTitle' => "Rejoignez notre réseau d'intervenants.",
        'panelTagline' => 'Enseignez dans nos établissements et gérez votre activité depuis un espace dédié.',
        'panelPoints' => [
            'Planning et classes en un coup d\'œil',
            'Récapitulatif d\'heures enseignées',
            'Suivi de vos compétences et diplômes',
        ],
    ])

    <div class="auth-form-col" style="align-items:flex-start;padding-top:40px;padding-bottom:40px">
        <div class="auth-form-wrap" style="max-width:560px">
            <div class="auth-chip">
                @include('partials.icon', ['n' => 'school', 's' => 12, 'w' => 2.2])Inscription intervenant
            </div>

            <h1>Créer mon compte</h1>
            <p class="auth-subtitle">Rejoignez l'espace intervenant en quelques minutes.</p>

            @if ($errors->any())
                <div class="status error">
                    @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
                    <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
                </div>
            @endif

            <form method="post" action="{{ route('intervenant-inscription.store') }}" enctype="multipart/form-data" class="auth-form">
                @csrf

                <div class="field">
                    <label for="civilite">Civilité</label>
                    <select name="civilite" id="civilite" required>
                        <option value="M" @selected(old('civilite') === 'M')>M</option>
                        <option value="Mme" @selected(old('civilite') === 'Mme')>Mme</option>
                    </select>
                </div>

                <div class="field">
                    <label for="nom">Nom</label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required>
                </div>

                <div class="field">
                    <label for="prenom">Prénom</label>
                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-icon">
                        @include('partials.icon', ['n' => 'user', 's' => 16, 'c' => '#9aa0b0', 'style' => 'position:absolute;left:12px;top:11px'])
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="input-icon">
                        @include('partials.icon', ['n' => 'lock', 's' => 16, 'c' => '#9aa0b0', 'style' => 'position:absolute;left:12px;top:11px'])
                        <input type="password" name="password" id="password" required minlength="8">
                    </div>
                </div>

                <div class="field">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance') }}" required>
                </div>

                <div class="field">
                    <label for="telephone">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}">
                </div>

                <div class="field">
                    <label for="mobile">Mobile</label>
                    <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}">
                </div>

                <div class="field">
                    <label for="adresse">Adresse</label>
                    <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}">
                </div>

                <div class="field">
                    <label for="code_postal">Code postal</label>
                    <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal') }}">
                </div>

                <div class="field">
                    <label for="ville">Ville</label>
                    <input type="text" name="ville" id="ville" value="{{ old('ville') }}">
                </div>

                <div class="field">
                    <label for="poste_actuel">Poste actuel</label>
                    <input type="text" name="poste_actuel" id="poste_actuel" value="{{ old('poste_actuel') }}">
                </div>

                <div class="field">
                    <label for="raison_sociale">Société (optionnel)</label>
                    <input type="text" name="raison_sociale" id="raison_sociale" value="{{ old('raison_sociale') }}">
                </div>

                <div class="field">
                    <label for="cours">Cours que vous souhaitez enseigner</label>
                    <select name="cours[]" id="cours" multiple size="6">
                        @foreach ($cours as $c)
                            <option value="{{ $c->id_cours }}">{{ $c->nom_cours }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="etablissements">Établissements</label>
                    <select name="etablissements[]" id="etablissements" multiple size="4">
                        @foreach ($etablissements as $etablissement)
                            <option value="{{ $etablissement->id_etablissement }}">{{ $etablissement->nom_etablissement }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="cv">CV (pdf/doc)</label>
                    <input type="file" name="cv" id="cv">
                </div>

                <div class="field">
                    <label for="photo">Photo</label>
                    <input type="file" name="photo" id="photo">
                </div>

                <button type="submit" class="btn btn-block">
                    Créer mon compte @include('partials.icon', ['n' => 'arrow-right', 's' => 16, 'c' => '#fff', 'w' => 2.2])
                </button>
            </form>

            <a href="{{ route('intervenant.login') }}" class="auth-back">
                @include('partials.icon', ['n' => 'arrow-left', 's' => 14, 'w' => 2])Déjà un compte ? Se connecter
            </a>
        </div>
    </div>
</div>
@endsection
