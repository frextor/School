@extends('layouts.app')

@section('title', 'Nouveau règlement')

@section('content')
@php
    $contact = $eleve->contact;
    $statuts = [
        'paye' => ['Payé', 'Encaissé sur le compte'],
        'accord_opco' => ['Accord OPCO', 'Prise en charge validée'],
        'cas_particulier' => ['Cas particulier', 'Échéancier négocié'],
    ];
    $modes = [
        'CB' => ['Carte bancaire', 'card'],
        'CHEQUE' => ['Chèque', 'file'],
        'VIREMENT' => ['Virement', 'arrow-right'],
        '' => ['Non précisé', 'alert'],
    ];
    $statutActuel = old('statut_paiement', 'paye');
    $modeActuel = old('mode_paiement', 'VIREMENT');
    $montantFormation = (float) ($eleve->montant_formation ?: 0);
    $euro = fn ($v) => number_format((float) $v, 2, ',', ' ').' €';
@endphp

<div class="crumb">
    <a href="{{ route('eleves.index') }}">Élèves</a>
    <span class="sep">/</span>
    <a href="{{ route('eleves.show', $eleve) }}">{{ $contact?->nom_complet }}</a>
    <span class="sep">/</span>
    <a href="{{ route('paiements.index', $eleve) }}">Règlements</a>
    <span class="sep">/</span>
    <span class="current">Nouveau</span>
</div>

<div class="page-head">
    <div>
        <h1>Nouveau règlement</h1>
        <p class="page-sub">
            {{ $contact?->nom_complet }}@if ($eleve->niveau) — {{ $eleve->niveau->nom_niveau }} @endif
            @if ($eleve->classe), {{ $eleve->classe->classe }} @endif
            @if ($eleve->etablissement) · {{ $eleve->etablissement->nom_etablissement }} @endif
        </p>
    </div>
</div>

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

