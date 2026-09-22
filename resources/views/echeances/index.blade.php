@extends('layouts.app')

@section('title', 'Échéanciers & impayés')

@section('content')
@php
    $dh = fn ($v) => number_format((float) $v, 2, ',', ' ').' DH';
    $couleurs = [
        'payee' => ['#e7f6f2', '#0f766e'],
        'partielle' => ['#eef0fe', '#3730a3'],
        'retard' => ['#fdecef', '#be123c'],
        'a_venir' => ['#eef1f6', '#475569'],
    ];
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Échéanciers</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Échéanciers & impayés</h1>
            <span class="badge badge-brand">{{ $annee }}</span>
        </div>
        <p class="page-sub">Frais d'inscription et mensualités, et ce qui reste à encaisser.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('echeances.generer') }}" class="btn">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Générer un échéancier
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

<div class="stats-row">
    <div class="stat"><span class="stat-value">{{ $dh($totaux['du']) }}</span><span class="stat-label">Total dû</span></div>
    <div class="stat is-ok"><span class="stat-value">{{ $dh($totaux['regle']) }}</span><span class="stat-label">Encaissé</span></div>
    <div class="stat is-alert"><span class="stat-value">{{ $dh($totaux['reste']) }}</span><span class="stat-label">Reste à encaisser</span></div>
    <div class="stat"><span class="stat-value">{{ $totaux['impayees'] }}</span><span class="stat-label">Échéances en retard</span></div>
</div>

<form method="get" class="filter-card">
    <div class="filter-row">
        <label class="stack">
            <span>Année scolaire</span>
            <select name="annee" onchange="this.form.submit()">
                <option value="{{ $annee }}">{{ $annee }}</option>
                @foreach ($annees as $a)
                    @continue($a === $annee)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>
        </label>
        <label class="stack" style="flex:1 1 200px">
            <span>Classe</span>
            <select name="classe">
                <option value="">Toutes</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id_classe }}" @selected(($filtres['classe'] ?? null) == $c->id_classe)>{{ $c->classe }}</option>
                @endforeach
            </select>
        </label>
        <label class="check" style="margin:0">
            <input type="checkbox" name="impayees" value="1" @checked($filtres['impayees'] ?? false)>
            Impayés seulement
        </label>
    </div>
    <div class="filter-foot">
        <a href="{{ route('echeances.index') }}" class="filter-reset">Réinitialiser</a>
        <button type="submit" class="btn">Filtrer</button>
    </div>
</form>

