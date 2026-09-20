@extends('layouts.app')

@section('title', 'Bulletins de notes')

@section('content')
@php
    $classesJson = $classes->map(fn ($c) => [
        'id_classe' => $c->id_classe,
        'classe' => $c->classe,
        'id_niveau' => (int) $c->id_niveau,
        'id_etablissement' => (int) $c->id_etablissement,
    ])->values();

    // Paramètres repris par l'écran de génération : l'élève arrive pré-sélectionné
    // avec la période cherchée, sans ressaisir la cascade campus/niveau/classe.
    $lienGeneration = fn ($eleve) => array_filter([
        'id_etablissement' => $eleve->classe?->id_etablissement,
        'id_niveau' => $eleve->id_niveau,
        'id_classe' => $eleve->id_classe,
        'id_eleve' => $eleve->id_eleve,
        'annee' => $filtres['annee'],
        'semestre' => $filtres['semestre'],
        'session' => $filtres['session'],
    ], fn ($v) => $v !== null && $v !== '');

    $libelleSemestre = fn ($s) => $s === null ? 'Tous semestres' : ($s ? 'Semestre '.$s : 'Année complète');
    $libelleSession = fn ($s) => $s === null ? 'Toutes sessions' : ($s ? 'Rattrapage '.$s : 'Session principale');
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Bulletins de notes</span>
</div>