<form method="post" action="{{ route('paiements.store', $eleve) }}" class="pay-form" id="paiement-form">
    @csrf
    <div class="pay-grid">
        <div class="pay-main">

            {{-- Informations --}}
            <section class="panel panel-pad">
                <h2 class="panel-title">Informations du règlement</h2>

                <label class="stack">
                    <span>Titre</span>
                    <input type="text" name="titre" value="{{ old('titre') }}" required
                           placeholder="ex. Solde 2e échéance — {{ date('Y') }}/{{ date('Y') + 1 }}">
                </label>

                <div class="stack">
                    <span>Statut</span>
                    <div class="choice-grid">
                        @foreach ($statuts as $valeur => [$libelle, $aide])
                            <label class="choice {{ $statutActuel === $valeur ? 'is-on' : '' }}">
                                <input type="radio" name="statut_paiement" value="{{ $valeur }}"
                                       @checked($statutActuel === $valeur) required>
                                <span>
                                    <span class="choice-label">{{ $libelle }}</span>
                                    <span class="choice-hint">{{ $aide }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="stack">
                    <span>Mode de paiement</span>
                    <div class="chips-row">
                        @foreach ($modes as $valeur => [$libelle, $ico])
                            <label class="mode-chip {{ (string) $modeActuel === (string) $valeur ? 'is-on' : '' }}">
                                <input type="radio" name="mode_paiement" value="{{ $valeur }}"
                                       @checked((string) $modeActuel === (string) $valeur)>
                                @include('partials.icon', ['n' => $ico, 's' => 15]){{ $libelle }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pair">
                    <label class="stack">
                        <span>Année de rentrée</span>
                        <input type="number" name="annee_rentree" value="{{ old('annee_rentree', date('Y')) }}">
                    </label>
                    <label class="stack">
                        <span>Date du règlement</span>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}">
                    </label>
                </div>

                <label class="stack">
                    <span>Commentaire</span>
                    <textarea name="commentaire" rows="3"
                              placeholder="Référence de chèque, accord OPCO, remise exceptionnelle…">{{ old('commentaire') }}</textarea>
                </label>
            </section>

            {{-- Options facturables --}}
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2>Options facturables</h2>
                        <p class="panel-sub">Le montant proposé vient du catalogue du niveau. Il reste modifiable sur ce règlement.</p>
                    </div>
                    @if ($optionsDisponibles->isNotEmpty())
                        <button type="button" class="btn btn-ghost" data-toggle-all>Tout cocher</button>
                    @endif
                </div>

                @if ($optionsDisponibles->isNotEmpty())
                    <div class="opt-list">
                        @foreach ($optionsDisponibles as $option)
                            @php $coche = collect(old('options', []))->contains($option->id_niveau_option); @endphp
                            <label class="opt {{ $coche ? 'is-on' : '' }}">
                                <input type="checkbox" name="options[]" value="{{ $option->id_niveau_option }}"
                                       data-opt-check @checked($coche)>
                                <span class="opt-text">
                                    <span class="opt-title">{{ $option->titre }}</span>
                                    <span class="opt-cat">Catalogue : {{ $euro($option->montant) }}</span>
                                </span>
                                <span class="opt-amount">
                                    <input type="number" step="0.01" data-opt-amount
                                           name="montant_option[{{ $option->id_niveau_option }}]"
                                           value="{{ old('montant_option.'.$option->id_niveau_option, $option->montant) }}"
                                           @disabled(! $coche)>
                                    <span class="opt-euro">€</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="opt-empty">
                        Aucune option facturable définie pour le niveau de cet élève
                        (configurable depuis <a href="{{ route('referentiel.niveaux.index') }}">Référentiel &rsaquo; Niveaux</a>).
                    </p>
                @endif
            </section>
        </div>

        {{-- Récapitulatif --}}
        <div class="pay-side">
            <section class="panel is-sticky">
                <div class="sum-head">
                    <div class="field-label">Montant du règlement</div>
                    <div class="sum-total">
                        <span data-total>0,00 €</span>
                        <span class="sum-hint" data-total-hint>aucune option</span>
                    </div>
                </div>

                <div class="sum-lines" data-recap></div>
                <p class="sum-empty" data-recap-empty>Cochez une option pour composer le montant.</p>

                <div class="sum-foot">
                    <button type="submit" class="btn btn-block">
                        Créer le règlement
                        @include('partials.icon', ['n' => 'arrow-right', 's' => 16, 'c' => '#fff', 'w' => 2.2])
                    </button>
                    <a href="{{ route('paiements.index', $eleve) }}" class="sum-cancel">Annuler</a>
                </div>
            </section>

            <section class="panel panel-pad">
                <div class="field-label" style="margin-bottom:10px">Situation de l'élève</div>
                <div class="sit">
                    <div class="sit-row">
                        <span>Montant de la formation</span>
                        <span class="sit-value">{{ $montantFormation > 0 ? $euro($montantFormation) : '—' }}</span>
                    </div>
                    @isset($dejaEncaisse)
                        <div class="sit-row">
                            <span>Déjà encaissé</span>
                            <span class="sit-value is-ok">{{ $euro($dejaEncaisse) }}</span>
                        </div>
                        <div class="sit-row">
                            <span>Restant dû</span>
                            <span class="sit-value is-warn">{{ $euro(max(0, $montantFormation - $dejaEncaisse)) }}</span>
                        </div>
                    @endisset
                </div>
                <a href="{{ route('paiements.index', $eleve) }}" class="sit-link">
                    Voir tous les règlements
                    @include('partials.icon', ['n' => 'chevron-right', 's' => 14, 'w' => 2.2, 'style' => 'margin-left:auto'])
                </a>
            </section>
        </div>
    </div>
</form>

<style>
    /* Nouveau règlement : styles spécifiques (le reste vient de layouts/app.blade.php) */
    .pay-form { max-width: none; }
    .pay-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(330px, 1fr)); gap: 14px; align-items: start; }
    .pay-main, .pay-side { display: flex; flex-direction: column; gap: 14px; min-width: 0; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-pad { padding: 18px; }
    .panel-title { margin: 0 0 15px; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 15px 18px 13px; border-bottom: 1px solid var(--border-soft); }
    .panel-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head .panel-sub { margin: 4px 0 0; font-size: 12.5px; color: var(--muted); max-width: 60ch; text-wrap: pretty; }
    .panel-head .btn { margin-left: auto; white-space: nowrap; }

    .stack { display: block; margin: 0 0 16px; }
    .stack:last-child { margin-bottom: 0; }
    .stack > span:first-child { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
    .stack input, .stack textarea { width: 100%; max-width: none; background: #fafbfd; }
    .pair { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .pair .stack { margin: 0; }

    .choice-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px; }
    .choice {
        display: flex; align-items: flex-start; gap: 9px; padding: 11px 12px; margin: 0;
        border: 1px solid var(--border); border-radius: 11px; background: #fff; cursor: pointer; min-width: 0;
        font-weight: 400; transition: border-color .14s ease, background .14s ease;
    }
    .choice:hover { border-color: #c3c6f5; }
    .choice.is-on { border-color: #c3c6f5; background: #fafbff; }
    .choice input { width: 15px; height: 15px; margin: 0; flex-shrink: 0; margin-top: 2px; }
    .choice-label { display: block; font-size: 13px; font-weight: 600; }
    .choice-hint { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }

    .chips-row { display: flex; gap: 7px; flex-wrap: wrap; }
    .mode-chip {
        display: inline-flex; align-items: center; gap: 7px; margin: 0; padding: 9px 13px;
        border: 1px solid var(--border); border-radius: 999px; background: #fff;
        color: #585e72; font-size: 12.5px; font-weight: 600; cursor: pointer;
        transition: border-color .14s ease, background .14s ease, color .14s ease;
    }
    .mode-chip:hover { border-color: #c3c6f5; }
    .mode-chip.is-on { border-color: #c3c6f5; background: var(--brand-light); color: var(--brand-deep); }
    .mode-chip input { position: absolute; opacity: 0; width: 0; height: 0; }
    .mode-chip svg { stroke: currentColor; }

    .opt-list { display: flex; flex-direction: column; }
    .opt {
        display: flex; align-items: center; gap: 12px; margin: 0; padding: 12px 18px;
        border-bottom: 1px solid #f6f7fa; cursor: pointer; font-weight: 400;
        transition: background .14s ease;
    }
    .opt.is-on { background: #fafbff; }
    .opt > input { width: 15px; height: 15px; margin: 0; flex-shrink: 0; }
    .opt-text { min-width: 0; flex: 1; }
    .opt-title { display: block; font-size: 13.5px; font-weight: 600; }
    .opt-cat { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
    .opt-amount { position: relative; flex-shrink: 0; }
    .opt-amount input {
        width: 112px; max-width: none; text-align: right; font-variant-numeric: tabular-nums;
        border-radius: 9px; padding: 9px 26px 9px 11px; font-size: 13px;
    }
    .opt-euro { position: absolute; right: 11px; top: 9px; font-size: 12.5px; color: var(--faint); pointer-events: none; }
    .opt-empty { margin: 0; padding: 22px 18px; font-size: 13px; color: var(--muted); }

    .is-sticky { position: sticky; top: 76px; }
    .sum-head { padding: 16px 18px 14px; border-bottom: 1px solid var(--border-soft); }
    .field-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .sum-total { display: flex; align-items: baseline; gap: 8px; margin-top: 7px; }
    .sum-total > span:first-child { font-size: 30px; font-weight: 700; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
    .sum-hint { font-size: 13px; color: var(--muted); }
    .sum-lines { display: flex; flex-direction: column; }
    .sum-line { display: flex; align-items: center; gap: 10px; padding: 10px 18px; border-bottom: 1px solid #f6f7fa; }
    .sum-line span:first-child { font-size: 13px; color: #585e72; min-width: 0; }
    .sum-line span:last-child { margin-left: auto; font-size: 13px; font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .sum-empty { margin: 0; padding: 20px 18px; font-size: 12.5px; color: var(--muted); text-align: center; }
    .sum-foot { padding: 14px 18px; background: #fbfbff; border-top: 1px solid var(--border-soft); }
    .sum-foot .btn-block { width: 100%; justify-content: center; padding: 12px 16px; font-size: 14px; }
    .sum-cancel { display: block; margin-top: 9px; text-align: center; font-size: 12.5px; color: var(--muted); font-weight: 600; }
    .sum-cancel:hover { color: var(--ink); }

    .sit { display: flex; flex-direction: column; gap: 8px; }
    .sit-row { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #585e72; }
    .sit-value { margin-left: auto; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink); white-space: nowrap; }
    .sit-value.is-ok { color: #0f766e; }
    .sit-value.is-warn { color: #b45309; }
    .sit-link { display: flex; align-items: center; gap: 6px; margin-top: 12px; padding-top: 11px; border-top: 1px solid var(--border-soft); font-size: 12.5px; font-weight: 600; }
</style>

<script>
    (function () {
        var form = document.getElementById('paiement-form');
        if (!form) return;

        var rows = Array.prototype.slice.call(form.querySelectorAll('.opt'));
        var totalEl = form.querySelector('[data-total]');
        var hintEl = form.querySelector('[data-total-hint]');
        var recapEl = form.querySelector('[data-recap]');
        var emptyEl = form.querySelector('[data-recap-empty]');
        var toggleAll = form.querySelector('[data-toggle-all]');

        var euro = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' });

        function refresh() {
            var total = 0, retenues = [];

            rows.forEach(function (row) {
                var check = row.querySelector('[data-opt-check]');
                var amount = row.querySelector('[data-opt-amount]');
                row.classList.toggle('is-on', check.checked);
                amount.disabled = !check.checked;
                if (check.checked) {
                    var valeur = parseFloat(amount.value) || 0;
                    total += valeur;
                    retenues.push([row.querySelector('.opt-title').textContent.trim(), valeur]);
                }
            });

            if (totalEl) totalEl.textContent = euro.format(total);
            if (hintEl) hintEl.textContent = retenues.length
                ? retenues.length + (retenues.length > 1 ? ' options' : ' option')
                : 'aucune option';

            if (recapEl) {
                recapEl.innerHTML = '';
                retenues.forEach(function (ligne) {
                    var div = document.createElement('div');
                    div.className = 'sum-line';
                    var nom = document.createElement('span');
                    nom.textContent = ligne[0];
                    var val = document.createElement('span');
                    val.textContent = euro.format(ligne[1]);
                    div.appendChild(nom);
                    div.appendChild(val);
                    recapEl.appendChild(div);
                });
            }
            if (emptyEl) emptyEl.hidden = retenues.length > 0;
            if (toggleAll) toggleAll.textContent = retenues.length === rows.length && rows.length ? 'Tout décocher' : 'Tout cocher';
        }

        rows.forEach(function (row) {
            row.querySelector('[data-opt-check]').addEventListener('change', refresh);
            row.querySelector('[data-opt-amount]').addEventListener('input', refresh);
        });

        if (toggleAll) {
            toggleAll.addEventListener('click', function () {
                var toutes = rows.every(function (r) { return r.querySelector('[data-opt-check]').checked; });
                rows.forEach(function (r) { r.querySelector('[data-opt-check]').checked = !toutes; });
                refresh();
            });
        }

        // Surlignage des choix statut / mode
        form.querySelectorAll('input[name="statut_paiement"]').forEach(function (input) {
            input.addEventListener('change', function () {
                form.querySelectorAll('.choice').forEach(function (c) {
                    c.classList.toggle('is-on', c.querySelector('input').checked);
                });
            });
        });
        form.querySelectorAll('input[name="mode_paiement"]').forEach(function (input) {
            input.addEventListener('change', function () {
                form.querySelectorAll('.mode-chip').forEach(function (c) {
                    c.classList.toggle('is-on', c.querySelector('input').checked);
                });
            });
        });

        refresh();
    })();
</script>
@endsection
