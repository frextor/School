@extends('layouts.app')

@section('title', 'Référentiel pédagogique')

@section('content')
@php
    // Affichage des nombres : 2 devient « 2 », 1.5 devient « 1,5 ».
    $n = fn ($valeur) => rtrim(rtrim(number_format((float) $valeur, 1, ',', ' '), '0'), ',');

    // Valeur d'un <input type="number"> : séparateur point, sans zéro superflu.
    $saisie = fn ($valeur) => rtrim(rtrim(number_format((float) $valeur, 2, '.', ''), '0'), '.') ?: '0';

    $cycleActif = request('cycle');
    $niveauActif = request('niveau');
    $filtre = $cycleActif || $niveauActif || $recherche !== '';

    // Les fiches s'ouvrent d'office quand la liste est courte ou filtrée :
    // seize niveaux dépliés d'un coup ne se lisent pas.
    $ouvrir = $filtre || $niveaux->count() <= 3;

    $parCycle = $niveaux->groupBy(fn ($niveau) => $niveau->formation?->niveau ?: 'Sans cycle');
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Référentiel pédagogique</span>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $erreur){{ $erreur }} @endforeach</span>
    </div>
@endif

<div class="page-head">
    <div>
        <h1>Référentiel pédagogique</h1>
        <p class="page-sub">Les matières enseignées à chaque niveau, leur coefficient dans la moyenne générale et leur volume horaire hebdomadaire.</p>
    </div>
    <div class="page-actions">
        <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-ghost">
            @include('partials.icon', ['n' => 'download', 's' => 15, 'w' => 2])Exporter
        </a>
    </div>
</div>

{{-- ---------- Ce que pèse le référentiel affiché ---------- --}}
<div class="rf-stats">
    <div class="rf-stat">
        <span class="rf-stat-label">Niveaux</span>
        <span class="rf-stat-value">{{ $stats['couverts'] }}<span class="rf-stat-sur">/{{ $stats['niveaux'] }}</span></span>
        <span class="rf-stat-hint">avec un référentiel</span>
    </div>
    <div class="rf-stat">
        <span class="rf-stat-label">Matières</span>
        <span class="rf-stat-value">{{ $stats['matieres'] }}</span>
        <span class="rf-stat-hint">{{ $stats['lignes'] }} ligne{{ $stats['lignes'] > 1 ? 's' : '' }} au total</span>
    </div>
    <div class="rf-stat is-brand">
        <span class="rf-stat-label">Volume hebdomadaire</span>
        <span class="rf-stat-value">{{ $n($stats['heures']) }} h</span>
        <span class="rf-stat-hint">tous niveaux confondus</span>
    </div>
    <div class="rf-stat {{ $stats['sansHeures'] ? 'is-alerte' : '' }}">
        <span class="rf-stat-label">Heures à renseigner</span>
        <span class="rf-stat-value">{{ $stats['sansHeures'] }}</span>
        <span class="rf-stat-hint">{{ $stats['sansHeures'] ? 'matières encore à 0 h' : 'référentiel complet' }}</span>
    </div>
</div>