<div class="table-card">
    <div class="table-head">
        <span class="table-count">
            @if ($echeances->total())
                {{ $echeances->firstItem() }}–{{ $echeances->lastItem() }} sur {{ number_format($echeances->total(), 0, ',', ' ') }}
            @else
                Aucune échéance
            @endif
        </span>
    </div>

    <div class="table-scroll">
        <table class="data-table" style="min-width:900px">
            <thead>
                <tr>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Échéance</th>
                    <th>Date</th>
                    <th class="num">Montant</th>
                    <th class="num">Réglé</th>
                    <th>Statut</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($echeances as $echeance)
                    @php [$bg, $ink] = $couleurs[$echeance->statut]; @endphp
                    <tr>
                        <td>
                            <a href="{{ route('eleves.show', $echeance->id_eleve) }}" class="cell-name">
                                {{ $echeance->eleve?->contact?->nom_complet ?? 'Élève #'.$echeance->id_eleve }}
                            </a>
                        </td>
                        <td class="muted">{{ $echeance->eleve?->classe?->classe ?? '—' }}</td>
                        <td>{{ $echeance->libelle }}</td>
                        <td class="muted">{{ $echeance->date_echeance?->format('d/m/Y') }}</td>
                        <td class="num strong">{{ $dh($echeance->montant) }}</td>
                        <td class="num">{{ (float) $echeance->montant_regle > 0 ? $dh($echeance->montant_regle) : '—' }}</td>
                        <td><span class="pill" style="background:{{ $bg }};color:{{ $ink }}">{{ $echeance->statut_libelle }}</span></td>
                        <td class="col-actions">
                            <button type="button" class="row-btn" data-regler="{{ $echeance->id_echeance }}" title="Enregistrer un règlement">
                                @include('partials.icon', ['n' => 'card', 's' => 14, 'c' => '#585e72'])
                            </button>
                            @if ((float) $echeance->montant_regle > 0)
                                <a href="{{ route('echeances.recu', $echeance) }}" class="row-btn" title="Reçu de paiement" target="_blank" rel="noopener">
                                    @include('partials.icon', ['n' => 'printer', 's' => 14, 'c' => '#585e72'])
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr class="reg-row" id="reg-{{ $echeance->id_echeance }}" hidden>
                        <td colspan="8">
                            <form method="post" action="{{ route('echeances.regler', $echeance) }}" class="reg-form">
                                @csrf
                                <label class="stack">
                                    <span>Montant réglé</span>
                                    <input type="number" step="0.01" min="0" name="montant_regle" value="{{ $echeance->montant_regle }}" required>
                                </label>
                                <label class="stack">
                                    <span>Date</span>
                                    <input type="date" name="date_reglement" value="{{ $echeance->date_reglement?->format('Y-m-d') ?? date('Y-m-d') }}">
                                </label>
                                <label class="stack">
                                    <span>Mode</span>
                                    <select name="mode_reglement">
                                        <option value="">--</option>
                                        @foreach (['Espèces', 'Chèque', 'Virement', 'Carte'] as $mode)
                                            <option value="{{ $mode }}" @selected($echeance->mode_reglement === $mode)>{{ $mode }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label class="stack" style="flex:1 1 200px">
                                    <span>Commentaire</span>
                                    <input type="text" name="commentaire" value="{{ $echeance->commentaire }}">
                                </label>
                                <button type="submit" class="btn">Enregistrer</button>
                            </form>

                            <p class="reg-aide">
                                Montant total dû : <strong>{{ $dh($echeance->montant) }}</strong> ·
                                reste <strong>{{ $dh($echeance->reste) }}</strong>.
                                Un montant inférieur au total enregistre un règlement partiel.
                            </p>

                            @if ((float) $echeance->montant_regle <= 0)
                                <form method="post" action="{{ route('echeances.destroy', $echeance) }}"
                                      onsubmit="return confirm('Supprimer cette échéance ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Supprimer l'échéance</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-cell">
                            @include('partials.icon', ['n' => 'card', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune échéance pour ces critères.</span>
                            <a href="{{ route('echeances.generer') }}">Générer un échéancier</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-foot">{{ $echeances->links() }}</div>
</div>

<style>
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 14px; }
    .stat { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 16px 18px; }
    .stat-value { display: block; font-size: 20px; font-weight: 700; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .stat-label { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; }
    .stat.is-ok .stat-value { color: #0f766e; }
    .stat.is-alert .stat-value { color: #b45309; }

    .filter-card .stack { margin: 0; }
    .filter-card .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .filter-card .stack input, .filter-card .stack select { width: 100%; max-width: none; }

    .data-table th.num, .data-table td.num { text-align: right; }
    .data-table td.num { font-variant-numeric: tabular-nums; }
    .data-table td.strong { font-weight: 600; }
    .data-table td.muted { color: var(--muted); }
    .pill { display: inline-flex; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }

    .reg-row td { background: #fbfbff; }
    .reg-form { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; max-width: none; margin: 0; }
    .reg-form .stack { margin: 0; flex: 0 1 150px; }
    .reg-form .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .reg-form input, .reg-form select { max-width: none; width: 100%; }
    .reg-aide { margin: 10px 0 0; font-size: 12px; color: var(--muted); }
</style>

<script>
    document.querySelectorAll('[data-regler]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var ligne = document.getElementById('reg-' + btn.dataset.regler);
            ligne.hidden = !ligne.hidden;
        });
    });
</script>
@endsection
