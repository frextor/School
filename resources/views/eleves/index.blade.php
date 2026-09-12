@extends('layouts.app')

@section('title', ($filtres['archives'] ?? false) ? 'Élèves archivés' : 'Élèves')

@section('content')
@php
    $archives = $filtres['archives'] ?? false;
    $titre = $archives ? 'Élèves archivés' : 'Élèves';

    // Puces de filtres actifs : libellé => [paramètre de requête, valeur affichée]
    // (le paramètre est explicite plutôt que dérivé du libellé — "Formation" est
    // porté par le champ `niveau`, pas `formation`).
    $chips = collect([
        'Recherche' => ['recherche', $filtres['recherche'] ?? null],
        'Profil' => ['profil', $filtres['profil'] ?? null],
        'Campus' => ['campus', request('campus')],
        'Formation' => ['niveau', request('niveau')],
        'Classe' => ['classe', request('classe')],
        'Année' => ['annee', request('annee')],
    ])->filter(fn ($paire) => filled($paire[1]))->all();

    $badgeParProfil = [
        'eleve' => ['#eef0fe', '#3730a3'],
        'candidat' => ['#fdf3e3', '#92400e'],
        'alumni' => ['#f4ecfd', '#6d28d9'],
        'reinscrit' => ['#e7f6f2', '#0f766e'],
        'abandon' => ['#fdecef', '#be123c'],
    ];
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">{{ $titre }}</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $titre }}</h1>
            <span class="badge badge-brand">{{ number_format($eleves->total(), 0, ',', ' ') }} élèves</span>
        </div>
        <p class="page-sub">{{ $archives ? 'Dossiers archivés.' : 'Dossiers actifs, tous campus.' }}</p>
    </div>
    <div class="page-actions">
        <button type="submit" form="filtres-eleves" name="export" value="1" class="btn btn-ghost">
            @include('partials.icon', ['n' => 'download', 's' => 15, 'w' => 2])Exporter
        </button>
        <a class="btn" href="{{ route('eleves.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouvel élève
        </a>
    </div>
</div>

