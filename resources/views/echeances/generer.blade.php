@extends('layouts.app')

@section('title', 'Générer un échéancier')

@section('content')
@php
    $mois = [
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
    ];
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <a href="{{ route('echeances.index') }}">Échéanciers</a>
    <span class="sep">/</span>
    <span class="current">Générer</span>
</div>

<div class="page-head">
    <div>
        <h1>Générer un échéancier</h1>
        <p class="page-sub">Frais d'inscription et mensualités, pour une classe entière ou un élève.</p>
    </div>
</div>

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

<form method="post" action="{{ route('echeances.generer.store') }}" class="gen-form">
    @csrf

    <section class="panel panel-pad">
        <h2 class="panel-title">Pour qui ?</h2>

        <div class="choice-grid">
            <label class="choice is-on">
                <input type="radio" name="cible" value="classe" checked data-cible>
                <span>
                    <span class="choice-label">Une classe entière</span>
                    <span class="choice-hint">Tous les élèves actifs, même tarif</span>
                </span>
            </label>
            <label class="choice">
                <input type="radio" name="cible" value="eleve" data-cible>
                <span>
                    <span class="choice-label">Un seul élève</span>
                    <span class="choice-hint">Tarif particulier, inscription en cours d'année</span>
                </span>
            </label>
        </div>

        <div class="pair" style="margin-top:16px">
            <label class="stack" data-champ="classe">
                <span>Classe</span>
                <select name="classe">
                    <option value="">-- Choisir --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id_classe }}" @selected($classeChoisie === $c->id_classe)>{{ $c->classe }}</option>
                    @endforeach
                </select>
            </label>
            <label class="stack" data-champ="eleve" hidden>
                <span>N° de l'élève</span>
                <input type="number" name="id_eleve" placeholder="Identifiant de l'élève">
            </label>
            <label class="stack">
                <span>Année scolaire</span>
                <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $annee) }}" placeholder="2026-2027" required>
            </label>
        </div>
    </section>

    <section class="panel panel-pad">
        <h2 class="panel-title">Montants</h2>

        <div class="pair">
            <label class="stack">
                <span>Frais d'inscription (laisser vide si aucun)</span>
                <input type="number" step="0.01" min="0" name="frais_inscription" value="{{ old('frais_inscription') }}" data-calc>
            </label>
            <label class="stack">
                <span>Date d'échéance des frais</span>
                <input type="date" name="date_inscription" value="{{ old('date_inscription') }}">
            </label>
        </div>

        <div class="pair">
            <label class="stack">
                <span>Montant d'une mensualité</span>
                <input type="number" step="0.01" min="0" name="montant_mensualite" value="{{ old('montant_mensualite') }}" required data-calc>
            </label>
            <label class="stack">
                <span>Nombre de mensualités</span>
                <input type="number" min="1" max="12" name="nb_mensualites" value="{{ old('nb_mensualites', 10) }}" required data-calc>
            </label>
            <label class="stack">
                <span>Premier mois</span>
                <select name="mois_debut" required>
                    @foreach ($mois as $num => $libelle)
                        <option value="{{ $num }}" @selected(old('mois_debut', 9) == $num)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </label>
            <label class="stack">
                <span>Jour d'échéance</span>
                <input type="number" min="1" max="28" name="jour_echeance" value="{{ old('jour_echeance', 5) }}" required>
            </label>
        </div>

        <div class="recap">
            <span class="recap-label">Total annuel par élève</span>
            <span class="recap-value" data-total>—</span>
        </div>

        <label class="check" style="margin-top:14px">
            <input type="checkbox" name="remplacer" value="1">
            Remplacer un échéancier existant
            <span class="check-hint">Les échéances déjà réglées, même partiellement, sont conservées.</span>
        </label>

        <div style="margin-top:16px;display:flex;gap:8px">
            <button type="submit" class="btn">Générer l'échéancier</button>
            <a href="{{ route('echeances.index') }}" class="btn btn-ghost">Annuler</a>
        </div>
    </section>
</form>

<style>
    .gen-form { max-width: 760px; display: flex; flex-direction: column; gap: 14px; }
    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; }
    .panel-pad { padding: 18px; }
    .panel-title { margin: 0 0 15px; font-size: 14px; font-weight: 700; }
    .choice-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px; }
    .choice {
        display: flex; align-items: flex-start; gap: 9px; padding: 11px 12px; margin: 0;
        border: 1px solid var(--border); border-radius: 11px; background: #fff; cursor: pointer; font-weight: 400;
    }
    .choice.is-on { border-color: #c3c6f5; background: #fafbff; }
    .choice input { width: 15px; height: 15px; margin: 2px 0 0; flex-shrink: 0; }
    .choice-label { display: block; font-size: 13px; font-weight: 600; }
    .choice-hint { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
    .pair { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
    .pair .stack { flex: 1 1 160px; margin: 0; }
    .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .stack input, .stack select { width: 100%; max-width: none; background: #fafbfd; }
    .recap {
        display: flex; align-items: baseline; gap: 10px; margin-top: 6px; padding: 14px 16px;
        background: var(--brand-light); border-radius: 11px;
    }
    .recap-label { font-size: 12.5px; font-weight: 600; color: var(--brand-deep); }
    .recap-value { margin-left: auto; font-size: 22px; font-weight: 700; color: var(--brand-deep); font-variant-numeric: tabular-nums; }
    .check { display: block; font-size: 13px; }
    .check-hint { display: block; font-size: 11.5px; color: var(--muted); margin-left: 23px; }
</style>

<script>
    (function () {
        // Bascule classe / élève
        document.querySelectorAll('[data-cible]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.choice').forEach(function (c) {
                    c.classList.toggle('is-on', c.querySelector('input').checked);
                });
                document.querySelector('[data-champ="classe"]').hidden = radio.value !== 'classe';
                document.querySelector('[data-champ="eleve"]').hidden = radio.value !== 'eleve';
            });
        });

        // Total annuel calculé en direct
        var euro = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'MAD' });
        var champs = document.querySelectorAll('[data-calc]');
        var total = document.querySelector('[data-total]');

        function calcul() {
            var form = document.querySelector('.gen-form');
            var inscription = parseFloat(form.frais_inscription.value) || 0;
            var mensualite = parseFloat(form.montant_mensualite.value) || 0;
            var nb = parseInt(form.nb_mensualites.value, 10) || 0;
            var somme = inscription + mensualite * nb;
            total.textContent = somme > 0 ? euro.format(somme) : '—';
        }

        champs.forEach(function (c) { c.addEventListener('input', calcul); });
        calcul();
    })();
</script>
@endsection
