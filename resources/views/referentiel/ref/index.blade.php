@extends('layouts.app')

@section('title', 'Référentiel des heures')

@section('content')
@php
    $pret = $idEtablissement && $idUe && $annee !== '';
    $onglet = request('onglet', 'niveau') === 'classe' ? 'classe' : 'niveau';
    $semestreFiltre = request('semestre');
    $lignes = $onglet === 'classe' ? ($lignesClasse ?? collect()) : ($lignesNiveau ?? collect());
    if ($semestreFiltre) {
        $lignes = $lignes->filter(fn ($l) => strtolower($l->semestre) === strtolower($semestreFiltre));
    }

    $somme = fn ($collection, $champ) => $collection->sum(fn ($l) => (float) $l->{$champ});
    $h = fn ($valeur) => rtrim(rtrim(number_format((float) $valeur, 2, ',', ' '), '0'), ',');

    $nomEtablissement = collect($etablissements)->firstWhere('id_etablissement', $idEtablissement)?->nom_etablissement;
    $nomUe = collect($unites)->firstWhere('id_unite_enseignement', $idUe)?->nom_unite_enseignement;
    $contexte = collect([$nomUe, $annee, $nomEtablissement])->filter()->implode(' · ');
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Référentiel des heures</span>
</div>

<div class="page-head">
    <div>
        <h1>Référentiel des heures d'enseignement</h1>
        <p class="page-sub">Volumes horaires et ECTS par unité d'enseignement, déclinés par niveau puis par classe.</p>
    </div>
    @if ($pret)
        <div class="page-actions">
            <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-ghost">
                @include('partials.icon', ['n' => 'download', 's' => 15, 'w' => 2])Exporter
            </a>
        </div>
    @endif
</div>

