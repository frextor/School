@extends('layouts.app')

@section('title', 'Modifier '.($eleve->contact?->nom_complet ?? 'élève'))

@section('content')
@php
    $contact = $eleve->contact;
    $v = fn ($champ, $valeur) => old($champ, $valeur);
@endphp

<div class="crumb">
    <a href="{{ route('eleves.index') }}">Élèves</a>
    <span class="sep">/</span>
    <a href="{{ route('eleves.show', $eleve) }}">{{ $contact?->nom_complet ?? 'Élève' }}</a>
    <span class="sep">/</span>
    <span class="current">Modifier</span>
</div>

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

<div class="page-head">
    <div>
        <h1>Modifier la fiche</h1>
        <p class="page-sub">
            {{ $contact?->nom_complet ?? 'Élève n°'.$eleve->id_eleve }}
            @if ($eleve->classe?->classe) · classe {{ $eleve->classe->classe }} @endif
            · ID {{ $eleve->id_eleve }}
        </p>
    </div>
    <div class="page-actions">
        <a class="btn btn-ghost" href="{{ route('eleves.show', $eleve) }}">Annuler</a>
    </div>
</div>

<form method="post" action="{{ route('eleves.update', $eleve) }}" class="ed-form">
    @csrf
    @method('PUT')

    <section class="panel ed-bloc">
        <div class="panel-head"><h2>Identité</h2></div>
        <div class="ed-grid">
            <label class="stack">
                <span>Civilité</span>
                <select name="civilite">
                    <option value="">—</option>
                    @foreach (['M' => 'M.', 'Mme' => 'Mme', 'Melle' => 'Melle'] as $code => $libelle)
                        <option value="{{ $code }}" @selected($v('civilite', $contact?->civilite) === $code)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </label>

            <label class="stack">
                <span>Nom</span>
                <input type="text" name="nom" maxlength="30" value="{{ $v('nom', $contact?->nom) }}" required>
            </label>

            <label class="stack">
                <span>Prénom</span>
                <input type="text" name="prenom" maxlength="30" value="{{ $v('prenom', $contact?->prenom) }}" required>
            </label>

            <label class="stack">
                <span>Sexe</span>
                <select name="sexe">
                    <option value="">—</option>
                    <option value="m" @selected($v('sexe', $contact?->sexe) === 'm')>Masculin</option>
                    <option value="f" @selected($v('sexe', $contact?->sexe) === 'f')>Féminin</option>
                </select>
            </label>

            <label class="stack">
                <span>Date de naissance</span>
                <input type="date" name="date_naissance" value="{{ $v('date_naissance', $contact?->date_naissance) }}">
            </label>

            <label class="stack">
                <span>Lieu de naissance</span>
                <input type="text" name="lieu_naissance" maxlength="25" value="{{ $v('lieu_naissance', $contact?->lieu_naissance) }}">
            </label>

            <label class="stack">
                <span>Pays de naissance</span>
                <input type="text" name="pays_naissance" maxlength="20" value="{{ $v('pays_naissance', $contact?->pays_naissance) }}">
            </label>

            <label class="stack">
                <span>Nationalité</span>
                <input type="text" name="nationalite" maxlength="20" value="{{ $v('nationalite', $contact?->nationalite) }}">
            </label>
        </div>
    </section>

    <section class="panel ed-bloc">
        <div class="panel-head"><h2>Coordonnées</h2></div>
        <div class="ed-grid">
            <label class="stack ed-large">
                <span>Email</span>
                <input type="email" name="email" maxlength="60" value="{{ $v('email', $contact?->email) }}">
            </label>

            <label class="stack">
                <span>Téléphone</span>
                <input type="text" name="telephone" maxlength="20" value="{{ $v('telephone', $contact?->telephone) }}">
            </label>

            <label class="stack ed-large">
                <span>Adresse</span>
                <input type="text" name="adresse" maxlength="100" value="{{ $v('adresse', $contact?->adresse) }}">
            </label>

            <label class="stack">
                <span>Code postal</span>
                <input type="text" name="code_postal" maxlength="10" value="{{ $v('code_postal', $contact?->code_postal) }}">
            </label>

            <label class="stack">
                <span>Ville</span>
                <input type="text" name="ville" maxlength="30" value="{{ $v('ville', $contact?->ville) }}">
            </label>

            <label class="stack">
                <span>Pays</span>
                <input type="text" name="pays" maxlength="30" value="{{ $v('pays', $contact?->pays) }}">
            </label>
        </div>
    </section>

    <section class="panel ed-bloc">
        <div class="panel-head"><h2>Scolarité</h2></div>
        <div class="ed-grid">
            <label class="stack">
                <span>Niveau</span>
                <select name="id_niveau" data-niveau required>
                    @foreach ($niveaux as $niveau)
                        <option value="{{ $niveau->id_niveau }}" @selected($v('id_niveau', $eleve->id_niveau) == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
                    @endforeach
                </select>
            </label>

            <label class="stack">
                <span>Classe</span>
                {{-- Filtrée par niveau : proposer une classe d'un autre niveau
                     n'aurait aucun sens et fausserait bulletins et emploi du temps. --}}
                <select name="id_classe" data-classe data-selected="{{ $v('id_classe', $eleve->id_classe) }}">
                    <option value="">— Aucune —</option>
                </select>
            </label>

            <label class="stack">
                <span>Profil</span>
                <select name="profil" required>
                    @foreach (['eleve' => 'Élève', 'reinscrit' => 'Réinscrit', 'alumni' => 'Ancien élève', 'abandon' => 'Abandon'] as $code => $libelle)
                        <option value="{{ $code }}" @selected($v('profil', $eleve->profil) === $code)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </label>

            <label class="stack">
                <span>Date d'inscription</span>
                <input type="date" name="date_inscription" value="{{ $v('date_inscription', $eleve->date_inscription?->format('Y-m-d')) }}">
            </label>

            <label class="stack">
                <span>Numéro Massar</span>
                <input type="text" name="numero_massar" maxlength="20" value="{{ $v('numero_massar', $eleve->numero_massar) }}">
            </label>

            <label class="stack">
                <span>Numéro social</span>
                <input type="text" name="numero_social" maxlength="20" value="{{ $v('numero_social', $eleve->numero_social) }}">
            </label>

            <label class="stack">
                <span>Langue maternelle</span>
                <input type="text" name="lang_maternelle" maxlength="20" value="{{ $v('lang_maternelle', $eleve->lang_maternelle) }}">
            </label>

            <label class="stack">
                <span>Frais de scolarité</span>
                <input type="text" name="montant_formation" maxlength="20" value="{{ $v('montant_formation', $eleve->montant_formation) }}"
                       placeholder="Repris de l'échéancier si vide">
            </label>
        </div>

        <div class="ed-cases">
            <label class="check">
                <input type="checkbox" name="valide" value="1" @checked($v('valide', $eleve->valide))>
                <span>
                    <span class="check-title">Dossier validé</span>
                    <span class="check-sub">L'inscription est confirmée par l'administration.</span>
                </span>
            </label>
            <label class="check">
                <input type="checkbox" name="visible" value="1" @checked($v('visible', $eleve->visible))>
                <span>
                    <span class="check-title">Fiche visible</span>
                    <span class="check-sub">Décocher archive l'élève : il sort des listes actives.</span>
                </span>
            </label>
        </div>
    </section>

    <section class="panel ed-bloc">
        <div class="panel-head"><h2>Commentaire</h2></div>
        <div class="ed-pad">
            <textarea name="commentaire" rows="3" placeholder="Note interne visible par l'administration…">{{ $v('commentaire', $eleve->commentaire) }}</textarea>
        </div>
    </section>

    <div class="ed-pied">
        <a href="{{ route('eleves.show', $eleve) }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<style>
    /* Modification d'une fiche élève : identité, coordonnées et scolarité
       dans un seul écran — l'état civil obligeait jusqu'ici à passer par la
       fiche contact. */
    .ed-form { max-width: 1000px; margin: 0; }
    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-head { padding: 14px 16px 12px; border-bottom: 1px solid var(--border-soft); }
    .panel-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .ed-bloc + .ed-bloc { margin-top: 14px; }

    .ed-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 13px; padding: 16px; }
    .ed-pad { padding: 16px; }
    .ed-form .stack { display: block; margin: 0; min-width: 0; }
    .ed-form .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .ed-form input, .ed-form select, .ed-form textarea { width: 100%; max-width: none; }
    .ed-large { grid-column: span 2; }

    .ed-cases { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 11px; padding: 0 16px 16px; }
    .check {
        display: flex; align-items: center; gap: 10px; margin: 0; padding: 11px 13px;
        border: 1px solid var(--border); border-radius: 11px; background: #fafbfd; cursor: pointer;
    }
    .check input { width: 16px; height: 16px; max-width: none; margin: 0; accent-color: var(--brand); flex-shrink: 0; cursor: pointer; }
    .check-title { display: block; font-size: 13px; font-weight: 600; }
    .check-sub { display: block; font-size: 11.5px; color: var(--muted); }

    .ed-pied { display: flex; align-items: center; gap: 10px; margin-top: 16px; }
    .ed-pied .btn:last-child { margin-left: auto; }

    @media (max-width: 620px) {
        .ed-large { grid-column: span 1; }
    }
</style>

<script>
    (function () {
        var classes = @json($classes->map(fn ($c) => ['id' => $c->id_classe, 'nom' => $c->classe, 'niveau' => (int) $c->id_niveau])->values());
        var niveau = document.querySelector('[data-niveau]');
        var classe = document.querySelector('[data-classe]');
        if (!niveau || !classe) return;

        function remplir() {
            var id = parseInt(niveau.value, 10) || 0;
            var choisie = classe.dataset.selected || '';
            var options = classes.filter(function (c) { return c.niveau === id; });

            classe.innerHTML = '<option value="">— Aucune —</option>' + options.map(function (c) {
                var sel = String(c.id) === String(choisie) ? ' selected' : '';
                return '<option value="' + c.id + '"' + sel + '>' + c.nom + '</option>';
            }).join('');
        }

        niveau.addEventListener('change', function () {
            classe.dataset.selected = '';
            remplir();
        });

        remplir();
    })();
</script>
@endsection
