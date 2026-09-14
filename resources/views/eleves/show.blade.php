@extends('layouts.app')

@section('title', 'Fiche élève')

@section('content')
@php
    $contact = $eleve->contact;
    $nom = $contact?->nom_complet ?? 'Élève #'.$eleve->id_eleve;
    $initiales = mb_strtoupper(mb_substr($contact?->prenom ?? '?', 0, 1).mb_substr($contact?->nom ?? '', 0, 1));

    $statutPaiement = $eleve->paiement_formation ?: 'Non payé';
    $statutBas = mb_strtolower($statutPaiement);
    $paiementTint = match (true) {
        str_contains($statutBas, 'payé') && ! str_contains($statutBas, 'non') => ['#e7f6f2', '#0f766e'],
        str_contains($statutBas, 'opco') => ['#eef0fe', '#3730a3'],
        default => ['#fdecef', '#be123c'],
    };

    $profilTint = match ($eleve->profil) {
        'eleve' => ['#eef0fe', '#3730a3'],
        'candidat' => ['#fdf3e3', '#92400e'],
        'alumni' => ['#f4ecfd', '#6d28d9'],
        'reinscrit' => ['#e7f6f2', '#0f766e'],
        'abandon' => ['#fdecef', '#be123c'],
        default => ['#eef1f6', '#475569'],
    };

    // Onglets secondaires : affichés seulement si le contrôleur fournit les données.
    $paiements = $paiements ?? null;
    $notesParUe = $notesParUe ?? null;
    $documents = $documents ?? null;

    $montant = (float) ($eleve->montant_formation ?: 0);
    $encaisse = $paiements ? (float) collect($paiements)->where('encaisse', true)->sum('montant') : null;
    $progression = $montant > 0 && $encaisse !== null ? min(100, round($encaisse / $montant * 100)) : null;
    $euro = fn ($v) => number_format((float) $v, 0, ',', ' ').' €';

    $scolarite = [
        'Profil' => ucfirst($eleve->profil ?? '—'),
        'Niveau' => $eleve->niveau?->nom_niveau ?? '—',
        'Niveau futur' => $eleve->niveauFuture?->nom_niveau ?? '—',
        'Classe' => $eleve->classe?->classe ?? '—',
        'Établissement' => $eleve->etablissement?->nom_etablissement ?? '—',
        'Année de formation' => $eleve->annee_formation ?? '—',
        "Date d'inscription" => $eleve->date_inscription?->format('d/m/Y') ?? '—',
        'Montant formation' => $montant > 0 ? $euro($montant) : '—',
    ];

    $coordonnees = [
        'Email' => $contact?->email ?? '—',
        'Téléphone' => $contact?->telephone ?? '—',
        'Date de naissance' => $contact?->date_naissance ? \Illuminate\Support\Carbon::parse($contact->date_naissance)->format('d/m/Y') : '—',
        'Adresse' => $contact?->adresse ?? '—',
        'Ville' => $contact?->ville ?? '—',
        'Code postal' => $contact?->code_postal ?? '—',
    ];
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <a href="{{ route('eleves.index') }}">Élèves</a>
    <span class="sep">/</span>
    <span class="current">{{ $nom }}</span>
</div>

