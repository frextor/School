@extends('layouts.app')

@section('title', 'Emploi du temps')

@section('content')
@php
    // Navigation et bascule de vue conservent les filtres en cours.
    $avec = fn (array $params) => route('planning.index', array_filter(
        array_merge(request()->query(), $params),
        fn ($v) => $v !== null && $v !== ''
    ));
    $urlSemaine = fn ($lundi) => $avec(['semaine' => $lundi->format('Y-m-d'), 'vue' => 'calendrier']);
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Emploi du temps</span>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

<div class="page-head">
    <div>
        <h1>Emploi du temps</h1>
        <p class="page-sub">La semaine de cours, classe par classe. Cliquez sur un créneau pour le modifier.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('planning.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouveau créneau
        </a>
    </div>
</div>

<form method="get" action="{{ route('planning.index') }}" class="filters filter-card">
    <input type="hidden" name="vue" value="{{ $vue }}">
    <input type="hidden" name="semaine" value="{{ $debutSemaine->format('Y-m-d') }}">

    <div class="filter-row">
        <select name="campus">
            <option value="">Tous les campus</option>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected($filtres['campus'] === $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <select name="classe">
            <option value="">Toutes les classes</option>
            @foreach ($classes as $classe)
                <option value="{{ $classe->id_classe }}" @selected($filtres['classe'] === (int) $classe->id_classe)>{{ $classe->classe }}</option>
            @endforeach
        </select>

        <select name="intervenant">
            <option value="">Tous les enseignants</option>
            @foreach ($intervenants as $intervenant)
                <option value="{{ $intervenant->id_intervenant }}" @selected($filtres['intervenant'] === $intervenant->id_intervenant)>{{ $intervenant->nom }} {{ $intervenant->prenom }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-ghost">Filtrer</button>

        <span class="pl-vues">
            <a href="{{ $avec(['vue' => 'calendrier']) }}" class="pl-vue @if ($vue === 'calendrier') is-active @endif">
                @include('partials.icon', ['n' => 'cal', 's' => 14, 'w' => 2])Calendrier
            </a>
            <a href="{{ $avec(['vue' => 'liste']) }}" class="pl-vue @if ($vue === 'liste') is-active @endif">
                @include('partials.icon', ['n' => 'list', 's' => 14, 'w' => 2])Liste
            </a>
        </span>
    </div>
</form>

@if ($vue === 'calendrier' && ! $cible)
    {{-- Sans classe ni enseignant, toutes les classes se superposeraient sur
         les mêmes heures : on fait choisir plutôt que d'afficher une bouillie. --}}
    <div class="cal-card pl-choix">
        @include('partials.icon', ['n' => 'cal', 's' => 28, 'c' => '#c3c6d4', 'w' => 1.6])
        <h2>Choisissez une classe ou un enseignant</h2>
        <p>Une grille horaire se lit pour une classe à la fois. Les {{ $classes->count() }} classes affichées ensemble se chevaucheraient sur les mêmes créneaux.</p>

        <div class="pl-choix-liste">
            @foreach ($classes as $classe)
                <a href="{{ $avec(['classe' => $classe->id_classe, 'vue' => 'calendrier']) }}" class="pl-choix-chip">
                    <i style="background: {{ $classe->couleur ?: '#4f46e5' }}"></i>{{ $classe->classe }}
                </a>
            @endforeach
        </div>

        <a href="{{ $avec(['vue' => 'liste']) }}" class="pl-choix-lien">
            Voir tous les créneaux en liste @include('partials.icon', ['n' => 'arrow-right', 's' => 14, 'c' => 'var(--brand)', 'w' => 2])
        </a>
    </div>
@elseif ($vue === 'calendrier')
    @include('partials.calendrier-semaine', [
        'semaine' => $semaine,
        'debutSemaine' => $debutSemaine,
        'urlSemaine' => $urlSemaine,
        'vide' => 'Aucun cours planifié cette semaine avec ces filtres.',
    ])

    @if ($classes->isNotEmpty())
        <div class="pl-legende">
            <span class="pl-legende-label">Classes</span>
            @foreach ($classes->take(12) as $classe)
                <span class="pl-puce"><i style="background: {{ $classe->couleur ?: '#4f46e5' }}"></i>{{ $classe->classe }}</span>
            @endforeach
        </div>
    @endif
@else
    <div class="table-card">
        <div class="table-head">
            <span class="table-count">{{ $creneaux->total() }} créneau{{ $creneaux->total() > 1 ? 'x' : '' }}</span>
        </div>

        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Cours</th>
                        <th>Classe</th>
                        <th>Enseignant</th>
                        <th>Campus</th>
                        <th>Salle</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($creneaux as $creneau)
                        <tr>
                            <td>
                                <span class="cell-name">{{ ucfirst($creneau->date_debut->translatedFormat('D j M Y')) }}</span>
                                <span class="cell-sub">{{ $creneau->date_debut->format('H:i') }} – {{ $creneau->date_fin->format('H:i') }}</span>
                            </td>
                            <td>{{ $creneau->cours?->nom_cours ?? '—' }}</td>
                            <td>
                                <span class="pl-puce"><i style="background: {{ $creneau->classe?->couleur ?: '#cbd0dd' }}"></i>{{ $creneau->classe?->classe ?? '—' }}</span>
                            </td>
                            <td>{{ trim(($creneau->intervenant?->nom ?? '').' '.($creneau->intervenant?->prenom ?? '')) ?: '—' }}</td>
                            <td>{{ $creneau->etablissement?->nom_etablissement ?? '—' }}</td>
                            <td>{{ $creneau->id_salle ?: '—' }}</td>
                            <td class="col-actions">
                                <a class="row-btn" href="{{ route('planning.edit', $creneau) }}" title="Modifier">
                                    @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                </a>
                                <form method="post" action="{{ route('planning.destroy', $creneau) }}" class="pl-suppr"
                                      onsubmit="return confirm('Supprimer ce créneau ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="row-btn" title="Supprimer">
                                        @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-cell">
                                @include('partials.icon', ['n' => 'cal', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
                                <span>Aucun créneau avec ces filtres.</span>
                                <a href="{{ route('planning.create') }}">Créer un créneau</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($creneaux->hasPages())
            <div class="table-foot">{{ $creneaux->links() }}</div>
        @endif
    </div>
@endif

<p class="pl-note">
    Créneaux ponctuels : ni détection de conflit de salle ou d'enseignant, ni récurrence —
    chaque séance est saisie pour sa date.
</p>

<style>
    /* Emploi du temps : bascule de vue, légende des classes. */
    .pl-vues { display: inline-flex; gap: 4px; margin-left: auto; padding: 3px; border-radius: 10px; background: #f3f4f9; }
    .pl-vue {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px;
        font-size: 12.5px; font-weight: 600; color: var(--muted);
    }
    .pl-vue svg { stroke: currentColor; }
    .pl-vue:hover { color: var(--brand-deep); }
    .pl-vue.is-active { background: #fff; color: var(--brand-deep); box-shadow: var(--shadow-sm); }

    .pl-legende { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; margin-top: 13px; }
    .pl-legende-label { font-size: 11.5px; font-weight: 600; color: var(--muted); }
    .pl-puce { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: #585e72; white-space: nowrap; }
    .pl-puce i { width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0; }

    .pl-choix { padding: 40px 24px; text-align: center; }
    .pl-choix h2 { margin: 12px 0 0; font-size: 15px; font-weight: 700; letter-spacing: -.015em; }
    .pl-choix p { margin: 7px auto 0; max-width: 56ch; font-size: 13px; color: var(--muted); text-wrap: pretty; }
    .pl-choix-liste { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-top: 18px; }
    .pl-choix-chip {
        display: inline-flex; align-items: center; gap: 7px; padding: 7px 13px; border-radius: 999px;
        border: 1px solid var(--border); background: #fff; font-size: 12.5px; font-weight: 600; color: #585e72;
    }
    .pl-choix-chip:hover { border-color: #c3c6f5; background: #fafbff; color: var(--brand-deep); }
    .pl-choix-chip i { width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0; }
    .pl-choix-lien { display: inline-flex; align-items: center; gap: 6px; margin-top: 18px; font-size: 12.5px; font-weight: 600; }

    .pl-suppr { display: inline-block; margin: 0; }
    .pl-note { margin: 14px 0 0; font-size: 12px; color: var(--muted); }
</style>
@endsection