{{-- ---------- Filtres ---------- --}}
<form method="get" action="{{ route('referentiel.ref.index') }}" class="rf-filtres">
    <div class="rf-cycles">
        <a href="{{ route('referentiel.ref.index') }}" class="rf-cycle {{ $cycleActif ? '' : 'is-active' }}">Tous les cycles</a>
        @foreach ($cycles as $cycle)
            <a href="{{ route('referentiel.ref.index', ['cycle' => $cycle->id_formation]) }}"
               class="rf-cycle {{ (string) $cycleActif === (string) $cycle->id_formation ? 'is-active' : '' }}">{{ $cycle->niveau }}</a>
        @endforeach
    </div>

    <div class="rf-recherche">
        @if ($cycleActif)<input type="hidden" name="cycle" value="{{ $cycleActif }}">@endif

        <label class="rf-champ">
            <span>Niveau</span>
            <select name="niveau" onchange="this.form.submit()">
                <option value="">Tous les niveaux</option>
                @foreach ($niveauxTous as $nomCycle => $niveauxDuCycle)
                    <optgroup label="{{ $nomCycle }}">
                        @foreach ($niveauxDuCycle as $niveau)
                            <option value="{{ $niveau->id_niveau }}" @selected((string) $niveauActif === (string) $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </label>

        <label class="rf-champ rf-champ-large">
            <span>Matière</span>
            <input type="text" name="recherche" value="{{ $recherche }}" placeholder="Arabe, Mathématiques…">
        </label>

        <button type="submit" class="btn">Filtrer</button>
        @if ($filtre)
            <a href="{{ route('referentiel.ref.index') }}" class="btn btn-ghost">Réinitialiser</a>
        @endif
    </div>
</form>

{{-- ---------- Le référentiel, cycle par cycle ---------- --}}
@forelse ($parCycle as $nomCycle => $niveauxCycle)
    <div class="rf-cycle-tete">
        <h2>{{ $nomCycle }}</h2>
        <span>{{ $niveauxCycle->count() }} niveau{{ $niveauxCycle->count() > 1 ? 'x' : '' }}</span>
    </div>

    @foreach ($niveauxCycle as $niveau)
        @php
            $lignes = $niveau->matieres;
            $totalCoef = $lignes->sum(fn ($m) => (float) $m->pivot->coefficient);
            $totalHeures = $lignes->sum(fn ($m) => (float) $m->pivot->volume_horaire);
            $dispo = $matieres->whereNotIn('id_cours', $lignes->pluck('id_cours'));
        @endphp

        <details class="rf-niveau" @if($ouvrir) open @endif>
            <summary class="rf-niveau-tete">
                <span class="rf-code">{{ $niveau->code_niveau }}</span>
                <span class="rf-niveau-id">
                    <span class="rf-niveau-nom">{{ $niveau->nom_niveau }}</span>
                    <span class="rf-niveau-sub">{{ $niveau->classes_count }} classe{{ $niveau->classes_count > 1 ? 's' : '' }}</span>
                </span>
                <span class="rf-resume">
                    <span class="rf-mesure"><strong>{{ $lignes->count() }}</strong> matière{{ $lignes->count() > 1 ? 's' : '' }}</span>
                    <span class="rf-mesure"><strong>{{ $n($totalCoef) }}</strong> de coefficient</span>
                    <span class="rf-mesure rf-mesure-h"><strong>{{ $n($totalHeures) }} h</strong> / semaine</span>
                </span>
                <span class="rf-chevron">@include('partials.icon', ['n' => 'chevron-down', 's' => 15, 'c' => '#9aa0b0', 'w' => 2.2])</span>
            </summary>

            <div class="rf-niveau-corps">
                @if ($lignes->isEmpty())
                    <div class="rf-vide">
                        @include('partials.icon', ['n' => 'book', 's' => 24, 'c' => '#c9cdd9', 'w' => 1.6])
                        <p>Aucune matière au référentiel de ce niveau.</p>
                        <span>Sans matière, ni les notes ni les bulletins de ce niveau ne peuvent être saisis.</span>
                    </div>
                @else
                    {{-- Tout le niveau s'enregistre d'un bloc : on règle une grille
                         horaire en une fois, pas matière par matière. --}}
                    <form method="post" action="{{ route('referentiel.ref.niveau.save', $niveau) }}" data-grille>
                        @csrf
                        <div class="rf-scroll">
                            <table class="rf-table">
                                <thead>
                                    <tr>
                                        <th class="rf-col-rang">Rang</th>
                                        <th>Matière</th>
                                        <th class="rf-col-num">Coefficient</th>
                                        <th class="rf-col-num">Heures / semaine</th>
                                        <th class="rf-col-part">Part de la moyenne</th>
                                        <th class="rf-col-act"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lignes as $matiere)
                                        @php
                                            $coef = (float) $matiere->pivot->coefficient;
                                            $part = $totalCoef > 0 ? round($coef / $totalCoef * 100) : 0;
                                        @endphp
                                        <tr>
                                            <td class="rf-col-rang">
                                                <input type="number" min="1" max="255" class="rf-mini"
                                                       name="lignes[{{ $matiere->id_cours }}][ordre]"
                                                       value="{{ (int) $matiere->pivot->ordre }}" aria-label="Rang de {{ $matiere->nom_cours }}">
                                            </td>
                                            <td class="rf-matiere">{{ $matiere->nom_cours }}</td>
                                            <td class="rf-col-num">
                                                <input type="number" step="0.5" min="0.5" max="20" required class="rf-num"
                                                       name="lignes[{{ $matiere->id_cours }}][coefficient]"
                                                       value="{{ $saisie($coef) }}"
                                                       aria-label="Coefficient de {{ $matiere->nom_cours }}">
                                            </td>
                                            <td class="rf-col-num">
                                                <input type="number" step="0.5" min="0" max="60" class="rf-num"
                                                       name="lignes[{{ $matiere->id_cours }}][volume_horaire]"
                                                       value="{{ $saisie($matiere->pivot->volume_horaire) }}"
                                                       aria-label="Heures hebdomadaires de {{ $matiere->nom_cours }}">
                                            </td>
                                            <td class="rf-col-part">
                                                <span class="rf-jauge" aria-hidden="true"><span style="width:{{ min(100, $part) }}%"></span></span>
                                                <span class="rf-part">{{ $part }} %</span>
                                            </td>
                                            <td class="rf-col-act">
                                                {{-- Le formulaire de retrait vit hors de la grille (les
                                                     formulaires ne s'imbriquent pas) : l'attribut `form`
                                                     le relie à ce bouton. --}}
                                                <button type="submit" class="row-btn is-danger"
                                                        form="rf-suppr-{{ $niveau->id_niveau }}-{{ $matiere->id_cours }}"
                                                        title="Retirer {{ $matiere->nom_cours }} de ce niveau">
                                                    @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#b91c1c', 'w' => 2])
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">Total</td>
                                        <td class="rf-col-num">{{ $n($totalCoef) }}</td>
                                        <td class="rf-col-num">{{ $n($totalHeures) }} h</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="rf-actions">
                            <button type="button" class="rf-ajout-btn" data-ouvrir-ajout="{{ $niveau->id_niveau }}">
                                @include('partials.icon', ['n' => 'plus', 's' => 14, 'w' => 2.2])Ajouter une matière
                            </button>
                            <button type="button" class="rf-ajout-btn" data-dupliquer="{{ $niveau->id_niveau }}"
                                    data-nom="{{ $niveau->nom_niveau }}">
                                @include('partials.icon', ['n' => 'copy', 's' => 14, 'w' => 2])Recopier vers d'autres niveaux
                            </button>
                            <span class="rf-dirty" data-dirty hidden>Modifications non enregistrées</span>
                            <button type="submit" class="btn">Enregistrer</button>
                        </div>
                    </form>

                    @foreach ($lignes as $matiere)
                        <form method="post" id="rf-suppr-{{ $niveau->id_niveau }}-{{ $matiere->id_cours }}"
                              action="{{ route('referentiel.niveaux.matieres.destroy', [$niveau, $matiere->id_cours]) }}"
                              class="rf-suppr"
                              onsubmit="return confirm('Retirer « {{ $matiere->nom_cours }} » du référentiel de {{ $niveau->nom_niveau }} ?\n\nLes notes déjà saisies dans cette matière ne sont pas supprimées, mais elle ne comptera plus dans la moyenne.')">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                @endif

                {{-- ---------- Ajout d'une matière à ce niveau ---------- --}}
                <form method="post" action="{{ route('referentiel.niveaux.matieres.store', $niveau) }}"
                      class="rf-ajout" data-ajout="{{ $niveau->id_niveau }}" @if($lignes->isNotEmpty()) hidden @endif>
                    @csrf
                    @if ($dispo->isEmpty())
                        <p class="rf-ajout-vide">Toutes les matières du catalogue sont déjà rattachées à ce niveau.</p>
                    @else
                        <div class="rf-ajout-grid">
                            <label class="rf-champ rf-champ-large">
                                <span>Matière</span>
                                <select name="id_cours" required>
                                    @foreach ($dispo as $matiere)
                                        <option value="{{ $matiere->id_cours }}">{{ $matiere->nom_cours }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="rf-champ">
                                <span>Coefficient</span>
                                <input type="number" step="0.5" min="0.5" max="20" name="coefficient" value="1" required>
                            </label>
                            <label class="rf-champ">
                                <span>Heures / semaine</span>
                                <input type="number" step="0.5" min="0" max="60" name="volume_horaire" value="0">
                            </label>
                            <div class="rf-ajout-actions">
                                @if ($lignes->isNotEmpty())
                                    <button type="button" class="btn btn-ghost" data-fermer-ajout="{{ $niveau->id_niveau }}">Annuler</button>
                                @endif
                                <button type="submit" class="btn">Ajouter</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </details>
    @endforeach
@empty
    <div class="rf-aucun">
        @include('partials.icon', ['n' => 'book', 's' => 28, 'c' => '#c9cdd9', 'w' => 1.6])
        <p>Aucun niveau ne correspond à ces filtres</p>
        <span>Élargissez la recherche, ou créez les niveaux depuis « Niveaux &amp; cycles ».</span>
        <a href="{{ route('referentiel.ref.index') }}">Réinitialiser les filtres</a>
    </div>
@endforelse

{{-- ---------- Recopier un référentiel ---------- --}}
<div class="rf-modal" data-modal hidden>
    <div class="rf-modal-fond" data-fermer></div>
    <form method="post" action="{{ route('referentiel.ref.dupliquer') }}" class="rf-modal-boite" role="dialog" aria-modal="true" aria-labelledby="rf-modal-titre">
        @csrf
        <input type="hidden" name="source" data-source value="">

        <div class="rf-modal-tete">
            <div>
                <h2 id="rf-modal-titre">Recopier un référentiel</h2>
                <p class="rf-modal-sub">Depuis <strong data-nom-source>—</strong> vers les niveaux cochés.</p>
            </div>
            <button type="button" class="rf-modal-x" data-fermer aria-label="Fermer">
                @include('partials.icon', ['n' => 'close', 's' => 15, 'c' => '#585e72', 'w' => 2.2])
            </button>
        </div>

        <div class="rf-modal-corps">
            <span class="rf-label">Niveaux de destination</span>
            @foreach ($niveauxTous as $nomCycle => $niveauxDuCycle)
                <div class="rf-groupe">
                    <span class="rf-groupe-nom">{{ $nomCycle }}</span>
                    <div class="rf-cases">
                        @foreach ($niveauxDuCycle as $niveau)
                            <label class="rf-case" data-case="{{ $niveau->id_niveau }}">
                                <input type="checkbox" name="cibles[]" value="{{ $niveau->id_niveau }}">
                                <span>{{ $niveau->nom_niveau }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <span class="rf-label rf-label-2">Que faire des matières déjà présentes ?</span>
            <label class="rf-mode">
                <input type="radio" name="mode" value="completer" checked>
                <span>
                    <strong>Compléter</strong>
                    Ajoute les matières manquantes et laisse intacts les coefficients déjà réglés.
                </span>
            </label>
            <label class="rf-mode">
                <input type="radio" name="mode" value="remplacer">
                <span>
                    <strong>Remplacer</strong>
                    Aligne exactement le niveau cible sur la source : les matières absentes de la source en sont retirées.
                </span>
            </label>
        </div>

        <div class="rf-modal-pied">
            <button type="button" class="btn btn-ghost" data-fermer>Annuler</button>
            <button type="submit" class="btn">Recopier</button>
        </div>
    </form>
</div>

<style>
    /* Référentiel pédagogique : une fiche dépliable par niveau, sa grille
       horaire éditable d'un bloc. Le reste vient de layouts/app.blade.php. */
    .rf-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; margin-bottom: 14px; }
    .rf-stat { background: var(--surface); border: 1px solid var(--border); border-radius: 13px; padding: 13px 15px; }
    .rf-stat.is-brand { background: #fafbff; border-color: #dfe2fb; }
    .rf-stat.is-alerte { background: #fffaf3; border-color: #fbe3c2; }
    .rf-stat-label { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .rf-stat-value { display: block; margin-top: 5px; font-size: 24px; font-weight: 700; letter-spacing: -.025em; font-variant-numeric: tabular-nums; }
    .rf-stat-sur { font-size: 15px; font-weight: 600; color: var(--faint); }
    .rf-stat-hint { display: block; margin-top: 2px; font-size: 11.5px; color: var(--muted); }
    .rf-stat.is-brand .rf-stat-value { color: var(--brand-deep); }
    .rf-stat.is-alerte .rf-stat-value { color: #b45309; }

    .rf-filtres { max-width: none; background: var(--surface); border: 1px solid var(--border); border-radius: 13px; padding: 13px 14px; margin-bottom: 18px; }
    .rf-cycles { display: flex; flex-wrap: wrap; gap: 6px; padding-bottom: 12px; margin-bottom: 12px; border-bottom: 1px solid var(--border-soft); }
    .rf-cycle { padding: 7px 14px; border-radius: 999px; border: 1px solid var(--border); font-size: 12.5px; font-weight: 600; color: #585e72; }
    .rf-cycle:hover { border-color: #c3c6f5; color: var(--brand); }
    .rf-cycle.is-active { border-color: var(--brand); background: var(--brand-light); color: var(--brand-deep); }
    .rf-recherche { display: flex; gap: 11px; flex-wrap: wrap; align-items: flex-end; }
    .rf-champ { flex: 0 1 200px; min-width: 150px; margin: 0; }
    .rf-champ-large { flex: 1 1 240px; }
    .rf-champ > span { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .rf-champ input, .rf-champ select { width: 100%; max-width: none; }
    .rf-recherche .btn { flex-shrink: 0; }

    .rf-cycle-tete { display: flex; align-items: baseline; gap: 10px; margin: 22px 2px 10px; }
    .rf-cycle-tete h2 { margin: 0; font-size: 13px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); }
    .rf-cycle-tete span { font-size: 12px; color: var(--faint); }

    .rf-niveau { background: var(--surface); border: 1px solid var(--border); border-radius: 13px; margin-bottom: 10px; overflow: hidden; }
    .rf-niveau[open] { border-color: #dfe2fb; box-shadow: var(--shadow-sm); }
    .rf-niveau-tete { display: flex; align-items: center; gap: 13px; padding: 13px 15px; cursor: pointer; list-style: none; }
    .rf-niveau-tete::-webkit-details-marker { display: none; }
    .rf-niveau-tete:hover { background: #fafbfd; }
    .rf-niveau[open] .rf-niveau-tete { border-bottom: 1px solid var(--border-soft); }
    .rf-niveau[open] .rf-chevron { transform: rotate(180deg); }
    .rf-chevron { display: inline-flex; flex-shrink: 0; transition: transform .15s ease; }
    .rf-code {
        flex-shrink: 0; min-width: 52px; padding: 5px 9px; border-radius: 8px; text-align: center;
        background: var(--brand-light); color: var(--brand-deep); font-size: 11.5px; font-weight: 700; letter-spacing: .02em;
    }
    .rf-niveau-id { min-width: 0; margin-right: auto; }
    .rf-niveau-nom { display: block; font-size: 14.5px; font-weight: 700; letter-spacing: -.015em; }
    .rf-niveau-sub { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
    .rf-resume { display: flex; gap: 16px; flex-wrap: wrap; }
    .rf-mesure { font-size: 12px; color: var(--muted); white-space: nowrap; }
    .rf-mesure strong { color: var(--ink); font-weight: 700; font-variant-numeric: tabular-nums; }
    .rf-mesure-h strong { color: var(--brand-deep); }

    /* `layouts/app.blade.php` borne les formulaires à 620 px (formulaires de
       saisie) : une grille horaire, elle, occupe toute la fiche. */
    .rf-niveau-corps { padding: 0; }
    .rf-niveau-corps form { max-width: none; margin: 0; }
    .rf-scroll { overflow-x: auto; }
    .rf-table { width: 100%; min-width: 660px; margin: 0; border: 0; border-radius: 0; box-shadow: none; border-collapse: collapse; }
    .rf-table th {
        padding: 9px 14px; text-align: left; font-size: 10.5px; font-weight: 700; letter-spacing: .05em;
        text-transform: uppercase; color: var(--muted); background: #fafbfd; border-bottom: 1px solid var(--border-soft); white-space: nowrap;
    }
    .rf-table td { padding: 8px 14px; border-bottom: 1px solid var(--border-soft); vertical-align: middle; font-size: 13.5px; }
    .rf-table tbody tr:last-child td { border-bottom: 0; }
    .rf-matiere { font-weight: 600; }
    .rf-col-rang { width: 74px; }
    .rf-col-num { width: 128px; text-align: right; }
    .rf-col-part { width: 168px; }
    .rf-col-act { width: 56px; text-align: right; }
    .rf-table input.rf-num, .rf-table input.rf-mini {
        max-width: none; margin: 0; padding: 6px 9px; text-align: right;
        font-size: 13px; font-variant-numeric: tabular-nums;
    }
    .rf-table input.rf-num { width: 96px; }
    .rf-table input.rf-mini { width: 58px; color: var(--muted); }
    .rf-table tfoot td {
        padding: 11px 14px; border-top: 1px solid var(--border); border-bottom: 0;
        font-size: 12.5px; font-weight: 700; color: var(--ink); font-variant-numeric: tabular-nums;
    }
    /* Le total tombe sous les chiffres saisis : l'input a sa propre marge
       intérieure, la cellule du pied doit la compenser. */
    .rf-table tfoot td.rf-col-num { padding-right: 23px; }
    .rf-table .row-btn.is-danger:hover { border-color: #fecaca; background: var(--danger-bg); }
    .rf-jauge { display: block; height: 5px; border-radius: 999px; background: #eef0f5; overflow: hidden; }
    .rf-jauge > span { display: block; height: 100%; border-radius: 999px; background: var(--brand); }
    .rf-part { display: block; margin-top: 3px; font-size: 11px; color: var(--muted); font-variant-numeric: tabular-nums; }

    .rf-actions { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; padding: 12px 15px; border-top: 1px solid var(--border-soft); background: #fcfcfe; }
    .rf-actions .btn:last-child { margin-left: auto; }
    .rf-ajout-btn {
        display: inline-flex; align-items: center; gap: 6px; padding: 8px 13px; border-radius: 9px;
        border: 1px dashed #c9cdd9; background: #fff; color: var(--brand); font-family: inherit;
        font-size: 12.5px; font-weight: 600; cursor: pointer;
    }
    .rf-ajout-btn:hover { border-color: #a5a8f0; background: #fafbff; }
    .rf-ajout-btn svg { stroke: currentColor; }
    .rf-dirty { font-size: 12px; font-weight: 600; color: #b45309; }
    .rf-dirty[hidden] { display: none; }

    .rf-ajout { max-width: none; margin: 0; padding: 14px 15px; border-top: 1px solid var(--border-soft); background: #fbfbff; }
    .rf-ajout[hidden] { display: none; }
    .rf-ajout-grid { display: flex; gap: 11px; flex-wrap: wrap; align-items: flex-end; }
    .rf-ajout-actions { display: flex; gap: 8px; margin-left: auto; }
    .rf-ajout-vide { margin: 0; font-size: 12.5px; color: var(--muted); }
    .rf-suppr { display: none; }

    .rf-vide { text-align: center; padding: 34px 16px; }
    .rf-vide p { margin: 9px 0 0; font-size: 13.5px; font-weight: 600; }
    .rf-vide span { display: block; margin: 3px auto 0; max-width: 52ch; font-size: 12.5px; color: var(--muted); }

    .rf-aucun { text-align: center; padding: 48px 20px; background: var(--surface); border: 1px solid var(--border); border-radius: 14px; }
    .rf-aucun p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .rf-aucun span { display: block; margin: 4px auto 0; max-width: 54ch; font-size: 13px; color: var(--muted); }
    .rf-aucun a { display: inline-block; margin-top: 10px; font-size: 12.5px; font-weight: 600; }

    /* Modale de recopie. `hidden` doit primer sur `display:flex`. */
    .rf-modal { position: fixed; inset: 0; z-index: 60; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .rf-modal[hidden] { display: none; }
    .rf-modal-fond { position: absolute; inset: 0; background: rgba(15, 18, 34, .45); }
    .rf-modal-boite {
        position: relative; max-width: 620px; width: 100%; max-height: 88vh; overflow-y: auto; margin: 0;
        background: #fff; border-radius: 16px; box-shadow: var(--shadow-lg);
    }
    .rf-modal-tete { display: flex; align-items: flex-start; gap: 12px; padding: 17px 18px 14px; border-bottom: 1px solid var(--border-soft); }
    .rf-modal-tete h2 { margin: 0; font-size: 16.5px; letter-spacing: -.02em; }
    .rf-modal-sub { margin: 3px 0 0; font-size: 12.5px; color: var(--muted); }
    .rf-modal-x {
        margin-left: auto; flex-shrink: 0; width: 30px; height: 30px; padding: 0; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); border-radius: 9px; background: #fff;
    }
    .rf-modal-x:hover { border-color: #c3c6f5; }
    .rf-modal-corps { padding: 16px 18px; }
    .rf-label { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
    .rf-label-2 { margin-top: 18px; }
    .rf-groupe + .rf-groupe { margin-top: 11px; }
    .rf-groupe[hidden] { display: none; }
    .rf-groupe-nom { display: block; font-size: 11.5px; font-weight: 600; color: var(--faint); margin-bottom: 6px; }
    .rf-cases { display: flex; flex-wrap: wrap; gap: 7px; }
    .rf-case {
        display: inline-flex; align-items: center; gap: 7px; margin: 0; padding: 6px 12px;
        border: 1px solid var(--border); border-radius: 999px; background: #fff;
        font-size: 12.5px; font-weight: 600; color: #585e72; cursor: pointer;
    }
    .rf-case:hover { border-color: #c3c6f5; }
    .rf-case input { width: 14px; height: 14px; max-width: none; margin: 0; accent-color: var(--brand); cursor: pointer; }
    .rf-case:has(input:checked) { border-color: var(--brand); background: var(--brand-light); color: var(--brand-deep); }
    .rf-case[hidden] { display: none; }
    .rf-mode { display: flex; gap: 10px; align-items: flex-start; margin: 0 0 8px; padding: 11px 13px; border: 1px solid var(--border); border-radius: 11px; cursor: pointer; }
    .rf-mode:has(input:checked) { border-color: var(--brand); background: #fafbff; }
    .rf-mode input { width: 15px; height: 15px; max-width: none; margin: 2px 0 0; accent-color: var(--brand); flex-shrink: 0; }
    .rf-mode strong { display: block; font-size: 13px; margin-bottom: 2px; }
    .rf-mode span { font-size: 12px; color: var(--muted); line-height: 1.45; }
    .rf-modal-pied { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-top: 1px solid var(--border-soft); }
    .rf-modal-pied .btn:last-child { margin-left: auto; }

    @media (max-width: 760px) {
        .rf-resume { display: none; }
        .rf-actions .btn:last-child { margin-left: 0; width: 100%; }
    }
</style>

<script>
    (function () {
        // Ajout d'une matière : le panneau reste replié tant qu'on ne le demande pas.
        function panneau(id) { return document.querySelector('[data-ajout="' + id + '"]'); }

        document.querySelectorAll('[data-ouvrir-ajout]').forEach(function (bouton) {
            bouton.addEventListener('click', function () {
                var cible = panneau(bouton.dataset.ouvrirAjout);
                if (!cible) return;
                cible.hidden = false;
                var champ = cible.querySelector('select, input');
                if (champ) champ.focus();
            });
        });

        document.querySelectorAll('[data-fermer-ajout]').forEach(function (bouton) {
            bouton.addEventListener('click', function () {
                var cible = panneau(bouton.dataset.fermerAjout);
                if (cible) cible.hidden = true;
            });
        });

        // Une grille se règle champ par champ : sans repère, on quitte la page
        // en croyant avoir enregistré.
        document.querySelectorAll('[data-grille]').forEach(function (form) {
            var temoin = form.querySelector('[data-dirty]');
            form.addEventListener('input', function () { if (temoin) temoin.hidden = false; });
        });

        // ---- Recopie d'un référentiel ----
        var modal = document.querySelector('[data-modal]');
        if (!modal) return;

        var source = modal.querySelector('[data-source]');
        var nomSource = modal.querySelector('[data-nom-source]');

        function ouvrir(id, nom) {
            source.value = id;
            nomSource.textContent = nom;

            // Le niveau source ne peut pas être sa propre destination.
            modal.querySelectorAll('[data-case]').forEach(function (etiquette) {
                var estSource = etiquette.dataset.case === String(id);
                etiquette.hidden = estSource;
                if (estSource) etiquette.querySelector('input').checked = false;
            });

            // Un cycle d'un seul niveau, celui-là même qu'on recopie, ne doit
            // pas laisser un intitulé au-dessus d'une liste vide.
            modal.querySelectorAll('.rf-groupe').forEach(function (groupe) {
                groupe.hidden = !groupe.querySelector('[data-case]:not([hidden])');
            });

            modal.hidden = false;
            document.body.style.overflow = 'hidden';
        }

        function fermer() {
            modal.hidden = true;
            document.body.style.overflow = '';
        }

        document.querySelectorAll('[data-dupliquer]').forEach(function (bouton) {
            bouton.addEventListener('click', function () { ouvrir(bouton.dataset.dupliquer, bouton.dataset.nom); });
        });

        modal.querySelectorAll('[data-fermer]').forEach(function (element) {
            element.addEventListener('click', fermer);
        });

        document.addEventListener('keydown', function (evenement) {
            if (evenement.key === 'Escape' && !modal.hidden) fermer();
        });
    })();
</script>
@endsection