{{-- ---------- En-tête d'identité ---------- --}}
<div class="id-card">
    <div class="id-row">
        <span class="id-avatar">{{ $initiales }}</span>

        <div class="id-main">
            <div class="id-title">
                <h1>{{ $nom }}</h1>
                <span class="pill" style="background:{{ $profilTint[0] }};color:{{ $profilTint[1] }}">{{ ucfirst($eleve->profil ?? '—') }}</span>
                <span class="pill dot-pill" style="background:{{ $eleve->visible ? '#e7f6f2' : '#fdecef' }};color:{{ $eleve->visible ? '#0f766e' : '#be123c' }}">
                    <span class="dot"></span>{{ $eleve->visible ? 'Visible' : 'Masqué' }}
                </span>
            </div>
            <p class="id-sub">
                {{ $eleve->niveau?->nom_niveau ?? '—' }}@if ($eleve->classe) — {{ $eleve->classe->classe }} @endif
                @if ($eleve->etablissement) · {{ $eleve->etablissement->nom_etablissement }} @endif
            </p>
            <div class="id-contacts">
                @if ($contact?->email)
                    <a href="mailto:{{ $contact->email }}">
                        @include('partials.icon', ['n' => 'mail', 's' => 15, 'c' => '#9aa0b0']){{ $contact->email }}
                    </a>
                @endif
                @if ($contact?->telephone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $contact->telephone) }}">
                        @include('partials.icon', ['n' => 'phone', 's' => 15, 'c' => '#9aa0b0']){{ $contact->telephone }}
                    </a>
                @endif
                <span class="id-meta">
                    @include('partials.icon', ['n' => 'tag', 's' => 15, 'c' => '#9aa0b0'])ID {{ $eleve->id_eleve }}
                </span>
            </div>
        </div>

        <div class="id-actions">
            @if (Route::has('bulletin-v2.create'))
                <a href="{{ route('bulletin-v2.create') }}" class="btn btn-ghost">
                    @include('partials.icon', ['n' => 'printer', 's' => 15, 'w' => 2])Bulletin PDF
                </a>
            @endif
            <a href="{{ route('eleves.edit', $eleve) }}" class="btn">
                @include('partials.icon', ['n' => 'pencil', 's' => 15, 'w' => 2])Modifier
            </a>
        </div>
    </div>

    <div class="tabs-bar" role="tablist">
        <button type="button" class="tab is-active" data-tab="scolarite" role="tab">Scolarité</button>
        @if ($paiements !== null)
            <button type="button" class="tab" data-tab="reglements" role="tab">
                Règlements<span class="tab-badge">{{ count($paiements) }}</span>
            </button>
        @endif
        @if ($notesParUe !== null)
            <button type="button" class="tab" data-tab="notes" role="tab">
                Notes<span class="tab-badge">{{ collect($notesParUe)->sum(fn ($n) => count($n)) }}</span>
            </button>
        @endif
        @if ($documents !== null)
            <button type="button" class="tab" data-tab="documents" role="tab">
                Documents<span class="tab-badge">{{ count($documents) }}</span>
            </button>
        @endif
    </div>
</div>

