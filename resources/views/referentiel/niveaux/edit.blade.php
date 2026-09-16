@extends('layouts.app')

@section('title', 'Modifier niveau')

@section('content')
@php
    $options = $niveau->options()->orderBy('annee', 'desc')->orderBy('ordre')->get();
    $matieres = $niveau->matieres;
    $somme = (float) $matieres->sum(fn ($m) => (float) $m->pivot->coefficient);
    $couleurs = ['#4f46e5', '#0f766e', '#b45309', '#7c3aed', '#be123c', '#475569'];
    $anneeCourante = (int) date('Y');
    $euro = fn ($v) => number_format((float) $v, 0, ',', ' ').' DH';
    $nombre = fn ($v) => rtrim(rtrim(number_format((float) $v, 1, ',', ' '), '0'), ',');
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <a href="{{ route('referentiel.niveaux.index') }}">Niveaux</a>
    <span class="sep">/</span>
    <span class="current">{{ $niveau->code_niveau }}</span>
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

{{-- ---------- En-tête ---------- --}}
<div class="nv-card">
    <div class="nv-row">
        <span class="nv-code">{{ $niveau->code_niveau }}</span>

        <div class="nv-main">
            <div class="nv-title">
                <h1>{{ $niveau->nom_niveau }}</h1>
                @if ($niveau->formation)
                    <span class="pill pill-slate">{{ $niveau->formation->niveau }}</span>
                @endif
            </div>
            <p class="nv-sub">
                @if ($niveau->niveauFuture)
                    Passage automatique vers {{ $niveau->niveauFuture->nom_niveau }}
                @else
                    Aucun passage automatique configuré
                @endif
            </p>
        </div>

        <div class="nv-actions">
            <a href="{{ route('referentiel.niveaux.index') }}" class="btn btn-ghost">
                @include('partials.icon', ['n' => 'arrow-left', 's' => 15, 'w' => 2])Tous les niveaux
            </a>
            <button type="submit" form="niveau-form" class="btn">
                @include('partials.icon', ['n' => 'check-simple', 's' => 15, 'w' => 2])Enregistrer
            </button>
        </div>
    </div>

    <div class="tabs-bar" role="tablist">
        <button type="button" class="tab is-active" data-tab="identite" role="tab">
            Identité<span class="tab-badge">3</span>
        </button>
        <button type="button" class="tab" data-tab="matieres" role="tab">
            Matières<span class="tab-badge">{{ $matieres->count() }}</span>
        </button>
        <button type="button" class="tab" data-tab="options" role="tab">
            Options facturables<span class="tab-badge">{{ $options->count() }}</span>
        </button>
    </div>
</div>