{{-- ---------- Recherche et filtres ---------- --}}
<form method="get" id="filtres-eleves" class="filter-card">
    <div class="filter-row">
        <div class="filter-search">
            @include('partials.icon', ['n' => 'search', 's' => 16, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:13px;top:11px'])
            <input type="text" name="recherche" value="{{ $filtres['recherche'] ?? '' }}"
                   placeholder="Nom, prénom, email ou téléphone" aria-label="Recherche">
        </div>

        <select name="profil" aria-label="Profil">
            <option value="">Profil : tous</option>
            @foreach (['eleve', 'alumni', 'reinscrit', 'abandon'] as $profil)
                <option value="{{ $profil }}" @selected(($filtres['profil'] ?? '') === $profil)>{{ ucfirst($profil) }}</option>
            @endforeach
        </select>

        <select name="campus" aria-label="Campus">
            <option value="">Campus : tous</option>
            @foreach ($etablissements ?? [] as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(request('campus') == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <select name="niveau" aria-label="Formation">
            <option value="">Formation : toutes</option>
            @foreach ($niveaux ?? [] as $niveau)
                <option value="{{ $niveau->id_niveau }}" @selected(request('niveau') == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
            @endforeach
        </select>

        <select name="classe" aria-label="Classe">
            <option value="">Classe : toutes</option>
            @foreach ($classes ?? [] as $classe)
                <option value="{{ $classe->id_classe }}" @selected(request('classe') == $classe->id_classe)>{{ $classe->classe }}</option>
            @endforeach
        </select>

        <button type="button" class="btn btn-ghost" data-toggle-advanced aria-expanded="false">
            @include('partials.icon', ['n' => 'levels', 's' => 15, 'w' => 2, 'style' => 'transform:rotate(90deg)'])<span>Plus de filtres</span>
        </button>
    </div>

    <div class="filter-advanced" hidden>
        <label>
            <span>Année de formation</span>
            <select name="annee">
                <option value="">Toutes</option>
                @foreach ($annees ?? [] as $annee)
                    <option value="{{ $annee }}" @selected(request('annee') == $annee)>{{ $annee }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Visible</span>
            <select name="visible">
                <option value="">Tous</option>
                <option value="1" @selected(request('visible') === '1')>Oui</option>
                <option value="0" @selected(request('visible') === '0')>Non</option>
            </select>
        </label>
        <label>
            <span>Archivés</span>
            <select name="archives">
                <option value="">Non</option>
                <option value="1" @selected($archives)>Oui</option>
            </select>
        </label>
    </div>

    <div class="filter-foot">
        @if (count($chips))
            <span class="filter-foot-label">Filtres actifs</span>
            @foreach ($chips as $label => [$param, $valeur])
                <a href="{{ request()->fullUrlWithQuery([$param => null]) }}" class="chip">
                    {{ $label }} : {{ $valeur }}
                    @include('partials.icon', ['n' => 'plus', 's' => 12, 'w' => 2.4, 'style' => 'transform:rotate(45deg)'])
                </a>
            @endforeach
        @endif
        <a href="{{ route('eleves.index') }}" class="filter-reset">Réinitialiser</a>
        <button type="submit" class="btn">Rechercher</button>
    </div>
</form>

{{-- ---------- Tableau ---------- --}}
<div class="table-card">
    <div class="table-head">
        <span class="table-count" data-selection-idle>
            {{ $eleves->firstItem() }}–{{ $eleves->lastItem() }} sur {{ number_format($eleves->total(), 0, ',', ' ') }}
        </span>
        <div class="bulk-bar" data-selection-active hidden>
            <span class="bulk-count"><strong data-selection-count>0</strong> sélectionné(s)</span>
            <div class="bulk-actions">
                <button type="button">Affecter une classe</button>
                <button type="button">Envoyer un email</button>
                <button type="button" class="btn-danger">Archiver</button>
            </div>
        </div>
    </div>

    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-check"><input type="checkbox" data-check-all aria-label="Tout sélectionner"></th>
                    @foreach (['nom' => 'Élève', 'id_eleve' => 'ID', 'campus' => 'Campus', 'niveau' => 'Formation / classe', 'annee' => 'Année', 'profil' => 'Profil', 'visible' => 'Visible'] as $colonne => $libelle)
                        @php $actif = request('tri') === $colonne; $sens = ($actif && request('sens') === 'asc') ? 'desc' : 'asc'; @endphp
                        <th class="{{ $actif ? 'is-sorted' : '' }}">
                            <a href="{{ request()->fullUrlWithQuery(['tri' => $colonne, 'sens' => $sens]) }}">
                                {{ $libelle }}
                                @include('partials.icon', ['n' => 'levels', 's' => 11, 'w' => 2.6, 'style' => 'transform:rotate(90deg)'])
                            </a>
                        </th>
                    @endforeach
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($eleves as $eleve)
                    @php
                        $contact = $eleve->contact;
                        $nom = $contact?->nom_complet ?? '—';
                        $initiales = mb_strtoupper(mb_substr($contact?->prenom ?? '?', 0, 1).mb_substr($contact?->nom ?? '', 0, 1));
                        [$tintBg, $tintInk] = $badgeParProfil[$eleve->profil] ?? ['#eef1f6', '#475569'];
                    @endphp
                    <tr>
                        <td class="col-check"><input type="checkbox" name="eleves[]" value="{{ $eleve->id_eleve }}" data-check-row aria-label="Sélectionner {{ $nom }}"></td>
                        <td>
                            <div class="cell-user">
                                <span class="cell-avatar">{{ $initiales }}</span>
                                <span class="cell-user-text">
                                    <a href="{{ route('eleves.show', $eleve) }}" class="cell-name">{{ $nom }}</a>
                                    <span class="cell-sub">{{ $contact?->email ?? '—' }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="num">{{ $eleve->id_eleve }}</td>
                        <td>{{ $eleve->etablissement?->nom_etablissement ?? '—' }}</td>
                        <td class="cell-wide">
                            <span>{{ $eleve->niveau?->nom_niveau ?? '—' }}</span>
                            <span class="cell-sub">{{ $eleve->classe?->classe ?? '—' }}</span>
                        </td>
                        <td class="num">{{ $eleve->annee_formation ?? '—' }}</td>
                        <td>
                            <span class="pill" style="background:{{ $tintBg }};color:{{ $tintInk }}">{{ ucfirst($eleve->profil) }}</span>
                        </td>
                        <td>
                            <span class="dot-status" style="color:{{ $eleve->visible ? '#0f766e' : '#b91c1c' }}">
                                <span class="dot" style="background:currentColor"></span>{{ $eleve->visible ? 'Oui' : 'Non' }}
                            </span>
                        </td>
                        <td class="col-actions">
                            <a href="{{ route('eleves.show', $eleve) }}" class="row-btn" title="Voir la fiche">
                                @include('partials.icon', ['n' => 'eye', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <a href="{{ route('eleves.edit', $eleve) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="empty-cell">
                            @include('partials.icon', ['n' => 'users', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun élève ne correspond à ces filtres.</span>
                            <a href="{{ route('eleves.index') }}">Réinitialiser la recherche</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-foot">
        {{ $eleves->withQueryString()->links() }}
    </div>
</div>

<script>
    (function () {
        // Panneau « Plus de filtres »
        var toggle = document.querySelector('[data-toggle-advanced]');
        var advanced = document.querySelector('.filter-advanced');
        if (toggle && advanced) {
            if (advanced.querySelector('select[value]:not([value=""])')) advanced.hidden = false;
            toggle.addEventListener('click', function () {
                advanced.hidden = !advanced.hidden;
                toggle.setAttribute('aria-expanded', advanced.hidden ? 'false' : 'true');
                toggle.querySelector('span').textContent = advanced.hidden ? 'Plus de filtres' : 'Moins de filtres';
            });
        }

        // Sélection de lignes + barre d'actions groupées
        var all = document.querySelector('[data-check-all]');
        var rows = Array.prototype.slice.call(document.querySelectorAll('[data-check-row]'));
        var idle = document.querySelector('[data-selection-idle]');
        var active = document.querySelector('[data-selection-active]');
        var counter = document.querySelector('[data-selection-count]');
        if (!rows.length) return;

        function sync() {
            var n = rows.filter(function (r) { return r.checked; }).length;
            if (counter) counter.textContent = n;
            if (idle) idle.hidden = n > 0;
            if (active) active.hidden = n === 0;
            if (all) {
                all.checked = n === rows.length;
                all.indeterminate = n > 0 && n < rows.length;
            }
        }
        if (all) all.addEventListener('change', function () {
            rows.forEach(function (r) { r.checked = all.checked; });
            sync();
        });
        rows.forEach(function (r) { r.addEventListener('change', sync); });
        sync();
    })();
</script>
@endsection
