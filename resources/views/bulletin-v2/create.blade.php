@extends('layouts.app')

@section('title', 'Générer un bulletin')

@section('content')
@php
    // Valeurs par défaut : une resoumission après erreur (`old`) l'emporte, puis
    // les paramètres passés par le raccourci « Générer » de la liste des bulletins.
    $anneeDefaut = (int) old('annee', request('annee', date('Y')));
    $semestreDefaut = (string) old('semestre', request('semestre', ''));
    // Un semestre à 0 et un semestre vide désignent la même chose (année complète).
    $semestreDefaut = $semestreDefaut === '0' ? '' : $semestreDefaut;
    $sessionDefaut = (int) old('session', request('session', 0));
    $etablissementDefaut = old('id_etablissement', request('id_etablissement'));
    $niveauDefaut = old('id_niveau', request('id_niveau'));
    $classeDefaut = old('id_classe', request('id_classe'));
    $eleveDefaut = old('id_eleve', request('id_eleve'));
    $classesJson = $classes->map(fn ($c) => [
        'id_classe' => $c->id_classe,
        'classe' => $c->classe,
        'id_niveau' => $c->id_niveau,
    ])->values();
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <a href="{{ route('bulletin-v2.index') }}">Bulletins</a>
    <span class="sep">/</span>
    <span class="current">Générer</span>
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

<div class="bl-head">
    <div class="bl-head-main">
        <h1>Générer un bulletin</h1>
        <p class="page-sub">Sélectionnez l'élève par son établissement, son niveau puis sa classe, précisez la période, et le PDF est produit à partir des notes saisies.</p>
    </div>
    <a href="{{ route('bulletin-v2.index') }}" class="btn btn-ghost">
        @include('partials.icon', ['n' => 'arrow-left', 's' => 15, 'w' => 2])Bulletins générés
    </a>
</div>