{{-- ---------- Contexte : établissement / UE / année ---------- --}}
<form method="get" action="{{ route('referentiel.ref.index') }}" class="ctx-card">
    <input type="hidden" name="onglet" value="{{ $onglet }}">

    <div class="ctx-row">
        <label>
            <span>Établissement</span>
            <select name="id_etablissement" onchange="this.form.submit()">
                <option value="">— Choisir —</option>
                @foreach ($etablissements as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected($idEtablissement == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
        </label>

        <label class="ctx-wide">
            <span>Unité d'enseignement</span>
            <select name="id_unite_enseignement" onchange="this.form.submit()">
                <option value="">— Choisir —</option>
                @foreach ($unites as $unite)
                    <option value="{{ $unite->id_unite_enseignement }}" @selected($idUe == $unite->id_unite_enseignement)>{{ $unite->nom_unite_enseignement }}</option>
                @endforeach
            </select>
        </label>

        <label class="ctx-narrow">
            <span>Année</span>
            <input type="text" name="annee" value="{{ $annee }}" placeholder="ex : 2025-2026">
        </label>

        <button type="submit" class="btn">Afficher</button>
    </div>

    @if ($pret)
        @php
            $toutes = ($lignesNiveau ?? collect())->concat($lignesClasse ?? collect());
            $volumeTotal = $somme($lignes, 'volume');
            $ectsTotal = $somme($lignes, 'ects');
            $s1 = $somme($lignes->filter(fn ($l) => strtolower($l->semestre) === 's1'), 'volume');
            $s2 = $somme($lignes->filter(fn ($l) => strtolower($l->semestre) === 's2'), 'volume');
            $nbIntervenants = $lignes->pluck('id_intervenant')->filter()->unique()->count();
        @endphp
        <div class="ctx-totals">
            <div class="ctx-total is-brand">
                <span class="ctx-total-label">Volume total</span>
                <span class="ctx-total-value">{{ $h($volumeTotal) }} h</span>
                <span class="ctx-total-hint">{{ $lignes->count() }} ligne(s)</span>
            </div>
            <div class="ctx-total is-brand">
                <span class="ctx-total-label">ECTS</span>
                <span class="ctx-total-value" style="color:var(--brand-deep)">{{ $h($ectsTotal) }}</span>
                <span class="ctx-total-hint">sur l'UE</span>
            </div>
            <div class="ctx-total">
                <span class="ctx-total-label">Semestre 1</span>
                <span class="ctx-total-value">{{ $h($s1) }} h</span>
                <span class="ctx-total-hint">cours magistraux inclus</span>
            </div>
            <div class="ctx-total">
                <span class="ctx-total-label">Semestre 2</span>
                <span class="ctx-total-value">{{ $h($s2) }} h</span>
                <span class="ctx-total-hint">cours magistraux inclus</span>
            </div>
            <div class="ctx-total">
                <span class="ctx-total-label">Intervenants</span>
                <span class="ctx-total-value">{{ $nbIntervenants }}</span>
                <span class="ctx-total-hint">affectés à l'UE</span>
            </div>
        </div>
    @endif
</form>

@if (! $pret)
    <div class="table-card">
        <div class="empty-state">
            @include('partials.icon', ['n' => 'clock', 's' => 28, 'c' => '#c9cdd9', 'w' => 1.6])
            <p>Choisissez un établissement, une unité d'enseignement et une année pour afficher le référentiel.</p>
        </div>
    </div>
@else
    {{-- ---------- Onglets niveau / classe ---------- --}}
    <div class="tabs">
        @foreach (['niveau' => ['Par niveau', ($lignesNiveau ?? collect())->count()], 'classe' => ['Par classe', ($lignesClasse ?? collect())->count()]] as $cle => [$libelle, $nombre])
            <a href="{{ request()->fullUrlWithQuery(['onglet' => $cle]) }}" class="tab {{ $onglet === $cle ? 'is-active' : '' }}">
                {{ $libelle }}<span class="tab-badge">{{ $nombre }}</span>
            </a>
        @endforeach
    </div>

    <div class="table-card">
        <div class="table-head">
            <span class="table-count">
                {{ $onglet === 'classe' ? 'Déclinaison par classe' : 'Déclinaison par niveau' }} — {{ $contexte }}
            </span>
            <div class="seg">
                @foreach (['' => 'Tous les semestres', 's1' => 'Semestre 1', 's2' => 'Semestre 2'] as $valeur => $libelle)
                    <a href="{{ request()->fullUrlWithQuery(['semestre' => $valeur ?: null]) }}"
                       class="seg-item {{ (string) $semestreFiltre === (string) $valeur ? 'is-active' : '' }}">{{ $libelle }}</a>
                @endforeach
            </div>
        </div>

        <div class="table-scroll">
            <table class="data-table ref-table">
                <thead>
                    <tr>
                        <th>Sem.</th>
                        <th>Niveau</th>
                        @if ($onglet === 'classe')<th>Classe</th>@endif
                        <th>Cours</th>
                        <th>Intervenant</th>
                        <th class="num">CC</th>
                        <th class="num">CR</th>
                        <th class="num">TD</th>
                        <th class="num">EI</th>
                        <th class="num">Volume</th>
                        <th class="num">ECTS</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @php $colonnesTexte = $onglet === 'classe' ? 5 : 4; @endphp
                    @forelse ($lignes->groupBy(fn ($l) => strtolower($l->semestre)) as $semestre => $groupe)
                        <tr class="group-row">
                            <td colspan="{{ $colonnesTexte + 7 }}">
                                Semestre {{ substr($semestre, 1) }}
                                <span class="group-sub">{{ $groupe->count() }} lignes · {{ $h($somme($groupe, 'volume')) }} h · {{ $h($somme($groupe, 'ects')) }} ECTS</span>
                            </td>
                        </tr>
                        @foreach ($groupe as $ligne)
                            @php
                                $intervenant = $ligne->intervenant ? $ligne->intervenant->nom.' '.$ligne->intervenant->prenom : null;
                                $initiales = $ligne->intervenant
                                    ? mb_strtoupper(mb_substr($ligne->intervenant->nom, 0, 1).mb_substr($ligne->intervenant->prenom, 0, 1))
                                    : '—';
                            @endphp
                            <tr>
                                <td><span class="tag">{{ strtoupper($ligne->semestre) }}</span></td>
                                <td class="nowrap">{{ $ligne->niveau?->nom_niveau ?? '—' }}</td>
                                @if ($onglet === 'classe')
                                    <td><span class="tag tag-violet">{{ $ligne->classe?->classe ?? '—' }}</span></td>
                                @endif
                                <td class="cell-course">{{ $ligne->cours?->nom_cours ?? '—' }}</td>
                                <td>
                                    <span class="cell-user">
                                        <span class="cell-avatar is-small">{{ $initiales }}</span>
                                        <span class="nowrap">{{ $intervenant ?? '—' }}</span>
                                    </span>
                                </td>
                                <td class="num">{{ $h($ligne->cc) }}</td>
                                <td class="num">{{ $h($ligne->cr) }}</td>
                                <td class="num">{{ $h($ligne->td) }}</td>
                                <td class="num">{{ $h($ligne->ei) }}</td>
                                <td class="num strong">{{ $h($ligne->volume) }}</td>
                                <td class="num"><span class="pill pill-brand">{{ $h($ligne->ects) }}</span></td>
                                <td class="col-actions">
                                    <form method="post"
                                          action="{{ $onglet === 'classe' ? route('referentiel.ref.classe.destroy', $ligne) : route('referentiel.ref.niveau.destroy', $ligne) }}"
                                          onsubmit="return confirm('Supprimer cette ligne ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="row-btn is-danger" title="Supprimer la ligne">
                                            @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#b91c1c'])
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="{{ $colonnesTexte + 7 }}" class="empty-cell">
                                @include('partials.icon', ['n' => 'clock', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                                <span>Aucune ligne pour ce filtre.</span>
                            </td>
                        </tr>
                    @endforelse

                    @if ($lignes->count())
                        <tr class="total-row">
                            <td colspan="{{ $colonnesTexte }}">Total {{ $onglet === 'classe' ? 'par classe' : 'par niveau' }}</td>
                            <td class="num">{{ $h($somme($lignes, 'cc')) }}</td>
                            <td class="num">{{ $h($somme($lignes, 'cr')) }}</td>
                            <td class="num">{{ $h($somme($lignes, 'td')) }}</td>
                            <td class="num">{{ $h($somme($lignes, 'ei')) }}</td>
                            <td class="num">{{ $h($somme($lignes, 'volume')) }}</td>
                            <td class="num"><span class="pill pill-solid">{{ $h($somme($lignes, 'ects')) }}</span></td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- ---------- Ajout d'une ligne ---------- --}}
        <div class="add-foot">
            <button type="button" class="add-trigger" data-toggle-add aria-expanded="false">
                @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Ajouter une ligne — {{ $onglet === 'classe' ? 'par classe' : 'par niveau' }}
            </button>
        </div>

        <form method="post"
              action="{{ $onglet === 'classe' ? route('referentiel.ref.classe.store') : route('referentiel.ref.niveau.store') }}"
              class="add-panel" hidden>
            @csrf
            <input type="hidden" name="id_etablissement" value="{{ $idEtablissement }}">
            <input type="hidden" name="id_unite_enseignement" value="{{ $idUe }}">
            <input type="hidden" name="annee" value="{{ $onglet === 'classe' ? (int) $annee : $annee }}">

            <div class="add-head">
                <strong>Ajouter une ligne — {{ $onglet === 'classe' ? 'par classe' : 'par niveau' }}</strong>
                <span class="add-context">{{ $contexte }}</span>
                <button type="button" class="row-btn" data-toggle-add aria-label="Fermer">
                    @include('partials.icon', ['n' => 'plus', 's' => 14, 'c' => '#585e72', 'w' => 2.4, 'style' => 'transform:rotate(45deg)'])
                </button>
            </div>

            <div class="add-grid">
                <label>
                    <span>Niveau</span>
                    <select name="id_niveau" required>
                        @foreach ($niveaux as $niveau)
                            <option value="{{ $niveau->id_niveau }}">{{ $niveau->nom_niveau }}</option>
                        @endforeach
                    </select>
                </label>

                @if ($onglet === 'classe')
                    <label>
                        <span>Classe</span>
                        <select name="id_classe" required>
                            @foreach ($classes as $classe)
                                <option value="{{ $classe->id_classe }}">{{ $classe->classe }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                <label>
                    <span>Semestre</span>
                    <select name="semestre" required>
                        <option value="s1">S1</option>
                        <option value="s2">S2</option>
                    </select>
                </label>

                <label>
                    <span>Cours</span>
                    <select name="id_cours" required>
                        @foreach ($cours as $c)
                            <option value="{{ $c->id_cours }}">{{ $c->nom_cours }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Intervenant</span>
                    <select name="id_intervenant" required>
                        @foreach ($intervenants as $intervenant)
                            <option value="{{ $intervenant->id_intervenant }}">{{ $intervenant->nom }} {{ $intervenant->prenom }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="add-numbers">
                @foreach (['cc' => 'CC', 'cr' => 'CR', 'td' => 'TD', 'ei' => 'EI', 'volume' => 'Volume', 'ects' => 'ECTS'] as $champ => $libelle)
                    <label>
                        <span>{{ $libelle }}</span>
                        <input type="number" step="0.01" name="{{ $champ }}" value="0">
                    </label>
                @endforeach
                <div class="add-actions">
                    <button type="button" class="btn btn-ghost" data-toggle-add>Annuler</button>
                    <button type="submit" class="btn">Ajouter la ligne</button>
                </div>
            </div>
        </form>
    </div>

    <p class="legend">CC cours magistral · CR cours renforcé · TD travaux dirigés · EI enseignement individualisé</p>
@endif

<style>
    /* Référentiel : styles spécifiques (le reste vient de layouts/app.blade.php) */
    .ctx-card { max-width: none; background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 14px; margin-bottom: 14px; }
    .ctx-row { display: flex; gap: 11px; flex-wrap: wrap; align-items: flex-end; }
    .ctx-row label { flex: 1 1 230px; min-width: 0; margin: 0; }
    .ctx-row label.ctx-wide { flex: 1 1 280px; }
    .ctx-row label.ctx-narrow { flex: 0 1 160px; min-width: 130px; }
    .ctx-row label > span { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .ctx-row select, .ctx-row input { width: 100%; max-width: none; }
    .ctx-row .btn { flex-shrink: 0; }

    .ctx-totals { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 14px; padding-top: 13px; border-top: 1px solid var(--border-soft); }
    .ctx-total { flex: 1 1 130px; min-width: 0; border: 1px solid #eef0f5; border-radius: 11px; padding: 10px 12px; }
    .ctx-total.is-brand { background: #fafbff; }
    .ctx-total-label { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .ctx-total-value { display: block; margin-top: 4px; font-size: 20px; font-weight: 700; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .ctx-total-hint { display: block; margin-top: 1px; font-size: 11.5px; color: var(--muted); }

    .tabs { display: flex; gap: 4px; background: #fff; border: 1px solid var(--border); border-radius: 11px; padding: 4px; margin-bottom: 14px; width: fit-content; }
    .tab { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; color: var(--muted); }
    .tab:hover { color: var(--ink); background: #f7f8fc; }
    .tab.is-active { background: var(--brand-light); color: var(--brand-deep); }
    .tab-badge { padding: 1px 7px; border-radius: 999px; font-size: 11px; font-weight: 700; background: #f0f1f6; color: var(--muted); }
    .tab.is-active .tab-badge { background: #fff; color: var(--brand-deep); }

    .table-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .table-head { display: flex; align-items: center; gap: 10px; padding: 12px 15px; border-bottom: 1px solid var(--border-soft); flex-wrap: wrap; }
    .table-count { font-size: 12.5px; color: var(--muted); }
    .seg { display: flex; gap: 5px; margin-left: auto; }
    .seg-item { padding: 6px 11px; border-radius: 8px; border: 1px solid var(--border); background: #fff; color: var(--muted); font-size: 12.5px; font-weight: 600; }
    .seg-item:hover { border-color: #c3c6f5; color: var(--brand); }
    .seg-item.is-active { border-color: #c3c6f5; background: var(--brand-light); color: var(--brand-deep); }

    .table-scroll { overflow-x: auto; }
    .data-table { min-width: 1020px; margin: 0; border: 0; border-radius: 0; box-shadow: none; }
    .data-table th { position: sticky; top: 0; z-index: 1; white-space: nowrap; }
    .data-table th.num, .data-table td.num { text-align: right; }
    .data-table td.num { font-variant-numeric: tabular-nums; color: #585e72; white-space: nowrap; }
    .data-table td.num.strong { font-weight: 700; color: var(--ink); }
    .data-table td { vertical-align: middle; }
    .nowrap { white-space: nowrap; }
    .cell-course { max-width: 280px; font-weight: 500; }
    .col-actions { width: 56px; text-align: right; }
    .col-actions form { display: inline-block; margin: 0; }
    .row-btn { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; border: 1px solid var(--border); background: #fff; cursor: pointer; padding: 0; }
    .row-btn:hover { border-color: #c3c6f5; background: #fafbff; }
    .row-btn.is-danger:hover { border-color: #fecaca; background: var(--danger-bg); }

    .cell-user { display: flex; align-items: center; gap: 9px; }
    .cell-avatar.is-small { width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0; background: var(--brand-light); color: var(--brand-deep); font-size: 10.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

    .tag { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 6px; background: #eef1f6; color: #475569; font-size: 11.5px; font-weight: 700; }
    .tag-violet { background: #f4ecfd; color: #6d28d9; font-weight: 600; }
    .pill { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 700; font-variant-numeric: tabular-nums; }
    .pill-brand { background: var(--brand-light); color: var(--brand-deep); }
    .pill-solid { background: var(--brand); color: #fff; }

    .group-row td { background: #fafbfd; border-bottom: 1px solid #eef0f5; padding: 7px 14px; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .group-sub { margin-left: 8px; font-weight: 600; letter-spacing: 0; text-transform: none; color: var(--faint); }
    .total-row td { border-top: 1px solid var(--border); border-bottom: 0; padding: 12px 14px; font-size: 13px; font-weight: 700; color: var(--ink); }
    .total-row td:first-child { font-size: 12.5px; }

    .empty-state { text-align: center; padding: 52px 16px; }
    .empty-state p { margin: 10px 0 0; font-size: 13px; color: var(--muted); }
    .empty-cell { text-align: center; padding: 42px 16px; }
    .empty-cell span { display: block; margin-top: 10px; font-size: 13px; color: var(--muted); }

    .add-foot { border-top: 1px solid var(--border-soft); padding: 12px 15px; }
    .add-trigger {
        display: inline-flex; align-items: center; gap: 7px; padding: 9px 14px; border-radius: 9px;
        border: 1px dashed #c9cdd9; background: #fff; color: var(--brand); font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit;
    }
    .add-trigger:hover { border-color: #a5a8f0; background: #fafbff; }
    .add-trigger svg { stroke: currentColor; }

    .add-panel { max-width: none; border-top: 1px solid var(--border); background: #fbfbff; padding: 15px; }
    .add-head { display: flex; align-items: center; gap: 9px; margin-bottom: 12px; }
    .add-head strong { font-size: 13px; }
    .add-context { font-size: 12px; color: var(--muted); }
    .add-head .row-btn { margin-left: auto; }
    .add-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 11px; }
    .add-grid label, .add-numbers label { margin: 0; min-width: 0; }
    .add-grid label > span, .add-numbers label > span { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .add-grid select { width: 100%; max-width: none; }
    .add-numbers { display: flex; gap: 11px; flex-wrap: wrap; align-items: flex-end; margin-top: 12px; padding-top: 12px; border-top: 1px solid #eef0f5; }
    .add-numbers label { flex: 0 1 92px; min-width: 80px; }
    .add-numbers input { width: 100%; max-width: none; text-align: right; font-variant-numeric: tabular-nums; }
    .add-actions { display: flex; gap: 8px; margin-left: auto; }

    .legend { margin: 14px 2px 0; font-size: 12px; color: var(--muted); }
</style>

<script>
    (function () {
        var panel = document.querySelector('.add-panel');
        var foot = document.querySelector('.add-foot');
        if (!panel) return;
        document.querySelectorAll('[data-toggle-add]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                panel.hidden = !panel.hidden;
                if (foot) foot.hidden = !panel.hidden;
            });
        });
    })();
</script>
@endsection
