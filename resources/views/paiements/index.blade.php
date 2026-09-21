@extends('layouts.app')

@section('title', 'Règlements · '.($eleve->contact?->nom_complet ?? 'Élève'))

@section('content')
@php
    $dh = fn ($v) => number_format((float) $v, 0, ',', ' ').' DH';
    $reste = max(0, $total - $regle);
    $progression = $total > 0 ? min(100, round($regle / $total * 100)) : 0;

    $teintes = [
        \App\Models\Echeance::STATUT_PAYEE => ['#e7f6f2', '#0f766e'],
        \App\Models\Echeance::STATUT_PARTIELLE => ['#eef0fe', '#3730a3'],
        \App\Models\Echeance::STATUT_RETARD => ['#fdecef', '#be123c'],
        \App\Models\Echeance::STATUT_A_VENIR => ['#f4f5fa', '#585e72'],
    ];

    $typesLibelles = [
        \App\Models\Echeance::TYPE_INSCRIPTION => "Frais d'inscription",
        \App\Models\Echeance::TYPE_MENSUALITE => 'Mensualité',
        \App\Models\Echeance::TYPE_OPTION => 'Option',
        \App\Models\Echeance::TYPE_AUTRE => 'Autre',
    ];
@endphp

<div class="crumb">
    <a href="{{ route('eleves.index') }}">Élèves</a>
    <span class="sep">/</span>
    <a href="{{ route('eleves.show', $eleve) }}">{{ $eleve->contact?->nom_complet ?? 'Élève' }}</a>
    <span class="sep">/</span>
    <span class="current">Règlements</span>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

<div class="page-head">
    <div>
        <h1>Règlements</h1>
        <p class="page-sub">
            {{ $eleve->contact?->nom_complet }}
            @if ($eleve->classe?->classe) · classe {{ $eleve->classe->classe }} @endif
            @if ($eleve->classe?->etablissement?->nom_etablissement) · {{ $eleve->classe->etablissement->nom_etablissement }} @endif
        </p>
    </div>
    <div class="page-actions">
        <a class="btn btn-ghost" href="{{ route('eleves.show', $eleve) }}">Fiche élève</a>
        @if (Route::has('echeances.generer'))
            <a class="btn" href="{{ route('echeances.generer') }}">Générer un échéancier</a>
        @endif
    </div>
</div>

@if ($total > 0)
    <section class="rg-synthese">
        <div class="rg-chiffre">
            <span class="rg-label">Total dû</span>
            <strong>{{ $dh($total) }}</strong>
        </div>
        <div class="rg-chiffre">
            <span class="rg-label">Encaissé</span>
            <strong class="is-ok">{{ $dh($regle) }}</strong>
        </div>
        <div class="rg-chiffre">
            <span class="rg-label">Reste à payer</span>
            <strong class="@if ($reste > 0) is-du @endif">{{ $dh($reste) }}</strong>
        </div>
        @if ($enRetard > 0)
            <div class="rg-chiffre">
                <span class="rg-label">En retard</span>
                <strong class="is-retard">{{ $enRetard }} échéance{{ $enRetard > 1 ? 's' : '' }}</strong>
            </div>
        @endif
        <div class="rg-barre">
            <div class="bar"><span style="width: {{ $progression }}%"></span></div>
            <span class="rg-pourcent">{{ $progression }} % réglé</span>
        </div>
    </section>
@endif