{{-- ---------- Corps : contenu + colonne latérale ---------- --}}
<div class="detail-grid">
    <div class="detail-main">

        {{-- Scolarité --}}
        <div data-panel="scolarite">
            <section class="panel">
                <div class="panel-head"><h2>Scolarité</h2></div>
                <div class="field-grid">
                    @foreach ($scolarite as $label => $valeur)
                        <div class="field">
                            <div class="field-label">{{ $label }}</div>
                            <div class="field-value">{{ $valeur }}</div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h2>Coordonnées</h2>
                    @if ($contact && Route::has('contacts.edit'))
                        <a href="{{ route('contacts.edit', $contact) }}">Modifier le contact</a>
                    @endif
                </div>
                <div class="field-grid">
                    @foreach ($coordonnees as $label => $valeur)
                        <div class="field">
                            <div class="field-label">{{ $label }}</div>
                            <div class="field-value is-regular">{{ $valeur }}</div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- Règlements --}}
        @if ($paiements !== null)
            <div data-panel="reglements" hidden>
                <section class="panel">
                    <div class="panel-head">
                        <h2>Échéancier</h2>
                        <span class="panel-sub">{{ count($paiements) }} échéances @if ($montant > 0) · {{ $euro($montant) }} au total @endif</span>
                        @if (Route::has('paiements.index'))
                            <a href="{{ route('paiements.index', $eleve) }}">Tous les règlements</a>
                        @endif
                    </div>
                    <div class="table-scroll">
                        <table class="data-table" style="min-width:440px">
                            <thead>
                                <tr>
                                    <th>Échéance</th>
                                    <th>Moyen</th>
                                    <th class="num">Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paiements as $paiement)
                                    <tr>
                                        <td class="strong">{{ $paiement->date_echeance?->format('d/m/Y') ?? '—' }}</td>
                                        <td class="muted">{{ $paiement->moyen ?? '—' }}</td>
                                        <td class="num strong">{{ $euro($paiement->montant) }}</td>
                                        <td>
                                            <span class="dot-status" style="color:{{ $paiement->encaisse ? '#0f766e' : '#b45309' }}">
                                                <span class="dot"></span>{{ $paiement->encaisse ? 'Encaissé' : 'À échoir' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @endif

        {{-- Notes --}}
        @if ($notesParUe !== null)
            <div data-panel="notes" hidden>
                <section class="panel">
                    <div class="panel-head">
                        <h2>Notes</h2>
                        @isset($moyenneGenerale)
                            <span class="panel-sub">moyenne générale {{ number_format((float) $moyenneGenerale, 2, ',', ' ') }}</span>
                        @endisset
                        @if (Route::has('bulletin-v2.create'))
                            <a href="{{ route('bulletin-v2.create') }}">Bulletin PDF</a>
                        @endif
                    </div>
                    <div class="table-scroll">
                        <table class="data-table" style="min-width:400px">
                            <thead>
                                <tr>
                                    <th>Unité d'enseignement</th>
                                    <th>Matière</th>
                                    <th class="num">Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notesParUe as $ue => $lignes)
                                    @foreach ($lignes as $ligne)
                                        <tr>
                                            <td class="muted">{{ $ue }}</td>
                                            <td class="strong">{{ $ligne->evaluation?->matiere?->nom_cours ?? '—' }}</td>
                                            <td class="num note">{{ $ligne->note !== null ? number_format((float) $ligne->note, 2, ',', ' ') : '—' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @endif

        {{-- Documents --}}
        @if ($documents !== null)
            <div data-panel="documents" hidden>
                <section class="panel">
                    <div class="panel-head"><h2>Documents</h2></div>
                    <div class="doc-list">
                        @foreach ($documents as $document)
                            <a href="{{ $document->url ?? '#' }}" class="doc" target="_blank" rel="noopener">
                                <span class="doc-icon">@include('partials.icon', ['n' => 'file', 's' => 16, 'c' => '#585e72'])</span>
                                <span class="doc-text">
                                    <span class="doc-name">{{ $document->nom }}</span>
                                    <span class="doc-meta">{{ $document->meta ?? '' }}</span>
                                </span>
                                @include('partials.icon', ['n' => 'download', 's' => 15, 'c' => '#c9cdd9', 'w' => 2, 'style' => 'margin-left:auto'])
                            </a>
                        @endforeach
                    </div>
                </section>
            </div>
        @endif
    </div>

    {{-- Colonne latérale --}}
    <div class="detail-side">
        <section class="panel panel-pad">
            <div class="field-label">Règlement de la formation</div>
            <div class="money">
                <span class="money-value">{{ $montant > 0 ? $euro($montant) : '—' }}</span>
                <span class="money-hint">montant total</span>
            </div>
            @if ($progression !== null)
                <div class="bar"><span style="width:{{ $progression }}%"></span></div>
                <div class="bar-legend">
                    <span class="is-ok">{{ $euro($encaisse) }} encaissés</span>
                    <span>{{ $euro(max(0, $montant - $encaisse)) }} restants</span>
                </div>
            @endif
            <div class="money-foot">
                <span class="pill" style="background:{{ $paiementTint[0] }};color:{{ $paiementTint[1] }}">{{ $statutPaiement }}</span>
                @if (Route::has('paiements.index'))
                    <a href="{{ route('paiements.index', $eleve) }}">Détail</a>
                @endif
            </div>
        </section>

        @isset($parcours)
            <section class="panel">
                <div class="panel-head"><h2>Parcours</h2></div>
                <div class="timeline">
                    @foreach ($parcours as $etape)
                        <div class="step">
                            <span class="step-dot"></span>
                            <span>
                                <span class="step-title">{{ $etape['titre'] }}</span>
                                <span class="step-date">{{ $etape['date'] }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endisset

        <section class="panel panel-pad">
            <h2 class="side-title">Actions</h2>
            <div class="side-actions">
                @if (Route::has('paiements.index'))
                    <a href="{{ route('paiements.index', $eleve) }}" class="side-action">
                        @include('partials.icon', ['n' => 'card', 's' => 15])Voir les règlements
                    </a>
                @endif
                @if (Route::has('bulletin-v2.create'))
                    <a href="{{ route('bulletin-v2.create') }}" class="side-action">
                        @include('partials.icon', ['n' => 'printer', 's' => 15])Générer un bulletin
                    </a>
                @endif
                @if ($contact?->email)
                    <a href="mailto:{{ $contact->email }}" class="side-action">
                        @include('partials.icon', ['n' => 'mail', 's' => 15])Envoyer un email
                    </a>
                @endif
                @if ($contact && Route::has('contacts.show'))
                    <a href="{{ route('contacts.show', $contact) }}" class="side-action">
                        @include('partials.icon', ['n' => 'contact', 's' => 15])Ouvrir la fiche contact
                    </a>
                @endif

                <form method="post" action="{{ route('eleves.destroy', $eleve) }}" onsubmit="return confirm('Masquer cet élève ?')">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="side-action is-danger" onclick="this.form.requestSubmit()">
                        @include('partials.icon', ['n' => 'eye-off', 's' => 15, 'c' => '#b91c1c'])Masquer l'élève
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>

<style>
    /* Fiche élève : styles spécifiques (le reste vient de layouts/app.blade.php) */
    .id-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px 20px 0; margin-bottom: 14px; }
    .id-row { display: flex; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
    .id-avatar {
        width: 62px; height: 62px; border-radius: 16px; flex-shrink: 0;
        background: linear-gradient(140deg, #6366f1, var(--brand-dark));
        color: #fff; font-size: 21px; font-weight: 700; letter-spacing: -.02em;
        display: flex; align-items: center; justify-content: center;
    }
    .id-main { min-width: 0; flex: 1 1 280px; }
    .id-title { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
    .id-title h1 { margin: 0; font-size: 23px; letter-spacing: -.025em; }
    .id-sub { margin: 7px 0 0; font-size: 13.5px; color: #585e72; }
    .id-contacts { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 11px; }
    .id-contacts a, .id-meta { display: inline-flex; align-items: center; gap: 7px; font-size: 13px; font-weight: 500; color: #585e72; }
    .id-contacts a:hover { color: var(--brand); }
    .id-meta { color: var(--muted); font-weight: 400; }
    .id-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto; }

    .pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 700; }
    .pill .dot, .dot-status .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .tabs-bar { display: flex; gap: 2px; margin-top: 18px; overflow-x: auto; }
    .tab {
        display: inline-flex; align-items: center; gap: 7px; padding: 10px 15px;
        border: 0; border-bottom: 2px solid transparent; background: none; cursor: pointer;
        font: inherit; font-size: 13.5px; font-weight: 600; color: var(--muted); white-space: nowrap;
        transition: color .14s ease, border-color .14s ease;
    }
    .tab:hover { color: var(--ink); background: none; }
    .tab.is-active { color: var(--brand-deep); border-bottom-color: var(--brand); }
    .tab-badge { padding: 1px 7px; border-radius: 999px; font-size: 11px; font-weight: 700; background: #f0f1f6; color: var(--muted); }
    .tab.is-active .tab-badge { background: var(--brand-light); color: var(--brand-deep); }

    .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 14px; align-items: start; }
    .detail-main, .detail-side { display: flex; flex-direction: column; gap: 14px; min-width: 0; }
    .detail-main > [data-panel] { display: flex; flex-direction: column; gap: 14px; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-pad { padding: 16px; }
    .panel-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 14px 16px 12px; border-bottom: 1px solid var(--border-soft); }
    .panel-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head .panel-sub { font-size: 12.5px; color: var(--muted); }
    .panel-head a { margin-left: auto; font-size: 12.5px; font-weight: 600; }

    .field-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 1px; background: var(--border-soft); }
    .field { background: var(--surface); padding: 12px 16px; }
    .field-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .field-value { font-size: 13.5px; font-weight: 600; margin-top: 3px; }
    .field-value.is-regular { font-weight: 500; }

    .table-scroll { overflow-x: auto; }
    .data-table { margin: 0; border: 0; border-radius: 0; box-shadow: none; }
    .data-table th.num, .data-table td.num { text-align: right; }
    .data-table td { vertical-align: middle; font-size: 13px; }
    .data-table td.num { font-variant-numeric: tabular-nums; }
    .data-table td.strong { font-weight: 600; }
    .data-table td.muted { color: var(--muted); }
    .data-table td.note { font-weight: 700; }
    .dot-status { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; }

    .doc-list { display: flex; flex-direction: column; }
    .doc { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid #f6f7fa; color: inherit; transition: background .14s ease; }
    .doc:hover { background: #fafbff; color: inherit; }
    .doc-icon { width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0; background: #f5f6fa; display: flex; align-items: center; justify-content: center; }
    .doc-text { min-width: 0; }
    .doc-name { display: block; font-size: 13.5px; font-weight: 600; }
    .doc-meta { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }

    .money { display: flex; align-items: baseline; gap: 8px; margin-top: 8px; }
    .money-value { font-size: 26px; font-weight: 700; letter-spacing: -.025em; font-variant-numeric: tabular-nums; }
    .money-hint { font-size: 12.5px; color: var(--muted); }
    .bar { height: 7px; border-radius: 999px; background: #f0f1f6; overflow: hidden; margin-top: 13px; }
    .bar span { display: block; height: 100%; border-radius: 999px; background: var(--brand); }
    .bar-legend { display: flex; justify-content: space-between; gap: 10px; margin-top: 7px; font-size: 12px; color: var(--muted); }
    .bar-legend .is-ok { color: #0f766e; font-weight: 600; }
    .money-foot { display: flex; align-items: center; gap: 9px; margin-top: 13px; padding-top: 12px; border-top: 1px solid var(--border-soft); }
    .money-foot a { margin-left: auto; font-size: 12.5px; font-weight: 600; }

    .timeline { display: flex; flex-direction: column; }
    .step { display: flex; gap: 11px; padding: 11px 16px; border-bottom: 1px solid #f6f7fa; }
    .step-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; background: var(--brand); }
    .step-title { display: block; font-size: 13px; font-weight: 600; }
    .step-date { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }

    .side-title { margin: 0 0 11px; font-size: 13.5px; font-weight: 700; }
    .side-actions { display: flex; flex-direction: column; gap: 7px; }
    .side-actions form { max-width: none; margin: 0; }
    .side-action {
        display: flex; align-items: center; gap: 9px; width: 100%; padding: 9px 11px;
        border-radius: 9px; border: 1px solid var(--border); background: #fff;
        color: #585e72; font: inherit; font-size: 13px; font-weight: 600; cursor: pointer;
        transition: border-color .14s ease, background .14s ease, color .14s ease;
    }
    .side-action:hover { border-color: #c3c6f5; background: #fafbff; color: var(--brand-deep); }
    .side-action svg { stroke: currentColor; flex-shrink: 0; }
    .side-action.is-danger { color: var(--danger-dark); border-color: #fecaca; }
    .side-action.is-danger:hover { background: var(--danger-bg); color: var(--danger-dark); border-color: #fecaca; }
    .side-action.is-danger svg { stroke: var(--danger-dark); }
</style>

<script>
    // Onglets de la fiche : bascule locale, sans rechargement.
    (function () {
        var tabs = Array.prototype.slice.call(document.querySelectorAll('.tab[data-tab]'));
        var panels = Array.prototype.slice.call(document.querySelectorAll('[data-panel]'));
        if (!tabs.length) return;

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.toggle('is-active', t === tab); });
                panels.forEach(function (p) { p.hidden = p.dataset.panel !== tab.dataset.tab; });
            });
        });
    })();
</script>
@endsection