<form method="post" action="{{ route('bulletin-v2.generate') }}" id="bulletin-form" class="bl-grid">
    @csrf

    <div class="bl-col">

        {{-- ---------- Étape 1 : élève ---------- --}}
        <section class="panel panel-pad">
            <div class="step-head">
                <span class="step-num">1</span>
                <h2>Élève concerné</h2>
            </div>

            <div class="id-grid">
                <label class="stack">
                    <span>Établissement</span>
                    <select name="id_etablissement" required data-etablissement>
                        @foreach ($etablissements as $etablissement)
                            <option value="{{ $etablissement->id_etablissement }}" @selected($etablissementDefaut == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="stack">
                    <span>Niveau</span>
                    <select name="id_niveau" required data-niveau>
                        <option value="">— Choisir —</option>
                        @foreach ($niveaux as $niveau)
                            <option value="{{ $niveau->id_niveau }}" @selected($niveauDefaut == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="stack">
                    <span>Classe</span>
                    <select data-classe data-preselect="{{ $classeDefaut }}" disabled>
                        <option value="">Choisir un niveau d'abord</option>
                    </select>
                </label>

                <label class="stack">
                    <span>Élève</span>
                    <select name="id_eleve" required data-eleve data-preselect="{{ $eleveDefaut }}" disabled>
                        <option value="">Choisir une classe d'abord</option>
                    </select>
                </label>
            </div>

            <p class="hint-line" data-cascade-aide>Choisissez un niveau pour charger ses classes.</p>
        </section>

        {{-- ---------- Étape 2 : période ---------- --}}
        <section class="panel panel-pad">
            <div class="step-head">
                <span class="step-num">2</span>
                <h2>Période du bulletin</h2>
            </div>

            <div class="per-grid">
                <label class="stack">
                    <span>Année</span>
                    <input type="number" name="annee" min="2000" max="2100" required
                           value="{{ $anneeDefaut }}" class="is-num" data-annee>
                </label>
                <label class="stack">
                    <span>Session</span>
                    <input type="number" name="session" min="0" max="9"
                           value="{{ $sessionDefaut }}" class="is-num" data-session>
                </label>
            </div>

            <div class="sem-block">
                <span class="field-inline">Semestre</span>
                <div class="chips" role="group" aria-label="Semestre">
                    @foreach (['1' => 'Semestre 1', '2' => 'Semestre 2', '' => 'Les deux'] as $valeur => $libelle)
                        <button type="button" class="chip {{ (string) $semestreDefaut === (string) $valeur ? 'is-active' : '' }}"
                                data-semestre="{{ $valeur }}">{{ $libelle }}</button>
                    @endforeach
                </div>
                <input type="hidden" name="semestre" value="{{ $semestreDefaut }}" data-semestre-input>
                <p class="hint-line" data-semestre-aide></p>
            </div>

            <div class="divider">
                <label class="check">
                    <input type="checkbox" data-rattrapage @checked($sessionDefaut > 0)>
                    <span>
                        <span class="check-title">Session de rattrapage</span>
                        <span class="check-sub" data-rattrapage-sub>N'imprime que les notes de la session choisie</span>
                    </span>
                </label>
            </div>
        </section>
    </div>

    {{-- ---------- Récapitulatif ---------- --}}
    <div class="bl-col">
        <section class="panel panel-pad bl-recap">
            <div class="field-label">Récapitulatif</div>

            <div class="recap-eleve">
                <span class="recap-avatar" data-recap-initiales>—</span>
                <span class="recap-text">
                    <span class="recap-nom" data-recap-nom>Aucun élève sélectionné</span>
                    <span class="recap-meta" data-recap-contexte>Complétez la sélection</span>
                </span>
            </div>

            <div class="sit">
                <div class="sit-row"><span>Niveau</span><span class="sit-value" data-recap-niveau>—</span></div>
                <div class="sit-row"><span>Classe</span><span class="sit-value" data-recap-classe>—</span></div>
                <div class="sit-row"><span>Année</span><span class="sit-value" data-recap-annee>{{ $anneeDefaut }}</span></div>
                <div class="sit-row"><span>Semestre</span><span class="sit-value" data-recap-semestre>—</span></div>
                <div class="sit-row"><span>Session</span><span class="sit-value" data-recap-session>—</span></div>
            </div>

            <button type="submit" class="btn btn-block" data-submit disabled>
                @include('partials.icon', ['n' => 'download', 's' => 15, 'w' => 2])Générer le PDF
            </button>
            <p class="hint-line is-center" data-submit-aide>Sélectionnez une classe et un élève pour activer la génération.</p>
        </section>

        @isset($bulletins)
            @if ($bulletins->isNotEmpty())
                <section class="panel panel-pad">
                    <div class="field-label" style="margin-bottom:10px">Derniers bulletins</div>
                    <div class="last-list">
                        @foreach ($bulletins->take(4) as $bulletin)
                            <a href="{{ route('bulletin-v2.show', $bulletin) }}" target="_blank" class="last-row">
                                <span class="last-text">
                                    <span class="last-nom">{{ $bulletin->eleve?->contact?->nom_complet ?? 'Élève' }}</span>
                                    <span class="last-meta">
                                        {{ $bulletin->annee }} ·
                                        {{ $bulletin->semestre ? 'S'.$bulletin->semestre : 'S1 + S2' }}
                                        @if ($bulletin->date_insert)
                                            · généré le {{ $bulletin->date_insert->format('d/m') }}
                                        @endif
                                    </span>
                                </span>
                                @include('partials.icon', ['n' => 'arrow-right', 's' => 15, 'c' => '#9aa0b0', 'w' => 2])
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        @endisset
    </div>
</form>

<style>
    /* Générer un bulletin : styles spécifiques (le reste vient de layouts/app.blade.php) */
    .bl-head { display: flex; align-items: flex-start; gap: 16px; flex-wrap: wrap; margin-bottom: 16px; }
    .bl-head-main { min-width: 0; flex: 1 1 320px; }
    .bl-head h1 { margin: 0; font-size: 22px; letter-spacing: -.025em; }
    .bl-head .page-sub { margin: 7px 0 0; font-size: 13.5px; color: #585e72; max-width: 70ch; text-wrap: pretty; }
    .bl-head .btn { white-space: nowrap; }

    .bl-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(330px, 1fr)); gap: 14px; align-items: start; max-width: none; margin: 0; }
    .bl-col { display: flex; flex-direction: column; gap: 14px; min-width: 0; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-pad { padding: 18px; }

    .step-head { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
    .step-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .step-num {
        width: 24px; height: 24px; border-radius: 7px; flex-shrink: 0;
        background: var(--brand-light); color: var(--brand-deep);
        font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center;
    }

    .stack { display: block; margin: 0; min-width: 0; }
    .stack > span:first-child { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
    .stack input, .stack select { width: 100%; max-width: none; }
    .stack select { cursor: pointer; }
    .stack select:disabled { background: #f7f8fb; color: var(--faint); cursor: not-allowed; }
    .stack input.is-num { background: #fafbfd; font-weight: 600; font-variant-numeric: tabular-nums; }
    .id-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; }
    .per-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }

    .hint-line { margin: 12px 0 0; font-size: 12px; color: var(--muted); text-wrap: pretty; }
    .hint-line.is-center { text-align: center; }
    .field-inline { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 8px; }
    .sem-block { margin-top: 16px; }
    .chips { display: flex; gap: 8px; flex-wrap: wrap; }
    .chip {
        padding: 9px 16px; border-radius: 999px; border: 1px solid var(--border); background: #fff;
        font: inherit; font-size: 13px; font-weight: 600; color: #585e72; cursor: pointer; white-space: nowrap;
        transition: background .14s ease, color .14s ease, border-color .14s ease;
    }
    .chip:hover { border-color: #c3c6f5; color: var(--brand-deep); }
    .chip.is-active { background: var(--brand); border-color: var(--brand); color: #fff; box-shadow: 0 2px 8px rgba(79, 70, 229, .26); }

    .divider { margin-top: 16px; padding-top: 15px; border-top: 1px solid var(--border-soft); }
    .check {
        display: flex; align-items: center; gap: 10px; margin: 0; padding: 10px 12px;
        border: 1px solid var(--border); border-radius: 10px; background: #fafbfd; cursor: pointer;
    }
    .check input { width: 16px; height: 16px; max-width: none; margin: 0; accent-color: var(--brand); flex-shrink: 0; cursor: pointer; }
    .check-title { display: block; font-size: 13px; font-weight: 600; }
    .check-sub { display: block; font-size: 11.5px; color: var(--muted); }

    .bl-recap { position: sticky; top: 74px; }
    .field-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .recap-eleve { display: flex; align-items: center; gap: 11px; margin-top: 12px; padding-bottom: 14px; border-bottom: 1px solid var(--border-soft); }
    .recap-avatar {
        width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
        background: var(--brand-light); color: var(--brand-deep);
        font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center;
    }
    .recap-text { min-width: 0; }
    .recap-nom { display: block; font-size: 14px; font-weight: 700; letter-spacing: -.015em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .recap-meta { display: block; font-size: 12px; color: var(--muted); margin-top: 1px; }

    .sit { display: flex; flex-direction: column; gap: 8px; margin-top: 14px; }
    .sit-row { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #585e72; }
    .sit-value { margin-left: auto; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink); text-align: right; }

    .btn-block { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; margin-top: 16px; }
    .btn-block:disabled { background: #eceef4; color: var(--faint); box-shadow: none; cursor: not-allowed; }
    .btn-block:disabled svg { stroke: var(--faint); }

    .last-list { display: flex; flex-direction: column; gap: 9px; }
    .last-row {
        display: flex; align-items: center; gap: 10px; padding: 9px 11px;
        border: 1px solid var(--border); border-radius: 9px; background: #fff;
        transition: border-color .14s ease, background .14s ease;
    }
    .last-row:hover { border-color: #c3c6f5; background: #fafbff; }
    .last-text { min-width: 0; }
    .last-nom { display: block; font-size: 13px; font-weight: 600; color: var(--ink); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .last-meta { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
    .last-row svg { margin-left: auto; flex-shrink: 0; }
</style>

<script>
    (function () {
        var classes = @json($classesJson);

        var niveau = document.querySelector('[data-niveau]');
        var classe = document.querySelector('[data-classe]');
        var eleve = document.querySelector('[data-eleve]');
        var etablissement = document.querySelector('[data-etablissement]');
        var annee = document.querySelector('[data-annee]');
        var session = document.querySelector('[data-session]');
        var rattrapage = document.querySelector('[data-rattrapage]');
        var semestreInput = document.querySelector('[data-semestre-input]');
        var submit = document.querySelector('[data-submit]');

        var aide = document.querySelector('[data-cascade-aide]');
        var aideSem = document.querySelector('[data-semestre-aide]');
        var aideSubmit = document.querySelector('[data-submit-aide]');
        var subRattrapage = document.querySelector('[data-rattrapage-sub]');

        var rNom = document.querySelector('[data-recap-nom]');
        var rIni = document.querySelector('[data-recap-initiales]');
        var rCtx = document.querySelector('[data-recap-contexte]');
        var rNiveau = document.querySelector('[data-recap-niveau]');
        var rClasse = document.querySelector('[data-recap-classe]');
        var rAnnee = document.querySelector('[data-recap-annee]');
        var rSem = document.querySelector('[data-recap-semestre]');
        var rSession = document.querySelector('[data-recap-session]');

        function texte(select) {
            if (!select || !select.value) return '';
            var opt = select.options[select.selectedIndex];
            return opt ? opt.textContent.trim() : '';
        }

        function couper(v) { return v.length > 30 ? v.slice(0, 29) + '…' : v; }

        function initiales(nom) {
            var parts = nom.split(/\s+/).filter(Boolean);
            if (!parts.length) return '—';
            return (parts[0][0] + (parts[1] ? parts[1][0] : '')).toUpperCase();
        }

        function syncRecap() {
            var nomEleve = texte(eleve);
            var nomClasse = texte(classe);
            var pret = Boolean(classe.value && eleve.value);

            rNom.textContent = nomEleve || 'Aucun élève sélectionné';
            rIni.textContent = nomEleve ? initiales(nomEleve) : '—';
            rCtx.textContent = nomEleve
                ? [texte(etablissement), nomClasse].filter(Boolean).join(' · ')
                : 'Complétez la sélection';
            rNiveau.textContent = couper(texte(niveau)) || '—';
            rClasse.textContent = couper(nomClasse) || '—';
            rAnnee.textContent = annee.value || '—';
            rSem.textContent = semestreInput.value ? 'S' + semestreInput.value : 'S1 + S2';

            var s = parseInt(session.value, 10) || 0;
            rSession.textContent = s === 0 ? 'Principale' : 'Rattrapage ' + s;

            submit.disabled = !pret;
            aideSubmit.textContent = pret
                ? 'Le PDF s\'ouvre dans un nouvel onglet et reste disponible dans la liste des bulletins.'
                : 'Sélectionnez une classe et un élève pour activer la génération.';

            if (!niveau.value) {
                aide.textContent = 'Choisissez un niveau pour charger ses classes.';
            } else if (!classe.value) {
                aide.textContent = 'Choisissez une classe pour charger ses élèves.';
            } else if (!eleve.value) {
                aide.textContent = 'Sélectionnez l\'élève dans la liste de la classe.';
            } else {
                aide.textContent = 'L\'élève est identifié : aucun identifiant à saisir à la main.';
            }

            if (subRattrapage) {
                subRattrapage.textContent = s === 0
                    ? 'N\'imprime que les notes de la session choisie'
                    : 'Session ' + s + ' : n\'imprime que les notes de cette session';
            }
        }

        // Semestre en pastilles
        document.querySelectorAll('[data-semestre]').forEach(function (chip) {
            chip.addEventListener('click', function () {
                document.querySelectorAll('[data-semestre]').forEach(function (c) {
                    c.classList.toggle('is-active', c === chip);
                });
                semestreInput.value = chip.dataset.semestre;
                aideSem.textContent = semestreInput.value
                    ? 'Seules les notes du semestre ' + semestreInput.value + ' seront imprimées.'
                    : 'Le bulletin couvrira l\'année complète, semestres 1 et 2.';
                syncRecap();
            });
        });

        // Cascade niveau -> classe
        niveau.addEventListener('change', function () {
            var id = parseInt(niveau.value, 10);
            var options = classes.filter(function (c) { return c.id_niveau === id; });

            eleve.innerHTML = '<option value="">Choisir une classe d\'abord</option>';
            eleve.disabled = true;

            if (!options.length) {
                classe.innerHTML = '<option value="">Aucune classe pour ce niveau</option>';
                classe.disabled = true;
                syncRecap();
                return;
            }

            classe.innerHTML = '<option value="">— Choisir —</option>' + options.map(function (c) {
                return '<option value="' + c.id_classe + '">' + c.classe + '</option>';
            }).join('');
            classe.disabled = false;

            if (classe.dataset.preselect) {
                classe.value = classe.dataset.preselect;
                classe.dataset.preselect = '';
                if (classe.value) classe.dispatchEvent(new Event('change'));
            }

            syncRecap();
        });

        // Cascade classe -> élève
        classe.addEventListener('change', function () {
            var id = classe.value;
            eleve.disabled = true;

            if (!id) {
                eleve.innerHTML = '<option value="">Choisir une classe d\'abord</option>';
                syncRecap();
                return;
            }

            eleve.innerHTML = '<option value="">Chargement…</option>';
            syncRecap();

            fetch('{{ url('bulletin-v2/classes') }}/' + id + '/eleves')
                .then(function (r) { return r.json(); })
                .then(function (eleves) {
                    if (!eleves.length) {
                        eleve.innerHTML = '<option value="">Aucun élève dans cette classe</option>';
                        syncRecap();
                        return;
                    }
                    eleve.innerHTML = '<option value="">— Choisir —</option>' + eleves.map(function (e) {
                        return '<option value="' + e.id_eleve + '">' + ((e.nom || '') + ' ' + (e.prenom || '')).trim() + '</option>';
                    }).join('');
                    eleve.disabled = false;

                    if (eleve.dataset.preselect) {
                        eleve.value = eleve.dataset.preselect;
                        eleve.dataset.preselect = '';
                    }

                    syncRecap();
                })
                .catch(function () {
                    eleve.innerHTML = '<option value="">Chargement impossible</option>';
                    syncRecap();
                });
        });

        // Rattrapage <-> session
        if (rattrapage) {
            rattrapage.addEventListener('change', function () {
                var s = parseInt(session.value, 10) || 0;
                session.value = rattrapage.checked ? Math.max(1, s) : 0;
                syncRecap();
            });
        }

        [eleve, etablissement, annee, session].forEach(function (el) {
            if (el) el.addEventListener('change', syncRecap);
        });

        // État initial (y compris après une erreur de validation)
        if (niveau.value) niveau.dispatchEvent(new Event('change'));
        aideSem.textContent = semestreInput.value
            ? 'Seules les notes du semestre ' + semestreInput.value + ' seront imprimées.'
            : 'Le bulletin couvrira l\'année complète, semestres 1 et 2.';
        syncRecap();
    })();
</script>
@endsection