@forelse ($echeancesParAnnee as $annee => $echeances)
    @php
        $totalAnnee = $echeances->sum('montant');
        $regleAnnee = $echeances->sum('montant_regle');
    @endphp
    <div class="table-card rg-bloc">
        <div class="table-head">
            <span class="table-count">
                Année {{ $annee }} · {{ $echeances->count() }} échéance{{ $echeances->count() > 1 ? 's' : '' }}
                · {{ $dh($totalAnnee) }} dont {{ $dh($regleAnnee) }} encaissés
            </span>
        </div>

        <div class="table-scroll">
            <table class="data-table" style="min-width:880px">
                <thead>
                    <tr>
                        <th>Échéance</th>
                        <th>Libellé</th>
                        <th>Type</th>
                        <th class="num">Montant</th>
                        <th class="num">Réglé</th>
                        <th>Moyen</th>
                        <th>Statut</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($echeances as $echeance)
                        @php $teinte = $teintes[$echeance->statut] ?? ['#f4f5fa', '#585e72']; @endphp
                        <tr>
                            <td class="strong">{{ $echeance->date_echeance?->format('d/m/Y') ?: '—' }}</td>
                            <td>{{ $echeance->libelle ?: '—' }}</td>
                            <td class="muted">{{ $typesLibelles[$echeance->type] ?? ucfirst($echeance->type) }}</td>
                            <td class="num">{{ $dh($echeance->montant) }}</td>
                            <td class="num">{{ (float) $echeance->montant_regle > 0 ? $dh($echeance->montant_regle) : '—' }}</td>
                            <td class="muted">{{ $echeance->mode_reglement ?: '—' }}</td>
                            <td>
                                <span class="pill" style="background:{{ $teinte[0] }};color:{{ $teinte[1] }}">{{ $echeance->statut_libelle }}</span>
                            </td>
                            <td class="col-actions">
                                @if ($echeance->statut !== \App\Models\Echeance::STATUT_PAYEE)
                                    <button type="button" class="row-btn" title="Encaisser"
                                            data-regler
                                            data-action="{{ route('echeances.regler', $echeance) }}"
                                            data-libelle="{{ $echeance->libelle ?: 'Échéance' }}"
                                            data-montant="{{ $echeance->montant }}"
                                            data-regle="{{ $echeance->montant_regle }}"
                                            data-echeance="{{ $echeance->date_echeance?->format('d/m/Y') }}">
                                        @include('partials.icon', ['n' => 'check-simple', 's' => 14, 'c' => '#0f766e', 'w' => 2.4])
                                    </button>
                                @elseif (Route::has('echeances.recu'))
                                    <a class="row-btn" href="{{ route('echeances.recu', $echeance) }}" target="_blank" title="Reçu de paiement">
                                        @include('partials.icon', ['n' => 'printer', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="rg-vide">
        @include('partials.icon', ['n' => 'card', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
        <p>Aucun échéancier pour cet élève</p>
        <span>Générez un échéancier pour répartir les frais d'inscription et les mensualités sur l'année.</span>
        @if (Route::has('echeances.generer'))
            <a href="{{ route('echeances.generer') }}">Générer un échéancier</a>
        @endif
    </div>
@endforelse

@if ($paiements->isNotEmpty())
    {{-- Ancien module de règlements : affiché seulement s'il porte des lignes. --}}
    <h2 class="rg-titre">Règlements saisis dans l'ancien module</h2>
    <div class="table-card">
        <div class="table-scroll">
            <table class="data-table" style="min-width:680px">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Date</th>
                        <th>Établissement</th>
                        <th class="num">Montant réglé</th>
                        <th class="num">Options</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paiements as $paiement)
                        <tr>
                            <td class="strong">{{ $paiement->titre }}</td>
                            <td class="muted">{{ $paiement->date?->format('d/m/Y') ?: '—' }}</td>
                            <td class="muted">{{ $paiement->etablissement?->nom_etablissement ?: '—' }}</td>
                            <td class="num">{{ $dh($paiement->montantTotal()) }}</td>
                            <td class="num">{{ $paiement->options->isNotEmpty() ? $dh($paiement->options->sum('montant')) : '—' }}</td>
                            <td class="col-actions">
                                <a class="row-btn" href="{{ route('paiements.show', $paiement) }}" title="Détail">
                                    @include('partials.icon', ['n' => 'eye', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Encaissement d'une échéance --}}
<div class="rg-modal" data-modal hidden>
    <div class="rg-modal-fond" data-fermer></div>
    <div class="rg-modal-boite" role="dialog" aria-modal="true" aria-labelledby="rg-modal-titre">
        <div class="rg-modal-tete">
            <div>
                <span class="rg-label">Encaisser</span>
                <h2 id="rg-modal-titre" data-champ-libelle>—</h2>
                <p class="rg-modal-sub" data-champ-echeance></p>
            </div>
            <button type="button" class="rg-modal-x" data-fermer aria-label="Fermer">
                @include('partials.icon', ['n' => 'close', 's' => 16, 'c' => '#585e72', 'w' => 2.2])
            </button>
        </div>

        <form method="post" data-form class="rg-form">
            @csrf
            <div class="rg-form-grid">
                <label class="stack">
                    <span>Montant réglé</span>
                    <input type="number" name="montant_regle" step="0.01" min="0" data-saisie-montant required>
                </label>
                <label class="stack">
                    <span>Date du règlement</span>
                    <input type="date" name="date_reglement" value="{{ now()->format('Y-m-d') }}">
                </label>
                <label class="stack">
                    <span>Moyen</span>
                    <select name="mode_reglement">
                        <option value="">—</option>
                        @foreach (['Espèces', 'Chèque', 'Virement', 'Carte', 'Prélèvement'] as $moyen)
                            <option value="{{ $moyen }}">{{ $moyen }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <label class="stack rg-form-large">
                <span>Commentaire</span>
                <input type="text" name="commentaire" maxlength="250" placeholder="N° de chèque, remarque…">
            </label>

            <div class="rg-modal-pied">
                <span class="rg-aide">Un montant inférieur au dû enregistre un règlement partiel.</span>
                <button type="submit" class="btn">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Règlements d'un élève : synthèse, puis l'échéancier par année. */
    .rg-synthese {
        display: flex; align-items: flex-start; gap: 30px; flex-wrap: wrap;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
        padding: 16px 18px; margin-bottom: 14px;
    }
    .rg-label { display: block; font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); }
    .rg-chiffre strong { display: block; margin-top: 5px; font-size: 22px; font-weight: 700; letter-spacing: -.025em; line-height: 1; }
    .rg-chiffre strong.is-ok { color: #0f766e; }
    .rg-chiffre strong.is-du { color: var(--brand); }
    .rg-chiffre strong.is-retard { color: var(--danger); font-size: 17px; }
    .rg-barre { flex: 1 1 200px; min-width: 160px; margin-left: auto; }
    .bar { height: 7px; border-radius: 999px; background: #eef0f6; overflow: hidden; }
    .bar span { display: block; height: 100%; background: var(--brand); }
    .rg-pourcent { display: block; margin-top: 6px; font-size: 11.5px; color: var(--muted); text-align: right; }

    .rg-bloc + .rg-bloc { margin-top: 14px; }
    .rg-titre { margin: 26px 0 12px; font-size: 15px; font-weight: 700; letter-spacing: -.015em; }
    .data-table th.num, .data-table td.num { text-align: right; }
    .data-table td.num { font-variant-numeric: tabular-nums; }
    .data-table td.strong { font-weight: 600; }
    .data-table td.muted { color: var(--muted); }

    .rg-vide {
        text-align: center; padding: 48px 20px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
    }
    .rg-vide p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .rg-vide span { display: block; margin: 4px auto 0; max-width: 52ch; font-size: 13px; color: var(--muted); }
    .rg-vide a { display: inline-block; margin-top: 10px; font-size: 12.5px; font-weight: 600; }

    .rg-modal { position: fixed; inset: 0; z-index: 60; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .rg-modal[hidden] { display: none; }
    .rg-modal-fond { position: absolute; inset: 0; background: rgba(15, 18, 34, .45); }
    .rg-modal-boite {
        position: relative; width: 100%; max-width: 540px; max-height: 88vh; overflow-y: auto;
        background: var(--surface); border-radius: 16px; box-shadow: var(--shadow-lg);
    }
    .rg-modal-tete { display: flex; align-items: flex-start; gap: 12px; padding: 18px 18px 14px; border-bottom: 1px solid var(--border-soft); }
    .rg-modal-tete h2 { margin: 4px 0 0; font-size: 17px; letter-spacing: -.02em; }
    .rg-modal-sub { margin: 3px 0 0; font-size: 12.5px; color: var(--muted); }
    .rg-modal-x {
        margin-left: auto; width: 30px; height: 30px; flex-shrink: 0; padding: 0; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); border-radius: 9px; background: #fff;
    }
    .rg-form { max-width: none; margin: 0; padding: 16px 18px; }
    .rg-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }
    .rg-form .stack { display: block; margin: 0; min-width: 0; }
    .rg-form .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .rg-form input, .rg-form select { width: 100%; max-width: none; }
    .rg-form-large { margin-top: 12px !important; }
    .rg-modal-pied { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--border-soft); }
    .rg-aide { font-size: 11.5px; color: var(--muted); }
    .rg-modal-pied .btn { margin-left: auto; }
</style>

<script>
    (function () {
        var modal = document.querySelector('[data-modal]');
        if (!modal) return;

        var form = modal.querySelector('[data-form]');
        var montant = modal.querySelector('[data-saisie-montant]');
        var declencheur = null;

        function ouvrir(bouton) {
            var d = bouton.dataset;

            modal.querySelector('[data-champ-libelle]').textContent = d.libelle;
            modal.querySelector('[data-champ-echeance]').textContent = 'Échéance du ' + (d.echeance || '—');
            form.action = d.action;
            // Pré-rempli au montant dû : encaisser en entier est le cas courant.
            montant.value = d.montant;
            montant.max = '';

            modal.hidden = false;
            document.body.style.overflow = 'hidden';
            montant.focus();
            montant.select();
        }

        function fermer() {
            modal.hidden = true;
            document.body.style.overflow = '';
            if (declencheur) declencheur.focus();
        }

        document.querySelectorAll('[data-regler]').forEach(function (bouton) {
            bouton.addEventListener('click', function () {
                declencheur = bouton;
                ouvrir(bouton);
            });
        });

        modal.querySelectorAll('[data-fermer]').forEach(function (el) {
            el.addEventListener('click', fermer);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hidden) fermer();
        });
    })();
</script>
@endsection
