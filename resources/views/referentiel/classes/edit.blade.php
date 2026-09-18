@extends('layouts.app')

@section('title', 'Modifier classe')

@section('content')
@php
    $eleves = $classe->eleves()->with('contact')->get()
        ->sortBy(fn ($e) => mb_strtolower(($e->contact?->nom ?? '').' '.($e->contact?->prenom ?? '')))
        ->values();
    $effectif = $eleves->count();
    $inscrits = $eleves->where('profil', 'eleve')->count();
    $candidats = $eleves->where('profil', 'candidat')->count();
    $couleur = $classe->couleur ?: '#cccccc';
    $capacite = isset($capacite) ? (int) $capacite : null;
    $taux = $capacite > 0 ? min(100, (int) round($effectif / $capacite * 100)) : null;
    $profils = [
        'eleve' => ['Élève', 'teal'],
        'candidat' => ['Candidat', 'amber'],
        'reinscrit' => ['Réinscrit', 'brand'],
        'alumni' => ['Alumni', 'slate'],
        'abandon' => ['Abandon', 'red'],
    ];
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <a href="{{ route('referentiel.classes.index') }}">Classes</a>
    <span class="sep">/</span>
    <span class="current">{{ $classe->classe }}</span>
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
<div class="cl-card">
    <div class="cl-row">
        <span class="cl-code" style="--cl:{{ $couleur }}">
            {{ $classe->code_classe ?: mb_strtoupper(mb_substr($classe->classe, 0, 4)) }}
        </span>

        <div class="cl-main">
            <div class="cl-title">
                <h1>{{ $classe->classe }}</h1>
                <span class="pill pill-slate">Classe #{{ $classe->id_classe }}</span>
                @if ($effectif === 0)
                    <span class="pill pill-amber">Aucun élève</span>
                @endif
            </div>
            <p class="cl-sub">
                {{ $classe->niveau?->nom_niveau ?? 'Niveau non renseigné' }}
                ·
                {{ $classe->etablissement?->nom_etablissement ?? 'Établissement non renseigné' }}
            </p>
        </div>

        <div class="cl-actions">
            <a href="{{ route('referentiel.classes.index') }}" class="btn btn-ghost">
                @include('partials.icon', ['n' => 'arrow-left', 's' => 15, 'w' => 2])Toutes les classes
            </a>
            <button type="submit" form="classe-form" class="btn">
                @include('partials.icon', ['n' => 'check-simple', 's' => 15, 'w' => 2])Enregistrer
            </button>
        </div>
    </div>

    <div class="tabs-bar" role="tablist">
        <button type="button" class="tab is-active" data-tab="identite" role="tab">
            Identité<span class="tab-badge">4</span>
        </button>
        <button type="button" class="tab" data-tab="eleves" role="tab">
            Élèves<span class="tab-badge">{{ $effectif }}</span>
        </button>
    </div>
</div>