<div class="page-head">
    <div>
        <h1>Bulletins de notes</h1>
        <p class="page-sub">Choisissez une population et une période : la recherche affiche les élèves concernés et le bulletin déjà produit pour chacun.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('bulletin-v2.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Générer un bulletin
        </a>
    </div>
</div>

<form method="get" action="{{ route('bulletin-v2.index') }}" class="filters filter-card">
    <input type="hidden" name="recherche" value="1">

    <div class="rech-grid">
        <label class="stack">
            <span>Campus</span>
            <select name="campus" data-campus>
                <option value="">Tous les campus</option>
                @foreach ($etablissements as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected($filtres['campus'] === $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
        </label>

        <label class="stack">
            <span>Niveau</span>
            <select name="niveau" data-niveau>
                <option value="">Tous les niveaux</option>
                @foreach ($niveaux as $niveau)
                    <option value="{{ $niveau->id_niveau }}" @selected($filtres['niveau'] === $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
                @endforeach
            </select>
        </label>

        <label class="stack">
            <span>Classe</span>
            <select name="classe" data-classe data-selected="{{ $filtres['classe'] }}">
                <option value="">Toutes les classes</option>
            </select>
        </label>

        <label class="stack">
            <span>Année scolaire</span>
            <select name="annee">
                <option value="">Toutes les années</option>
                @foreach ($annees as $annee)
                    <option value="{{ $annee }}" @selected($filtres['annee'] === (int) $annee)>{{ $annee }}-{{ $annee + 1 }}</option>
                @endforeach
            </select>
        </label>

        <label class="stack">
            <span>Semestre</span>
            <select name="semestre">
                <option value="">Tous les semestres</option>
                <option value="1" @selected($filtres['semestre'] === 1)>Semestre 1</option>
                <option value="2" @selected($filtres['semestre'] === 2)>Semestre 2</option>
                <option value="0" @selected($filtres['semestre'] === 0)>Année complète</option>
            </select>
        </label>

        <label class="stack">
            <span>Session</span>
            <select name="session">
                <option value="">Toutes les sessions</option>
                <option value="0" @selected($filtres['session'] === 0)>Session principale</option>
                <option value="1" @selected($filtres['session'] === 1)>Rattrapage</option>
            </select>
        </label>
    </div>

    <div class="rech-actions">
        <button type="submit" class="btn">
            @include('partials.icon', ['n' => 'search', 's' => 15, 'c' => '#fff', 'w' => 2.2])Rechercher
        </button>
        @if ($recherche)
            <a href="{{ route('bulletin-v2.index') }}" class="filter-reset">Réinitialiser</a>
        @endif
    </div>
</form>

@if ($recherche)
    <div class="table-card">
        <div class="table-head">
            <span class="table-count">
                {{ $eleves->total() }} élève{{ $eleves->total() > 1 ? 's' : '' }}
                · {{ $filtres['annee'] ? $filtres['annee'].'-'.($filtres['annee'] + 1) : 'toutes années' }}
                · {{ $libelleSemestre($filtres['semestre']) }}
                · {{ $libelleSession($filtres['session']) }}
            </span>
        </div>

        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Élève</th>
                        <th>Classe</th>
                        <th>Niveau</th>
                        <th>Campus</th>
                        <th>Bulletin</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($eleves as $eleve)
                        @php
                            $contact = $eleve->contact;
                            $nom = trim(($contact?->nom ?? '').' '.($contact?->prenom ?? '')) ?: 'Élève n°'.$eleve->id_eleve;
                            $bulletin = $bulletinsParEleve[$eleve->id_eleve] ?? null;
                            $initiales = mb_strtoupper(mb_substr($contact?->nom ?: 'E', 0, 1).mb_substr($contact?->prenom ?: '', 0, 1));
                        @endphp
                        <tr>
                            <td>
                                <div class="cell-user">
                                    <span class="cell-avatar">{{ $initiales }}</span>
                                    <span class="cell-user-text">
                                        <a href="{{ route('eleves.show', $eleve) }}" class="cell-name">{{ $nom }}</a>
                                        <span class="cell-sub">N° {{ $eleve->numero_massar ?: $eleve->id_eleve }}</span>
                                    </span>
                                </div>
                            </td>
                            <td>{{ $eleve->classe?->classe ?? '—' }}</td>
                            <td>{{ $eleve->niveau?->nom_niveau ?? '—' }}</td>
                            <td>{{ $eleve->classe?->etablissement?->nom_etablissement ?? '—' }}</td>
                            <td>
                                @if ($bulletin)
                                    <span class="dot-status" style="color:#15803d">
                                        <span class="dot" style="background:#16a34a"></span>
                                        Généré le {{ $bulletin->date_insert?->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="dot-status" style="color:var(--muted)">
                                        <span class="dot" style="background:#cbd0dd"></span>
                                        Pas encore généré
                                    </span>
                                @endif
                            </td>
                            <td class="col-actions">
                                @if ($bulletin)
                                    <a class="row-btn" href="{{ route('bulletin-v2.show', $bulletin) }}" target="_blank" title="Voir le PDF">
                                        @include('partials.icon', ['n' => 'download', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                    </a>
                                @endif
                                <a class="row-btn" title="Générer un bulletin"
                                   href="{{ route('bulletin-v2.create', $lienGeneration($eleve)) }}">
                                    @include('partials.icon', ['n' => 'printer', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">
                                @include('partials.icon', ['n' => 'search', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
                                <span>Aucun élève ne correspond à ces critères.</span>
                                <a href="{{ route('bulletin-v2.index') }}">Réinitialiser la recherche</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($eleves->hasPages())
            <div class="table-foot">{{ $eleves->links() }}</div>
        @endif
    </div>
@else
    <div class="table-card">
        <div class="table-head">
            <span class="table-count">Derniers bulletins générés</span>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Élève</th>
                        <th>Campus</th>
                        <th>Année scolaire</th>
                        <th>Semestre</th>
                        <th>Généré le</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($derniers as $bulletin)
                        <tr>
                            <td>{{ $bulletin->eleve?->contact?->nom_complet ?? 'Élève n°'.$bulletin->id_eleve }}</td>
                            <td>{{ $bulletin->etablissement?->nom_etablissement ?? '—' }}</td>
                            <td>{{ $bulletin->annee }}-{{ $bulletin->annee + 1 }}</td>
                            <td>{{ $bulletin->semestre ? 'S'.$bulletin->semestre : 'Année complète' }}</td>
                            <td>{{ $bulletin->date_insert?->format('d/m/Y H:i') }}</td>
                            <td class="col-actions">
                                <a class="row-btn" href="{{ route('bulletin-v2.show', $bulletin) }}" target="_blank" title="Voir le PDF">
                                    @include('partials.icon', ['n' => 'download', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">
                                @include('partials.icon', ['n' => 'file', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
                                <span>Aucun bulletin généré pour le moment.</span>
                                <a href="{{ route('bulletin-v2.create') }}">Générer le premier bulletin</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<style>
    /* Recherche de bulletins : six filtres alignés, le reste vient du layout. */
    .rech-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(165px, 1fr)); gap: 12px; }
    .rech-grid .stack { display: block; margin: 0; min-width: 0; }
    .rech-grid .stack > span:first-child { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
    .rech-grid select { width: 100%; max-width: none; cursor: pointer; }
    .rech-actions { display: flex; align-items: center; gap: 10px; margin-top: 13px; }
    .rech-actions .btn { display: inline-flex; align-items: center; gap: 7px; }
</style>

<script>
    (function () {
        var classes = @json($classesJson);
        var campus = document.querySelector('[data-campus]');
        var niveau = document.querySelector('[data-niveau]');
        var classe = document.querySelector('[data-classe]');
        if (!classe) return;

        // Cascade campus + niveau -> classes : on ne propose jamais une classe
        // qui viderait forcément le résultat.
        function remplir() {
            var idCampus = parseInt(campus.value, 10) || 0;
            var idNiveau = parseInt(niveau.value, 10) || 0;
            var choisie = classe.dataset.selected || '';

            var options = classes.filter(function (c) {
                return (!idCampus || c.id_etablissement === idCampus)
                    && (!idNiveau || c.id_niveau === idNiveau);
            });

            classe.innerHTML = '<option value="">Toutes les classes</option>' + options.map(function (c) {
                var sel = String(c.id_classe) === String(choisie) ? ' selected' : '';
                return '<option value="' + c.id_classe + '"' + sel + '>' + c.classe + '</option>';
            }).join('');
        }

        [campus, niveau].forEach(function (el) {
            if (el) el.addEventListener('change', function () {
                remplir();
                classe.dataset.selected = classe.value;
            });
        });

        remplir();
    })();
</script>
@endsection