<div class="nv-grid">
    <div class="nv-col">

        {{-- ---------- Identité ---------- --}}
        <div data-panel="identite">
            <section class="panel panel-pad">
                <h2 class="panel-title">Identité du niveau</h2>

                <form method="post" action="{{ route('referentiel.niveaux.update', $niveau) }}" id="niveau-form" class="nv-form">
                    @csrf
                    @method('PUT')

                    <div class="id-pair">
                        <label class="stack">
                            <span>Code</span>
                            <input type="text" name="code_niveau" required class="is-code"
                                   value="{{ old('code_niveau', $niveau->code_niveau) }}">
                        </label>
                        <label class="stack is-wide">
                            <span>Nom</span>
                            <input type="text" name="nom_niveau" required
                                   value="{{ old('nom_niveau', $niveau->nom_niveau) }}">
                        </label>
                    </div>

                    <label class="stack">
                        <span>Cycle</span>
                        <select name="id_formation" required>
                            @foreach ($formations as $formation)
                                <option value="{{ $formation->id_formation }}" @selected(old('id_formation', $niveau->id_formation) == $formation->id_formation)>{{ $formation->niveau }}</option>
                            @endforeach
                        </select>
                    </label>

                    <div class="divider">
                        <label class="stack">
                            <span>Niveau suivant</span>
                            <select name="id_niveau_future" data-suivant>
                                <option value="">— Aucun —</option>
                                @foreach ($niveaux as $autre)
                                    <option value="{{ $autre->id_niveau }}" @selected(old('id_niveau_future', $niveau->id_niveau_future) == $autre->id_niveau)>{{ $autre->nom_niveau }}</option>
                                @endforeach
                            </select>
                        </label>
                        <p class="hint-line" data-suivant-aide></p>
                    </div>
                </form>
            </section>
        </div>

        {{-- ---------- Matières ---------- --}}
        <div data-panel="matieres" hidden>
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2>Matières et coefficients</h2>
                        <p class="panel-sub">La moyenne générale du bulletin est pondérée par ces coefficients.</p>
                    </div>
                    <span class="pill pill-brand">total {{ $nombre($somme) }}</span>
                </div>

                @forelse ($matieres as $index => $matiere)
                    @php
                        $coef = (float) $matiere->pivot->coefficient;
                        $part = $somme > 0 ? round($coef / $somme * 100) : 0;
                        $couleur = $couleurs[$index % count($couleurs)];
                    @endphp
                    <form method="post" action="{{ route('referentiel.niveaux.matieres.update', [$niveau, $matiere->id_cours]) }}" class="mat-row">
                        @csrf
                        <input type="hidden" name="_method" value="PUT" data-method>

                        <span class="mat-ordre">{{ $matiere->pivot->ordre ?: $index + 1 }}</span>
                        <input type="hidden" name="ordre" value="{{ $matiere->pivot->ordre ?: $index + 1 }}">

                        <span class="mat-text">
                            <span class="mat-nom">{{ $matiere->nom_cours }}</span>
                            <span class="mat-poids">{{ $somme > 0 ? $part.' % de la moyenne générale' : '—' }}</span>
                        </span>

                        <span class="mat-coef">
                            <button type="button" class="step" data-step="-0.5" aria-label="Diminuer">
                                @include('partials.icon', ['n' => 'minus', 's' => 13, 'c' => '#585e72', 'w' => 2.4])
                            </button>
                            <input type="number" step="0.5" min="0.5" max="20" name="coefficient"
                                   value="{{ $coef }}" required data-coef>
                            <button type="button" class="step" data-step="0.5" aria-label="Augmenter">
                                @include('partials.icon', ['n' => 'plus', 's' => 13, 'c' => '#585e72', 'w' => 2.4])
                            </button>

                            <button type="submit" class="save" title="Enregistrer le coefficient">
                                @include('partials.icon', ['n' => 'check-simple', 's' => 14, 'c' => '#585e72', 'w' => 2.2])
                            </button>
                            <button type="submit" class="remove"
                                    formaction="{{ route('referentiel.niveaux.matieres.destroy', [$niveau, $matiere->id_cours]) }}"
                                    onclick="this.closest('form').querySelector('[data-method]').value='DELETE'; return confirm('Retirer cette matière du niveau ?')"
                                    title="Retirer du niveau">
                                @include('partials.icon', ['n' => 'close', 's' => 14, 'c' => '#b91c1c', 'w' => 2])
                            </button>
                        </span>

                        <span class="mat-bar"><span style="width:{{ $part }}%;background:{{ $couleur }}"></span></span>
                    </form>
                @empty
                    <p class="cell-empty">Aucune matière rattachée à ce niveau : la moyenne générale ne pourra pas être pondérée.</p>
                @endforelse

                <div class="add-foot">
                    @if ($matieresDisponibles->isEmpty())
                        <p class="add-empty">Toutes les matières sont déjà rattachées à ce niveau.</p>
                    @else
                        <form method="post" action="{{ route('referentiel.niveaux.matieres.store', $niveau) }}" class="add-form">
                            @csrf
                            <label class="mini" style="flex:1 1 200px">
                                <span>Ajouter une matière</span>
                                <select name="id_cours" required>
                                    @foreach ($matieresDisponibles as $cours)
                                        <option value="{{ $cours->id_cours }}">{{ $cours->nom_cours }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="mini" style="flex:0 1 92px">
                                <span>Coefficient</span>
                                <input type="number" step="0.5" min="0.5" max="20" name="coefficient" value="1" required class="coef-input">
                            </label>
                            <input type="hidden" name="ordre" value="{{ $matieres->count() + 1 }}">
                            <button type="submit" class="btn">
                                @include('partials.icon', ['n' => 'plus', 's' => 14, 'w' => 2.4])Ajouter
                            </button>
                        </form>
                    @endif
                </div>
            </section>
        </div>

        {{-- ---------- Options facturables ---------- --}}
        <div data-panel="options" hidden>
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2>Options facturables</h2>
                        <p class="panel-sub">Proposées, avec leur montant, lors de la création d'un règlement pour un élève de ce niveau.</p>
                    </div>
                    <span class="pill pill-teal">{{ $euro($options->where('annee', $anneeCourante)->sum('montant')) }} en {{ $anneeCourante }}</span>
                </div>

                @forelse ($options->groupBy('annee') as $annee => $liste)
                    <div class="year-head">
                        <span class="year-label">Année {{ $annee }}</span>
                        <span class="year-total">{{ $euro($liste->sum('montant')) }}</span>
                    </div>

                    @foreach ($liste as $option)
                        <form method="post" action="{{ route('referentiel.niveaux.options.update', [$niveau, $option]) }}" class="opt-row">
                            @csrf
                            <input type="hidden" name="_method" value="PUT" data-method>
                            <input type="hidden" name="annee" value="{{ $option->annee }}">
                            <input type="hidden" name="ordre" value="{{ $option->ordre }}">

                            <input type="text" name="titre" value="{{ $option->titre }}" required class="opt-titre">

                            <select name="id_objet_paiement" required class="opt-objet">
                                @foreach ($objetsPaiement as $objet)
                                    <option value="{{ $objet->id_objet_paiement }}" @selected($option->id_objet_paiement == $objet->id_objet_paiement)>{{ $objet->objet_paiement }}</option>
                                @endforeach
                            </select>

                            <span class="opt-amount">
                                <input type="number" step="0.01" name="montant" value="{{ $option->montant }}" required>
                                <span class="opt-euro">DH</span>
                            </span>

                            <button type="submit" class="save" title="Enregistrer l'option">
                                @include('partials.icon', ['n' => 'check-simple', 's' => 14, 'c' => '#585e72', 'w' => 2.2])
                            </button>
                            <button type="submit" class="remove"
                                    formaction="{{ route('referentiel.niveaux.options.destroy', [$niveau, $option]) }}"
                                    onclick="this.closest('form').querySelector('[data-method]').value='DELETE'; return confirm('Supprimer cette option ?')"
                                    title="Supprimer l'option">
                                @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#b91c1c'])
                            </button>
                        </form>
                    @endforeach
                @empty
                    <p class="cell-empty">Aucune option pour ce niveau.</p>
                @endforelse

                <div class="add-foot">
                    <span class="add-label">Ajouter une option</span>
                    <form method="post" action="{{ route('referentiel.niveaux.options.store', $niveau) }}" class="add-form">
                        @csrf
                        <input type="text" name="titre" required class="opt-titre" placeholder="Frais de dossier, assurance…">
                        <select name="id_objet_paiement" required class="opt-objet">
                            @foreach ($objetsPaiement as $objet)
                                <option value="{{ $objet->id_objet_paiement }}">{{ $objet->objet_paiement }}</option>
                            @endforeach
                        </select>
                        <span class="opt-amount">
                            <input type="number" step="0.01" name="montant" required placeholder="0">
                            <span class="opt-euro">DH</span>
                        </span>
                        <input type="number" name="annee" value="{{ $anneeCourante }}" required class="year-input" aria-label="Année">
                        <input type="hidden" name="ordre" value="{{ $options->count() }}">
                        <button type="submit" class="btn">
                            @include('partials.icon', ['n' => 'plus', 's' => 14, 'w' => 2.4])Ajouter
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    {{-- ---------- Colonne latérale ---------- --}}
    <div class="nv-col">
        <section class="panel panel-pad">
            <div class="field-label">Pondération du bulletin</div>
            <div class="weight-total">
                <span class="weight-value">{{ $nombre($somme) }}</span>
                <span class="weight-hint">points de coefficient</span>
            </div>

            @if ($somme > 0)
                <div class="stack-bar">
                    @foreach ($matieres as $index => $matiere)
                        <span style="flex:{{ (float) $matiere->pivot->coefficient / $somme * 100 }} 0 0;background:{{ $couleurs[$index % count($couleurs)] }}"></span>
                    @endforeach
                </div>
                <div class="legend">
                    @foreach ($matieres as $index => $matiere)
                        <div class="legend-row">
                            <span class="legend-dot" style="background:{{ $couleurs[$index % count($couleurs)] }}"></span>
                            <span class="legend-name">{{ $matiere->nom_cours }}</span>
                            <span class="legend-part">{{ round((float) $matiere->pivot->coefficient / $somme * 100) }} %</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="hint-line" style="margin-top:12px">Ajoutez des matières pour pondérer la moyenne générale.</p>
            @endif
        </section>

        <section class="panel panel-pad">
            <div class="field-label" style="margin-bottom:10px">Ce niveau</div>
            <div class="sit">
                @isset($elevesCount)
                    <div class="sit-row"><span>Élèves inscrits</span><span class="sit-value">{{ $elevesCount }}</span></div>
                @endisset
                @isset($classesCount)
                    <div class="sit-row"><span>Classes rattachées</span><span class="sit-value">{{ $classesCount }}</span></div>
                @endisset
                <div class="sit-row"><span>Matières</span><span class="sit-value">{{ $matieres->count() }}</span></div>
                <div class="sit-row"><span>Options facturables</span><span class="sit-value">{{ $options->count() }}</span></div>
            </div>
        </section>

        <section class="panel panel-pad">
            <h2 class="side-title">Actions</h2>
            <div class="side-actions">
                <a href="{{ route('eleves.index', ['niveau' => $niveau->id_niveau]) }}" class="side-action">
                    @include('partials.icon', ['n' => 'users', 's' => 15])Voir les élèves du niveau
                </a>
                @if (Route::has('referentiel.ref.index'))
                    <a href="{{ route('referentiel.ref.index') }}" class="side-action">
                        @include('partials.icon', ['n' => 'clock', 's' => 15])Référentiel des heures
                    </a>
                @endif

                <form method="post" action="{{ route('referentiel.niveaux.destroy', $niveau) }}"
                      onsubmit="return confirm('Supprimer ce niveau ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="side-action is-danger">
                        @include('partials.icon', ['n' => 'trash', 's' => 15, 'c' => '#b91c1c'])Supprimer le niveau
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>

<style>
    /* Niveau : styles spécifiques (le reste vient de layouts/app.blade.php) */
    .nv-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px 20px 0; margin-bottom: 14px; }
    .nv-row { display: flex; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
    .nv-code {
        padding: 0 14px; height: 58px; border-radius: 14px; flex-shrink: 0;
        background: var(--brand-light); color: var(--brand-deep);
        font-size: 17px; font-weight: 700; letter-spacing: -.01em;
        display: flex; align-items: center; font-variant-numeric: tabular-nums;
    }
    .nv-main { min-width: 0; flex: 1 1 280px; }
    .nv-title { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
    .nv-title h1 { margin: 0; font-size: 22px; letter-spacing: -.025em; }
    .nv-sub { margin: 7px 0 0; font-size: 13.5px; color: #585e72; }
    .nv-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto; }

    .pill { display: inline-flex; align-items: center; padding: 4px 11px; border-radius: 999px; font-size: 12px; font-weight: 700; white-space: nowrap; font-variant-numeric: tabular-nums; }
    .pill-brand { background: var(--brand-light); color: var(--brand-deep); }
    .pill-slate { background: #eef1f6; color: #475569; font-size: 11.5px; padding: 3px 9px; }
    .pill-teal { background: #e7f6f2; color: #0f766e; }

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

    .nv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 14px; align-items: start; }
    .nv-col { display: flex; flex-direction: column; gap: 14px; min-width: 0; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-pad { padding: 18px; }
    .panel-title { margin: 0 0 15px; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 14px 16px 12px; border-bottom: 1px solid var(--border-soft); }
    .panel-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head .panel-sub { margin: 4px 0 0; font-size: 12.5px; color: var(--muted); max-width: 62ch; text-wrap: pretty; }
    .panel-head .pill { margin-left: auto; }

    /* Identité */
    .nv-form { max-width: none; }
    .stack { display: block; margin: 0 0 14px; }
    .stack:last-child { margin-bottom: 0; }
    .stack > span:first-child { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
    .stack input, .stack select { width: 100%; max-width: none; }
    .stack input { background: #fafbfd; }
    .stack select { cursor: pointer; }
    .stack input.is-code { font-weight: 600; font-variant-numeric: tabular-nums; }
    .id-pair { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 14px; }
    .id-pair .stack { margin: 0; }
    .id-pair .stack.is-wide { grid-column: span 2; }
    .divider { margin-top: 16px; padding-top: 15px; border-top: 1px solid var(--border-soft); }
    .hint-line { margin: 8px 0 0; font-size: 12px; color: var(--muted); text-wrap: pretty; }

    /* Matières */
    .mat-row {
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap; max-width: none; margin: 0;
        padding: 12px 16px; border-bottom: 1px solid #f6f7fa;
    }
    .mat-ordre {
        width: 26px; height: 26px; border-radius: 7px; flex-shrink: 0; background: #f5f6fa;
        color: var(--muted); font-size: 11px; font-weight: 700; font-variant-numeric: tabular-nums;
        display: flex; align-items: center; justify-content: center;
    }
    .mat-text { min-width: 0; flex: 1 1 200px; }
    .mat-nom { display: block; font-size: 13.5px; font-weight: 600; letter-spacing: -.005em; }
    .mat-poids { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
    .mat-coef { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
    .mat-coef input[data-coef], .coef-input {
        width: 64px; max-width: none; text-align: center; font-variant-numeric: tabular-nums;
        border-radius: 9px; padding: 9px 8px; font-size: 13.5px; font-weight: 700; background: #fff;
    }
    .step, .save, .remove {
        border-radius: 8px; padding: 0; flex-shrink: 0; background: #fff; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); transition: background .14s ease;
    }
    .step { width: 28px; height: 28px; }
    .save { width: 30px; height: 30px; }
    .step:hover, .save:hover { background: #f4f5fa; }
    .remove { width: 30px; height: 30px; border-color: #fecaca; }
    .remove:hover { background: var(--danger-bg); }
    .mat-bar { flex: 1 1 100%; height: 4px; border-radius: 999px; background: #f0f1f6; overflow: hidden; }
    .mat-bar span { display: block; height: 100%; border-radius: 999px; transition: width .25s cubic-bezier(.22,1,.36,1); }

    /* Options */
    .year-head { display: flex; align-items: center; gap: 9px; padding: 9px 16px; background: #fafbfd; border-bottom: 1px solid var(--border-soft); }
    .year-label { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .year-total { margin-left: auto; font-size: 12px; font-weight: 600; color: #585e72; font-variant-numeric: tabular-nums; }
    .opt-row {
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap; max-width: none; margin: 0;
        padding: 12px 16px; border-bottom: 1px solid #f6f7fa;
    }
    .opt-titre {
        flex: 1 1 200px; min-width: 0; max-width: none; border-radius: 9px; padding: 9px 11px;
        font-size: 13px; font-weight: 600; background: #fafbfd;
    }
    .opt-objet { flex: 1 1 170px; min-width: 150px; max-width: none; border-radius: 9px; padding: 9px 11px; font-size: 13px; color: #585e72; cursor: pointer; }
    .opt-amount { position: relative; flex-shrink: 0; }
    .opt-amount input {
        width: 106px; max-width: none; text-align: right; font-variant-numeric: tabular-nums;
        border-radius: 9px; padding: 9px 26px 9px 11px; font-size: 13px; font-weight: 600; background: #fafbfd;
    }
    .opt-euro { position: absolute; right: 11px; top: 9px; font-size: 12.5px; color: var(--faint); pointer-events: none; }
    .year-input { width: 82px; max-width: none; border-radius: 9px; padding: 9px 11px; font-size: 13px; font-variant-numeric: tabular-nums; background: #fafbfd; flex-shrink: 0; }

    .cell-empty { margin: 0; padding: 30px 18px; text-align: center; font-size: 13px; color: var(--muted); text-wrap: pretty; }
    .add-foot { padding: 14px 16px; background: #fbfbff; border-top: 1px solid var(--border-soft); }
    .add-label { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 7px; }
    .add-form { display: flex; gap: 9px; align-items: flex-end; flex-wrap: wrap; max-width: none; margin: 0; }
    .add-form .btn { white-space: nowrap; }
    .add-empty { margin: 0; font-size: 13px; color: var(--muted); }
    .mini { display: block; margin: 0; min-width: 0; }
    .mini > span { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .mini select, .mini input { width: 100%; max-width: none; border-radius: 9px; padding: 9px 11px; font-size: 13px; }

    /* Colonne latérale */
    .field-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .weight-total { display: flex; align-items: baseline; gap: 8px; margin-top: 7px; }
    .weight-value { font-size: 28px; font-weight: 700; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
    .weight-hint { font-size: 13px; color: var(--muted); }
    .stack-bar { display: flex; height: 7px; border-radius: 999px; overflow: hidden; margin-top: 13px; gap: 1px; background: #f0f1f6; }
    .stack-bar span { display: block; height: 100%; }
    .legend { display: flex; flex-direction: column; gap: 7px; margin-top: 12px; }
    .legend-row { display: flex; align-items: center; gap: 9px; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .legend-name { font-size: 12.5px; color: #585e72; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .legend-part { margin-left: auto; font-size: 12.5px; font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }

    .sit { display: flex; flex-direction: column; gap: 8px; }
    .sit-row { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #585e72; }
    .sit-value { margin-left: auto; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink); }

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
    (function () {
        // Onglets
        var tabs = Array.prototype.slice.call(document.querySelectorAll('.tab[data-tab]'));
        var panels = Array.prototype.slice.call(document.querySelectorAll('[data-panel]'));
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.toggle('is-active', t === tab); });
                panels.forEach(function (p) { p.hidden = p.dataset.panel !== tab.dataset.tab; });
            });
        });

        // Pas ±0,5 sur les coefficients
        document.querySelectorAll('.mat-row').forEach(function (row) {
            var champ = row.querySelector('[data-coef]');
            row.querySelectorAll('[data-step]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var valeur = parseFloat(champ.value) || 0;
                    valeur = Math.min(20, Math.max(0.5, valeur + parseFloat(btn.dataset.step)));
                    champ.value = valeur;
                });
            });
        });

        // Aide contextuelle du niveau suivant
        var suivant = document.querySelector('[data-suivant]');
        var aide = document.querySelector('[data-suivant-aide]');
        function syncAide() {
            if (!suivant || !aide) return;
            aide.textContent = suivant.value
                ? 'Les élèves réinscrits de ce niveau seront proposés dans le niveau suivant.'
                : 'Sans niveau suivant, la réinscription devra être affectée manuellement.';
        }
        if (suivant) suivant.addEventListener('change', syncAide);
        syncAide();
    })();
</script>
@endsection
