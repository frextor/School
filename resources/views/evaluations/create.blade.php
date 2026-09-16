@extends('layouts.app')

@section('title', 'Nouvelle évaluation')

@section('content')
@php
    $semestreActuel = (int) old('semestre', 1);
    $referentielActuel = old('referentiel', 'classe');
    $baremeActuel = old('type_notation', '20');
    $baremesConnus = ['20', '10', '5'];
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <a href="{{ route('evaluations.index') }}">Évaluations</a>
    <span class="sep">/</span>
    <span class="current">Nouvelle</span>
</div>

<div class="page-head">
    <div>
        <h1>Nouvelle évaluation</h1>
        <p class="page-sub">Les élèves concernés sont déterminés par la classe ou le groupe choisi. La saisie des notes s'ouvre une fois l'évaluation créée.</p>
    </div>
</div>

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

<form method="post" action="{{ route('evaluations.store') }}" class="ev-form" id="evaluation-form">
    @csrf
    <div class="ev-grid">
        <div class="ev-col">

            {{-- 1. Identification --}}
            <section class="panel panel-pad">
                <div class="step-head">
                    <span class="step-num">1</span>
                    <h2>Identification</h2>
                </div>

                <label class="stack">
                    <span>Nom de l'évaluation</span>
                    <input type="text" name="nom_evaluation" maxlength="50" required
                           value="{{ old('nom_evaluation') }}" data-nom
                           placeholder="ex. Partiel de mi-semestre — Droit du sport">
                    <span class="counter"><span data-nom-count>{{ mb_strlen(old('nom_evaluation', '')) }}</span> / 50</span>
                </label>

                <div class="triple">
                    <label class="stack">
                        <span>Campus</span>
                        <select name="id_campus" required data-campus>
                            @foreach ($etablissements as $etablissement)
                                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_campus') == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="stack">
                        <span>Année</span>
                        <input type="number" name="annee" required value="{{ old('annee', date('Y')) }}" data-annee>
                    </label>
                    <div class="stack">
                        <span>Semestre</span>
                        <div class="chips-row">
                            @foreach ([1, 2] as $s)
                                <label class="pill-radio {{ $semestreActuel === $s ? 'is-on' : '' }}">
                                    <input type="radio" name="semestre" value="{{ $s }}" @checked($semestreActuel === $s) required data-semestre>
                                    S{{ $s }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- 2. Élèves concernés --}}
            <section class="panel panel-pad">
                <div class="step-head">
                    <span class="step-num">2</span>
                    <h2>Élèves concernés</h2>
                </div>

                <div class="stack">
                    <span>Référentiel</span>
                    <div class="choice-grid">
                        @foreach (['classe' => ['Classe', 'Toute la promotion'], 'groupe' => ['Groupe', 'Sous-ensemble de TD']] as $valeur => [$libelle, $aide])
                            <label class="choice {{ $referentielActuel === $valeur ? 'is-on' : '' }}">
                                <input type="radio" name="referentiel" value="{{ $valeur }}"
                                       @checked($referentielActuel === $valeur) required data-referentiel>
                                <span>
                                    <span class="choice-label">{{ $libelle }}</span>
                                    <span class="choice-hint">{{ $aide }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Un select par référentiel : seul celui du référentiel actif est visible et soumis. --}}
                <label class="stack" data-cible="classe" @unless($referentielActuel === 'classe') hidden @endunless>
                    <span>Classe</span>
                    <select name="id_referentiel" required @disabled($referentielActuel !== 'classe') data-cible-select>
                        @foreach ($classes ?? [] as $classe)
                            <option value="{{ $classe->id_classe }}"
                                    data-effectif="{{ $classe->eleves_count ?? '' }}"
                                    @selected(old('id_referentiel') == $classe->id_classe)>{{ $classe->classe }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="stack" data-cible="groupe" @unless($referentielActuel === 'groupe') hidden @endunless>
                    <span>Groupe</span>
                    <select name="id_referentiel" required @disabled($referentielActuel !== 'groupe') data-cible-select>
                        @foreach ($groupes ?? [] as $groupe)
                            <option value="{{ $groupe->id_groupe }}"
                                    data-effectif="{{ $groupe->eleves_count ?? '' }}"
                                    @selected(old('id_referentiel') == $groupe->id_groupe)>{{ $groupe->nom_groupe ?? $groupe->groupe }}</option>
                        @endforeach
                    </select>
                </label>

                @if (empty($classes) && empty($groupes))
                    {{-- Repli : le contrôleur ne passe ni $classes ni $groupes → saisie de l'identifiant. --}}
                    <label class="stack" data-cible-fallback>
                        <span>Identifiant de la classe ou du groupe</span>
                        <input type="number" name="id_referentiel" required value="{{ old('id_referentiel') }}">
                        <span class="hint-line">Passez <code>$classes</code> et <code>$groupes</code> à la vue pour obtenir un vrai sélecteur.</span>
                    </label>
                @else
                    <div class="note-inline" data-effectif-box hidden>
                        @include('partials.icon', ['n' => 'users', 's' => 16, 'c' => '#4f46e5'])
                        <span data-effectif-text></span>
                    </div>
                @endif
            </section>

            {{-- 3. Rattachement pédagogique --}}
            <section class="panel panel-pad">
                <div class="step-head">
                    <span class="step-num">3</span>
                    <h2>Rattachement pédagogique</h2>
                </div>

                <label class="stack">
                    <span>Unité d'enseignement <em style="font-weight:400;color:var(--muted)">(facultatif)</em></span>
                    {{-- Découpage du supérieur : sans objet en K-12, où l'on note par matière. --}}
                    <select name="id_ue">
                        <option value="">— Aucune —</option>
                        @foreach ($unites as $unite)
                            <option value="{{ $unite->id_unite_enseignement }}" @selected(old('id_ue') == $unite->id_unite_enseignement)>{{ $unite->nom_unite_enseignement }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="stack">
                    <span>Matière</span>
                    <select name="id_matiere" required>
                        @foreach ($cours as $cour)
                            <option value="{{ $cour->id_cours }}" @selected(old('id_matiere') == $cour->id_cours)>{{ $cour->nom_cours }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="stack">
                    <span>Type d'évaluation</span>
                    <div class="type-list">
                        @foreach ($typesEvaluation as $index => $te)
                            @php $on = old('id_type_evaluation') ? old('id_type_evaluation') == $te->id_type_evaluation : $loop->first; @endphp
                            <label class="type-row {{ $on ? 'is-on' : '' }}">
                                <input type="radio" name="id_type_evaluation" value="{{ $te->id_type_evaluation }}"
                                       @checked($on) required data-type data-coef="{{ $te->coef }}"
                                       data-label="{{ $te->type?->type }}">
                                <span class="type-label">{{ $te->type?->type ?? 'Type #'.$te->id_type_evaluation }}</span>
                                <span class="type-coef">coef {{ $te->coef }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- 4. Déroulé et notation --}}
            <section class="panel panel-pad">
                <div class="step-head">
                    <span class="step-num">4</span>
                    <h2>Déroulé et notation</h2>
                </div>

                <div class="triple">
                    <label class="stack">
                        <span>Date</span>
                        <input type="date" name="date_evaluation" required value="{{ old('date_evaluation') }}">
                    </label>
                    <label class="stack">
                        <span>Heure de début</span>
                        <input type="time" name="heure_debut" value="{{ old('heure_debut') }}" data-debut>
                    </label>
                    <label class="stack">
                        <span>Heure de fin</span>
                        <input type="time" name="heure_fin" value="{{ old('heure_fin') }}" data-fin>
                    </label>
                </div>
                <p class="duree" data-duree hidden></p>

                <div class="divider">
                    <span class="stack-label">Barème</span>
                    <div class="chips-row">
                        @foreach ($baremesConnus as $valeur)
                            <button type="button" class="pill-btn {{ $baremeActuel === $valeur ? 'is-on' : '' }}"
                                    data-bareme-preset="{{ $valeur }}">Sur {{ $valeur }}</button>
                        @endforeach
                        <input type="text" name="type_notation" maxlength="2" required
                               value="{{ $baremeActuel }}" data-bareme class="bareme-input"
                               inputmode="numeric" aria-label="Barème">
                    </div>
                </div>

                <label class="choice is-standalone {{ old('boolean_facultatif') ? 'is-on' : '' }}">
                    <input type="checkbox" name="boolean_facultatif" value="1" @checked(old('boolean_facultatif')) data-facultatif>
                    <span>
                        <span class="choice-label">Évaluation facultative</span>
                        <span class="choice-hint">Une note absente n'abaisse pas la moyenne de l'unité d'enseignement.</span>
                    </span>
                </label>
            </section>
        </div>

        {{-- Récapitulatif --}}
        <div class="ev-col">
            <section class="panel is-sticky">
                <div class="sum-head">
                    <div class="field-label">Récapitulatif</div>
                    <div class="sum-name" data-sum-name>Évaluation sans nom</div>
                    <div class="sum-pills">
                        <span class="pill pill-brand" data-sum-type>—</span>
                        <span class="pill pill-slate" data-sum-coef>coef —</span>
                        <span class="pill pill-amber" data-sum-fac hidden>Facultative</span>
                    </div>
                </div>

                <div class="sum-lines">
                    <div class="sum-line"><span>Campus</span><span data-sum-campus>—</span></div>
                    <div class="sum-line"><span>Période</span><span data-sum-periode>—</span></div>
                    <div class="sum-line"><span data-sum-cible-label>Classe</span><span data-sum-cible>—</span></div>
                    <div class="sum-line" data-sum-effectif-row hidden><span>Effectif</span><span data-sum-effectif>—</span></div>
                    <div class="sum-line"><span>Barème</span><span data-sum-bareme>—</span></div>
                </div>

                <div class="sum-foot">
                    <button type="submit" class="btn btn-block">
                        Créer l'évaluation
                        @include('partials.icon', ['n' => 'arrow-right', 's' => 16, 'c' => '#fff', 'w' => 2.2])
                    </button>
                    <a href="{{ route('evaluations.index') }}" class="sum-cancel">Annuler</a>
                </div>
            </section>
        </div>
    </div>
</form>

<style>
    /* Nouvelle évaluation : styles spécifiques (le reste vient de layouts/app.blade.php) */
    .ev-form { max-width: none; }
    .ev-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(330px, 1fr)); gap: 14px; align-items: start; }
    .ev-col { display: flex; flex-direction: column; gap: 14px; min-width: 0; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-pad { padding: 18px; }

    .step-head { display: flex; align-items: center; gap: 9px; margin-bottom: 15px; }
    .step-num {
        width: 24px; height: 24px; border-radius: 7px; flex-shrink: 0; background: var(--brand-light);
        color: var(--brand-deep); font-size: 11px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
    }
    .step-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }

    .stack { display: block; margin: 0 0 14px; }
    .stack:last-child { margin-bottom: 0; }
    .stack > span:first-child, .stack-label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
    .stack input, .stack select { width: 100%; max-width: none; }
    .stack input { background: #fafbfd; }
    .stack select { cursor: pointer; }
    .counter { display: block; font-size: 11.5px; color: var(--faint); margin-top: 5px; text-align: right; font-variant-numeric: tabular-nums; }
    .hint-line { display: block; font-size: 11.5px; color: var(--muted); margin-top: 5px; }
    .triple { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; }
    .triple .stack { margin: 0; }
    .divider { margin-top: 16px; padding-top: 15px; border-top: 1px solid var(--border-soft); }

    .choice-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 8px; }
    .choice {
        display: flex; align-items: flex-start; gap: 9px; margin: 0; padding: 11px 12px;
        border: 1px solid var(--border); border-radius: 11px; background: #fff; cursor: pointer;
        min-width: 0; font-weight: 400; transition: border-color .14s ease, background .14s ease;
    }
    .choice:hover { border-color: #c3c6f5; }
    .choice.is-on { border-color: #c3c6f5; background: #fafbff; }
    .choice.is-standalone { margin-top: 16px; padding: 12px 13px; }
    .choice input { width: 15px; height: 15px; margin: 2px 0 0; flex-shrink: 0; }
    .choice-label { display: block; font-size: 13.5px; font-weight: 600; }
    .choice-hint { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; text-wrap: pretty; }

    .note-inline {
        display: flex; align-items: center; gap: 9px; margin-top: 13px; padding: 11px 13px;
        border-radius: 10px; background: #fafbff; border: 1px solid #eef0f5;
        font-size: 12.5px; color: #585e72;
    }
    .note-inline svg { flex-shrink: 0; }

    .chips-row { display: flex; gap: 7px; flex-wrap: wrap; align-items: center; }
    .pill-radio, .pill-btn {
        display: inline-flex; align-items: center; gap: 7px; margin: 0; padding: 9px 14px;
        border: 1px solid var(--border); border-radius: 999px; background: #fff;
        color: #585e72; font: inherit; font-size: 12.5px; font-weight: 600; cursor: pointer;
        transition: border-color .14s ease, background .14s ease, color .14s ease;
    }
    .pill-radio:hover, .pill-btn:hover { border-color: #c3c6f5; background: #fff; }
    .pill-radio.is-on, .pill-btn.is-on { border-color: #c3c6f5; background: var(--brand-light); color: var(--brand-deep); }
    .pill-radio input { position: absolute; opacity: 0; width: 0; height: 0; }
    .bareme-input {
        width: 78px; max-width: none; border-radius: 999px; padding: 9px 13px;
        font-size: 12.5px; text-align: center; font-variant-numeric: tabular-nums; background: #fafbfd;
    }

    .type-list { display: flex; flex-direction: column; gap: 7px; }
    .type-row {
        display: flex; align-items: center; gap: 10px; margin: 0; padding: 10px 12px;
        border: 1px solid var(--border); border-radius: 10px; background: #fff; cursor: pointer;
        font-weight: 400; transition: border-color .14s ease, background .14s ease;
    }
    .type-row:hover { border-color: #c3c6f5; }
    .type-row.is-on { border-color: #c3c6f5; background: #fafbff; }
    .type-row input { width: 15px; height: 15px; margin: 0; flex-shrink: 0; }
    .type-label { font-size: 13px; font-weight: 600; min-width: 0; }
    .type-coef {
        margin-left: auto; padding: 2px 8px; border-radius: 6px; white-space: nowrap;
        font-size: 11.5px; font-weight: 700; background: #f0f1f6; color: var(--muted);
    }
    .type-row.is-on .type-coef { background: var(--brand-light); color: var(--brand-deep); }

    .duree { margin: 9px 0 0; font-size: 12px; color: var(--muted); }
    .duree.is-error { color: var(--danger-dark); font-weight: 600; }

    /* Récapitulatif */
    .is-sticky { position: sticky; top: 76px; }
    .sum-head { padding: 16px 18px 14px; border-bottom: 1px solid var(--border-soft); }
    .field-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .sum-name { font-size: 16px; font-weight: 700; letter-spacing: -.015em; margin-top: 7px; text-wrap: pretty; }
    .sum-pills { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 9px; }
    .pill { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 700; }
    .pill-brand { background: var(--brand-light); color: var(--brand-deep); }
    .pill-slate { background: #eef1f6; color: #475569; }
    .pill-amber { background: #fdf3e3; color: #92400e; }

    .sum-lines { display: flex; flex-direction: column; }
    .sum-line { display: flex; align-items: center; gap: 10px; padding: 10px 18px; border-bottom: 1px solid #f6f7fa; }
    .sum-line span:first-child { font-size: 12.5px; color: var(--muted); min-width: 0; }
    .sum-line span:last-child { margin-left: auto; font-size: 12.5px; font-weight: 600; text-align: right; max-width: 60%; }

    .sum-foot { padding: 14px 18px; background: #fbfbff; border-top: 1px solid var(--border-soft); }
    .sum-foot .btn-block { width: 100%; justify-content: center; padding: 12px 16px; font-size: 14px; }
    .sum-cancel { display: block; margin-top: 9px; text-align: center; font-size: 12.5px; color: var(--muted); font-weight: 600; }
    .sum-cancel:hover { color: var(--ink); }
</style>

<script>
    (function () {
        var form = document.getElementById('evaluation-form');
        if (!form) return;

        var q = function (sel) { return form.querySelector(sel); };
        var qa = function (sel) { return Array.prototype.slice.call(form.querySelectorAll(sel)); };

        var nom = q('[data-nom]');
        var campus = q('[data-campus]');
        var annee = q('[data-annee]');
        var bareme = q('[data-bareme]');
        var debut = q('[data-debut]');
        var fin = q('[data-fin]');
        var facultatif = q('[data-facultatif]');
        var effectifBox = q('[data-effectif-box]');
        var effectifText = q('[data-effectif-text]');

        function texte(sel, valeur) {
            var el = q(sel);
            if (el) el.textContent = valeur;
        }

        // Bascule classe / groupe : seul le select actif est visible et soumis.
        function syncReferentiel() {
            var actif = (qa('[data-referentiel]').filter(function (r) { return r.checked; })[0] || {}).value || 'classe';
            qa('.choice-grid .choice').forEach(function (c) {
                c.classList.toggle('is-on', c.querySelector('input').checked);
            });
            qa('[data-cible]').forEach(function (bloc) {
                var on = bloc.dataset.cible === actif;
                bloc.hidden = !on;
                var select = bloc.querySelector('[data-cible-select]');
                if (select) select.disabled = !on;
            });
            texte('[data-sum-cible-label]', actif === 'groupe' ? 'Groupe' : 'Classe');
        }

        function syncCible() {
            var bloc = qa('[data-cible]').filter(function (b) { return !b.hidden; })[0];
            var select = bloc && bloc.querySelector('[data-cible-select]');
            if (!select || !select.options.length) {
                if (effectifBox) effectifBox.hidden = true;
                return;
            }
            var option = select.options[select.selectedIndex];
            texte('[data-sum-cible]', option.textContent.trim());

            var effectif = option.getAttribute('data-effectif');
            var row = q('[data-sum-effectif-row]');
            if (effectif) {
                if (effectifBox) {
                    effectifBox.hidden = false;
                    effectifText.textContent = effectif + ' élèves seront concernés par cette évaluation';
                }
                if (row) row.hidden = false;
                texte('[data-sum-effectif]', effectif + ' élèves');
            } else {
                if (effectifBox) effectifBox.hidden = true;
                if (row) row.hidden = true;
            }
        }

        function syncType() {
            qa('.type-row').forEach(function (r) {
                r.classList.toggle('is-on', r.querySelector('input').checked);
            });
            var actif = qa('[data-type]').filter(function (t) { return t.checked; })[0];
            texte('[data-sum-type]', actif ? (actif.dataset.label || 'Type') : '—');
            texte('[data-sum-coef]', actif ? 'coef ' + actif.dataset.coef : 'coef —');
        }

        function syncDuree() {
            var el = q('[data-duree]');
            if (!el || !debut || !fin) return;
            var toMin = function (v) {
                var p = String(v).split(':');
                var h = parseInt(p[0], 10), m = parseInt(p[1], 10);
                return isNaN(h) || isNaN(m) ? null : h * 60 + m;
            };
            var d = toMin(debut.value), f = toMin(fin.value);
            if (d === null || f === null) { el.hidden = true; return; }
            el.hidden = false;
            var delta = f - d;
            if (delta <= 0) {
                el.textContent = "L'heure de fin doit suivre l'heure de début.";
                el.className = 'duree is-error';
                return;
            }
            var h = Math.floor(delta / 60), m = delta % 60;
            el.textContent = 'Durée : ' + (h ? h + ' h' : '') + (m ? (h ? ' ' : '') + m + ' min' : '');
            el.className = 'duree';
        }

        function syncRecap() {
            texte('[data-sum-name]', (nom && nom.value.trim()) || 'Évaluation sans nom');
            if (nom) texte('[data-nom-count]', String(nom.value.length));
            if (campus) texte('[data-sum-campus]', campus.options[campus.selectedIndex].textContent.trim());
            var semestre = (qa('[data-semestre]').filter(function (s) { return s.checked; })[0] || {}).value || '1';
            texte('[data-sum-periode]', (annee ? annee.value : '') + ' · S' + semestre);
            texte('[data-sum-bareme]', bareme && bareme.value ? 'Sur ' + bareme.value : '—');
            var fac = q('[data-sum-fac]');
            if (fac && facultatif) fac.hidden = !facultatif.checked;
        }

        // Barème : pastilles prédéfinies + champ libre
        qa('[data-bareme-preset]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                bareme.value = btn.dataset.baremePreset;
                syncBaremePills();
                syncRecap();
            });
        });
        function syncBaremePills() {
            qa('[data-bareme-preset]').forEach(function (b) {
                b.classList.toggle('is-on', b.dataset.baremePreset === bareme.value);
            });
        }

        // Écoutes
        qa('[data-referentiel]').forEach(function (r) {
            r.addEventListener('change', function () { syncReferentiel(); syncCible(); });
        });
        qa('[data-cible-select]').forEach(function (s) { s.addEventListener('change', syncCible); });
        qa('[data-type]').forEach(function (t) { t.addEventListener('change', syncType); });
        qa('[data-semestre]').forEach(function (s) {
            s.addEventListener('change', function () {
                qa('.pill-radio').forEach(function (p) { p.classList.toggle('is-on', p.querySelector('input').checked); });
                syncRecap();
            });
        });
        if (nom) nom.addEventListener('input', syncRecap);
        if (campus) campus.addEventListener('change', syncRecap);
        if (annee) annee.addEventListener('input', syncRecap);
        if (debut) debut.addEventListener('change', syncDuree);
        if (fin) fin.addEventListener('change', syncDuree);
        if (bareme) bareme.addEventListener('input', function () {
            bareme.value = bareme.value.replace(/\D/g, '').slice(0, 2);
            syncBaremePills();
            syncRecap();
        });
        if (facultatif) facultatif.addEventListener('change', function () {
            facultatif.closest('.choice').classList.toggle('is-on', facultatif.checked);
            syncRecap();
        });

        syncReferentiel();
        syncCible();
        syncType();
        syncDuree();
        syncBaremePills();
        syncRecap();
    })();
</script>
@endsection