<div class="cl-grid">
    <div class="cl-col">

        {{-- ---------- Identité ---------- --}}
        <div data-panel="identite">
            <section class="panel panel-pad">
                <h2 class="panel-title">Identité de la classe</h2>

                <form method="post" action="{{ route('referentiel.classes.update', $classe) }}" id="classe-form" class="cl-form">
                    @csrf
                    @method('PUT')

                    <div class="id-pair">
                        <label class="stack">
                            <span>Code</span>
                            <input type="text" name="code_classe" class="is-code"
                                   value="{{ old('code_classe', $classe->code_classe) }}"
                                   placeholder="B3-MKT-A">
                        </label>
                        <label class="stack is-wide">
                            <span>Nom de la classe</span>
                            <input type="text" name="classe" required
                                   value="{{ old('classe', $classe->classe) }}">
                        </label>
                    </div>

                    <div class="id-pair">
                        <label class="stack">
                            <span>Niveau</span>
                            <select name="id_niveau" required>
                                @foreach ($niveaux as $niveau)
                                    <option value="{{ $niveau->id_niveau }}" @selected(old('id_niveau', $classe->id_niveau) == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="stack">
                            <span>Établissement</span>
                            <select name="id_etablissement" required>
                                @foreach ($etablissements as $etablissement)
                                    <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $classe->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="divider">
                        <span class="field-label">Couleur du planning</span>
                        <div class="color-line">
                            <span class="color-preview" data-preview style="background:{{ old('couleur', $couleur) }}"></span>
                            <input type="color" name="couleur" value="{{ old('couleur', $couleur) }}" data-color aria-label="Couleur de la classe">
                            <div class="swatches">
                                @foreach (['#4f46e5', '#0f766e', '#b45309', '#7c3aed', '#be123c', '#475569'] as $choix)
                                    <button type="button" class="swatch" data-swatch="{{ $choix }}"
                                            style="background:{{ $choix }}" aria-label="Choisir {{ $choix }}"></button>
                                @endforeach
                            </div>
                        </div>
                        <p class="hint-line">Utilisée pour repérer la classe dans les plannings et les panneaux d'affichage.</p>
                    </div>
                </form>
            </section>
        </div>

        {{-- ---------- Élèves ---------- --}}
        <div data-panel="eleves" hidden>
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2>Élèves rattachés</h2>
                        <p class="panel-sub">Le rattachement se fait depuis la fiche de l'élève : il détermine son planning, son émargement et son bulletin.</p>
                    </div>
                    <span class="pill pill-brand">{{ $effectif }} {{ $effectif > 1 ? 'élèves' : 'élève' }}</span>
                </div>

                @if ($effectif > 0)
                    <div class="el-filter">
                        <input type="search" placeholder="Filtrer dans la liste…" data-filtre aria-label="Filtrer les élèves">
                    </div>
                @endif

                @forelse ($eleves as $eleve)
                    @php
                        $nom = trim(($eleve->contact?->nom ?? '').' '.($eleve->contact?->prenom ?? '')) ?: 'Élève #'.$eleve->id_eleve;
                        $initiales = mb_strtoupper(mb_substr($eleve->contact?->nom ?? 'É', 0, 1).mb_substr($eleve->contact?->prenom ?? '', 0, 1));
                        [$libelle, $ton] = $profils[$eleve->profil] ?? [ucfirst((string) $eleve->profil), 'slate'];
                    @endphp
                    <div class="el-row" data-nom="{{ mb_strtolower($nom.' '.($eleve->contact?->email ?? '')) }}">
                        <span class="el-avatar">{{ $initiales }}</span>
                        <span class="el-text">
                            <span class="el-nom">{{ $nom }}</span>
                            <span class="el-meta">{{ $eleve->contact?->email ?: '—' }}</span>
                        </span>
                        <span class="pill pill-{{ $ton }}">{{ $libelle }}</span>
                        @if (Route::has('eleves.show'))
                            <a href="{{ route('eleves.show', $eleve) }}" class="row-btn" title="Ouvrir la fiche">
                                @include('partials.icon', ['n' => 'eye', 's' => 15, 'c' => '#585e72'])
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="cell-empty">Aucun élève rattaché : la classe n'apparaîtra pas dans les plannings.</p>
                @endforelse

                @if ($effectif > 0)
                    <p class="cell-empty" data-vide hidden>Aucun élève ne correspond à ce filtre.</p>
                @endif

                @if (Route::has('eleves.index'))
                    <div class="add-foot">
                        <a href="{{ route('eleves.index', ['classe' => $classe->id_classe]) }}" class="btn btn-ghost">
                            @include('partials.icon', ['n' => 'users', 's' => 15])Gérer les rattachements
                        </a>
                    </div>
                @endif
            </section>
        </div>
    </div>

    {{-- ---------- Colonne latérale ---------- --}}
    <div class="cl-col">
        <section class="panel panel-pad">
            <div class="field-label">Effectif</div>
            <div class="weight-total">
                <span class="weight-value">{{ $effectif }}</span>
                <span class="weight-hint">
                    @if ($capacite)
                        élèves sur {{ $capacite }} places
                    @else
                        {{ $effectif > 1 ? 'élèves rattachés' : 'élève rattaché' }}
                    @endif
                </span>
            </div>

            @if ($taux !== null)
                <div class="gauge">
                    <span style="width:{{ $taux }}%;background:{{ $taux >= 100 ? 'var(--danger-dark)' : 'var(--brand)' }}"></span>
                </div>
                <p class="hint-line">
                    @if ($taux >= 100)
                        Effectif maximum atteint.
                    @else
                        {{ $capacite - $effectif }} {{ $capacite - $effectif > 1 ? 'places encore disponibles' : 'place encore disponible' }}.
                    @endif
                </p>
            @endif

            <div class="sit" style="margin-top:14px">
                <div class="sit-row"><span>Élèves</span><span class="sit-value">{{ $inscrits }}</span></div>
                <div class="sit-row"><span>Candidats</span><span class="sit-value">{{ $candidats }}</span></div>
            </div>
        </section>

        <section class="panel panel-pad">
            <div class="field-label" style="margin-bottom:10px">Cette classe</div>
            <div class="sit">
                <div class="sit-row"><span>Niveau</span><span class="sit-value">{{ $classe->niveau?->code_niveau ?? '—' }}</span></div>
                <div class="sit-row"><span>Établissement</span><span class="sit-value">{{ $classe->etablissement?->nom_etablissement ?? '—' }}</span></div>
                <div class="sit-row"><span>Couleur</span><span class="sit-value"><span class="dot" style="background:{{ $couleur }}"></span>{{ $couleur }}</span></div>
            </div>
        </section>

        <section class="panel panel-pad">
            <h2 class="side-title">Actions</h2>
            <div class="side-actions">
                @if (Route::has('eleves.index'))
                    <a href="{{ route('eleves.index', ['classe' => $classe->id_classe]) }}" class="side-action">
                        @include('partials.icon', ['n' => 'users', 's' => 15])Voir les élèves de la classe
                    </a>
                @endif
                @if ($classe->niveau && Route::has('referentiel.niveaux.edit'))
                    <a href="{{ route('referentiel.niveaux.edit', $classe->niveau) }}" class="side-action">
                        @include('partials.icon', ['n' => 'layers', 's' => 15])Modifier le niveau
                    </a>
                @endif
                @if (Route::has('referentiel.ref.index'))
                    <a href="{{ route('referentiel.ref.index', ['onglet' => 'classe']) }}" class="side-action">
                        @include('partials.icon', ['n' => 'clock', 's' => 15])Référentiel des heures
                    </a>
                @endif

                <form method="post" action="{{ route('referentiel.classes.destroy', $classe) }}"
                      onsubmit="return confirm('Supprimer cette classe ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="side-action is-danger">
                        @include('partials.icon', ['n' => 'trash', 's' => 15, 'c' => '#b91c1c'])Supprimer la classe
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>

<style>
    /* Classe : styles spécifiques (le reste vient de layouts/app.blade.php) */
    .cl-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px 20px 0; margin-bottom: 14px; }
    .cl-row { display: flex; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
    .cl-code {
        padding: 0 14px; height: 58px; min-width: 58px; border-radius: 14px; flex-shrink: 0;
        background: var(--brand-light); color: var(--brand-deep);
        border-left: 4px solid var(--cl, var(--brand));
        font-size: 16px; font-weight: 700; letter-spacing: -.01em;
        display: flex; align-items: center; justify-content: center; font-variant-numeric: tabular-nums;
    }
    .cl-main { min-width: 0; flex: 1 1 280px; }
    .cl-title { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
    .cl-title h1 { margin: 0; font-size: 22px; letter-spacing: -.025em; }
    .cl-sub { margin: 7px 0 0; font-size: 13.5px; color: #585e72; }
    .cl-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto; }
    .cl-actions .btn { white-space: nowrap; }

    .pill { display: inline-flex; align-items: center; padding: 4px 11px; border-radius: 999px; font-size: 12px; font-weight: 700; white-space: nowrap; font-variant-numeric: tabular-nums; }
    .pill-brand { background: var(--brand-light); color: var(--brand-deep); }
    .pill-slate { background: #eef1f6; color: #475569; font-size: 11.5px; padding: 3px 9px; }
    .pill-teal { background: #e7f6f2; color: #0f766e; }
    .pill-amber { background: #fff7ed; color: #b45309; }
    .pill-red { background: var(--danger-bg); color: var(--danger-dark); }

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

    .cl-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 14px; align-items: start; }
    .cl-col { display: flex; flex-direction: column; gap: 14px; min-width: 0; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-pad { padding: 18px; }
    .panel-title { margin: 0 0 15px; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 14px 16px 12px; border-bottom: 1px solid var(--border-soft); }
    .panel-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head .panel-sub { margin: 4px 0 0; font-size: 12.5px; color: var(--muted); max-width: 62ch; text-wrap: pretty; }
    .panel-head .pill { margin-left: auto; }

    /* Identité */
    .cl-form { max-width: none; }
    .stack { display: block; margin: 0; }
    .stack > span:first-child { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
    .stack input, .stack select { width: 100%; max-width: none; }
    .stack input { background: #fafbfd; }
    .stack select { cursor: pointer; }
    .stack input.is-code { font-weight: 600; font-variant-numeric: tabular-nums; }
    .id-pair { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 14px; }
    .id-pair .stack.is-wide { grid-column: span 2; }
    .divider { margin-top: 2px; padding-top: 15px; border-top: 1px solid var(--border-soft); }
    .hint-line { margin: 9px 0 0; font-size: 12px; color: var(--muted); text-wrap: pretty; }

    .color-line { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 8px; }
    .color-preview { width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--border); flex-shrink: 0; }
    .color-line input[type="color"] {
        width: 52px; height: 34px; max-width: none; padding: 3px; border-radius: 9px;
        border: 1px solid var(--border); background: #fff; cursor: pointer; flex-shrink: 0;
    }
    .swatches { display: flex; gap: 6px; flex-wrap: wrap; }
    .swatch {
        width: 26px; height: 26px; border-radius: 8px; padding: 0; cursor: pointer;
        border: 1px solid rgba(0, 0, 0, .08); transition: transform .14s ease;
    }
    .swatch:hover { transform: translateY(-2px); }

    /* Élèves */
    .el-filter { padding: 12px 16px; border-bottom: 1px solid var(--border-soft); background: #fbfbff; }
    .el-filter input { width: 100%; max-width: none; border-radius: 999px; padding: 9px 14px; font-size: 13px; background: #fff; }
    .el-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 11px 16px; border-bottom: 1px solid #f6f7fa; }
    .el-avatar {
        width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
        background: var(--brand-light); color: var(--brand-deep);
        font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center;
    }
    .el-text { min-width: 0; flex: 1 1 180px; }
    .el-nom { display: block; font-size: 13.5px; font-weight: 600; letter-spacing: -.005em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .el-meta { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
    .row-btn {
        width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
        border: 1px solid var(--border); background: #fff;
        display: inline-flex; align-items: center; justify-content: center; transition: background .14s ease;
    }
    .row-btn:hover { background: #f4f5fa; }

    .cell-empty { margin: 0; padding: 30px 18px; text-align: center; font-size: 13px; color: var(--muted); text-wrap: pretty; }
    .add-foot { padding: 14px 16px; background: #fbfbff; border-top: 1px solid var(--border-soft); }
    .add-foot .btn { white-space: nowrap; }

    /* Colonne latérale */
    .field-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .weight-total { display: flex; align-items: baseline; gap: 8px; margin-top: 7px; }
    .weight-value { font-size: 28px; font-weight: 700; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
    .weight-hint { font-size: 13px; color: var(--muted); }
    .gauge { height: 7px; border-radius: 999px; background: #f0f1f6; overflow: hidden; margin-top: 13px; }
    .gauge span { display: block; height: 100%; border-radius: 999px; transition: width .25s cubic-bezier(.22,1,.36,1); }

    .sit { display: flex; flex-direction: column; gap: 8px; }
    .sit-row { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #585e72; }
    .sit-value { margin-left: auto; display: inline-flex; align-items: center; gap: 7px; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink); }
    .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; border: 1px solid rgba(0, 0, 0, .08); }

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

        // Couleur : aperçu + pastilles
        var couleur = document.querySelector('[data-color]');
        var apercu = document.querySelector('[data-preview]');
        var code = document.querySelector('.cl-code');
        function syncCouleur() {
            if (!couleur) return;
            if (apercu) apercu.style.background = couleur.value;
            if (code) code.style.setProperty('--cl', couleur.value);
        }
        if (couleur) couleur.addEventListener('input', syncCouleur);
        document.querySelectorAll('[data-swatch]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!couleur) return;
                couleur.value = btn.dataset.swatch;
                syncCouleur();
            });
        });

        // Filtre de la liste d'élèves
        var filtre = document.querySelector('[data-filtre]');
        var vide = document.querySelector('[data-vide]');
        if (filtre) {
            filtre.addEventListener('input', function () {
                var q = filtre.value.trim().toLowerCase();
                var visibles = 0;
                document.querySelectorAll('.el-row').forEach(function (row) {
                    var ok = !q || (row.dataset.nom || '').indexOf(q) !== -1;
                    row.hidden = !ok;
                    if (ok) visibles++;
                });
                if (vide) vide.hidden = visibles !== 0;
            });
        }
    })();
</script>
@endsection
