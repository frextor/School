@extends('layouts.app')

@section('title', "Types d'évaluation")

@section('content')
@php
    // Un coefficient affiché ne se lit pas ici mais dans les paramétrages par
    // classe : on montre la plage constatée, ou rien si le type n'a jamais servi.
    $coefficient = function (?array $usage) {
        if (! $usage || ! $usage['parametrages']) {
            return null;
        }

        $format = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, ',', ''), '0'), ',');
        $min = $format($usage['coef_min']);
        $max = $format($usage['coef_max']);

        return $min === $max ? 'coef. '.$min : 'coef. '.$min.' à '.$max;
    };
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Types d'évaluation</span>
</div>

@if (session('error'))
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

<div class="page-head">
    <div>
        <h1>Types d'évaluation</h1>
        <p class="page-sub">Le vocabulaire commun à toutes les notes de l'école : contrôle continu, activités intégrées, examen de fin de semestre…</p>
    </div>
</div>

<div class="te-grid">
    <div class="te-col">
        <div class="table-card">
            <div class="table-head">
                <span class="table-count">{{ $types->total() }} type{{ $types->total() > 1 ? 's' : '' }} au dictionnaire</span>
            </div>

            <div class="table-scroll">
                <table class="data-table te-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Coefficient constaté</th>
                            <th>Paramétrages</th>
                            <th>Évaluations</th>
                            <th>Notes</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($types as $type)
                            @php
                                $usage = $usages[$type->id_type] ?? null;
                                $utilise = $usage && ($usage['parametrages'] || $usage['evaluations'] || $usage['notes']);
                                $coef = $coefficient($usage);
                            @endphp
                            <tr data-ligne>
                                <td>
                                    <span class="te-nom" data-affichage>
                                        <span class="te-mark">@include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => 'var(--brand-deep)', 'w' => 2])</span>
                                        <span class="cell-name">{{ $type->type }}</span>
                                    </span>

                                    {{-- Renommage sur place : la route update existait déjà,
                                         mais aucun écran ne l'appelait. --}}
                                    <form method="post" action="{{ route('types-evaluation.update', $type) }}" class="te-edit" data-edition hidden>
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="type" value="{{ $type->type }}" maxlength="50" required aria-label="Nom du type">
                                        <button type="submit" class="btn btn-sm">Enregistrer</button>
                                        <button type="button" class="btn btn-ghost btn-sm" data-annuler>Annuler</button>
                                    </form>
                                </td>

                                <td>
                                    @if ($coef)
                                        <span class="pill pill-brand">{{ $coef }}</span>
                                    @else
                                        <span class="te-vide">—</span>
                                    @endif
                                </td>

                                <td class="num">{{ $usage['parametrages'] ?? 0 }}</td>
                                <td class="num">{{ $usage['evaluations'] ?? 0 }}</td>
                                <td class="num">{{ $usage['notes'] ?? 0 }}</td>

                                <td class="col-actions">
                                    <button type="button" class="row-btn" data-renommer title="Renommer">
                                        @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                    </button>

                                    @if ($utilise)
                                        {{-- Supprimé, ce type laisserait des notes orphelines que
                                             le bulletin ne saurait plus pondérer. --}}
                                        <span class="row-btn is-off" title="Utilisé : renommez-le plutôt que de le supprimer">
                                            @include('partials.icon', ['n' => 'lock', 's' => 14, 'c' => '#c3c6d4', 'w' => 2])
                                        </span>
                                    @else
                                        <form method="post" action="{{ route('types-evaluation.destroy', $type) }}" class="te-suppr"
                                              onsubmit="return confirm('Supprimer « {{ $type->type }} » ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="row-btn" title="Supprimer">
                                                @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-cell">
                                    @include('partials.icon', ['n' => 'tag', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
                                    <span>Aucun type d'évaluation. Sans type, aucune évaluation ne peut être créée.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($types->hasPages())
                <div class="table-foot">{{ $types->links() }}</div>
            @endif
        </div>
    </div>

    <div class="te-col">
        <section class="panel panel-pad">
            <div class="field-label">Ajouter un type</div>

            <form method="post" action="{{ route('types-evaluation.store') }}" class="te-add">
                @csrf
                <input type="text" name="type" value="{{ old('type') }}" maxlength="50" required
                       placeholder="Ex. Examen blanc" aria-label="Nom du type d'évaluation">
                <button type="submit" class="btn btn-block">
                    @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Ajouter
                </button>
            </form>

            @if ($suggestions->isNotEmpty())
                <div class="te-sugg">
                    <span class="te-sugg-label">Types usuels au Maroc, encore absents</span>
                    @foreach ($suggestions as $suggestion)
                        <form method="post" action="{{ route('types-evaluation.store') }}">
                            @csrf
                            <input type="hidden" name="type" value="{{ $suggestion }}">
                            <button type="submit" class="chip chip-add">
                                @include('partials.icon', ['n' => 'plus', 's' => 13, 'c' => 'var(--brand-deep)', 'w' => 2.4]){{ $suggestion }}
                            </button>
                        </form>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="panel panel-pad te-aide">
            <div class="field-label">Où se règle le coefficient ?</div>
            <p>
                Ce dictionnaire ne porte que le <strong>nom</strong> du type. Le coefficient se
                définit par classe et par période, à la création d'une évaluation : un contrôle
                continu peut peser 1 en 6AEP et 2 en 2BAC.
            </p>
            <p>La colonne « Coefficient constaté » résume les valeurs déjà paramétrées.</p>
            @if (Route::has('evaluations.create'))
                <a href="{{ route('evaluations.create') }}" class="te-lien">
                    Créer une évaluation @include('partials.icon', ['n' => 'arrow-right', 's' => 14, 'c' => 'var(--brand)', 'w' => 2])
                </a>
            @endif
        </section>
    </div>
</div>

<style>
    /* Types d'évaluation : dictionnaire à gauche, ajout et rappel à droite. */
    .te-grid { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 14px; align-items: start; }
    .te-col { display: flex; flex-direction: column; gap: 14px; min-width: 0; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-pad { padding: 18px; }
    .field-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }

    /* Le dictionnaire tient en six colonnes courtes : pas de largeur minimale
       héritée des grandes listes, qui forcerait un scroll inutile. */
    .te-table { min-width: 640px; }
    .te-nom { display: flex; align-items: center; gap: 10px; }
    .te-mark {
        width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0; background: var(--brand-light);
        display: flex; align-items: center; justify-content: center;
    }
    .te-vide { color: var(--faint); }
    .pill-brand { background: var(--brand-light); color: var(--brand-deep); }

    .te-edit { display: flex; align-items: center; gap: 7px; max-width: none; margin: 0; }
    .te-edit input { max-width: 220px; margin: 0; }
    .btn-sm { padding: .34rem .7rem; font-size: 12px; box-shadow: none; }
    .te-suppr { display: inline-block; margin: 0; }
    .row-btn.is-off { cursor: not-allowed; background: #f7f8fb; }

    .te-add { max-width: none; margin: 12px 0 0; }
    .te-add input { width: 100%; max-width: none; }
    .btn-block { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; margin-top: 10px; }

    .te-sugg { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--border-soft); }
    .te-sugg-label { width: 100%; font-size: 11.5px; color: var(--muted); margin-bottom: 2px; }
    .te-sugg form { max-width: none; margin: 0; }
    .chip-add {
        display: inline-flex; align-items: center; gap: 5px; padding: 6px 11px; border-radius: 999px;
        border: 1px dashed #c3c6f5; background: #fff; color: var(--brand-deep);
        font: inherit; font-size: 12px; font-weight: 600; cursor: pointer;
    }
    .chip-add:hover { background: var(--brand-light); border-style: solid; }

    .te-aide p { margin: 10px 0 0; font-size: 12.5px; color: #585e72; line-height: 1.55; text-wrap: pretty; }
    .te-lien { display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; font-size: 12.5px; font-weight: 600; }

    @media (max-width: 980px) {
        .te-grid { grid-template-columns: minmax(0, 1fr); }
    }
</style>

<script>
    (function () {
        // Renommage sur place : on échange l'affichage et le formulaire de la ligne.
        document.querySelectorAll('[data-ligne]').forEach(function (ligne) {
            var affichage = ligne.querySelector('[data-affichage]');
            var edition = ligne.querySelector('[data-edition]');
            var ouvrir = ligne.querySelector('[data-renommer]');
            var annuler = ligne.querySelector('[data-annuler]');
            if (!affichage || !edition || !ouvrir) return;

            function basculer(enEdition) {
                affichage.hidden = enEdition;
                edition.hidden = !enEdition;
                ouvrir.disabled = enEdition;
                if (enEdition) {
                    var champ = edition.querySelector('input[name="type"]');
                    champ.focus();
                    champ.select();
                }
            }

            ouvrir.addEventListener('click', function () { basculer(true); });
            if (annuler) annuler.addEventListener('click', function () { basculer(false); });

            edition.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') basculer(false);
            });
        });
    })();
</script>
@endsection
