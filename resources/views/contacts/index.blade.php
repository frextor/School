@extends('layouts.app')

@section('title', 'Contacts')

@section('content')
@php
    $segmentActif = request('segment', 'tous');
    $segments = [
        'tous' => 'Tous les contacts',
        'prospects' => 'Prospects',
        'candidats' => 'Candidats',
        'agents' => 'Agents de joueur',
        'salons' => 'Salons',
        'stop' => 'Stop relances',
    ];

    $chips = collect([
        'Recherche' => ['recherche', $filtres['recherche'] ?? null],
        'Formation' => ['id_formation', request('id_formation') ? collect($formations ?? [])->firstWhere('id_formation', request('id_formation'))?->niveau : null],
        'École' => ['etablissement', request('etablissement')],
        'Ville' => ['ville', request('ville')],
        'Code postal' => ['code_postal', request('code_postal')],
        'Agents de joueur' => ['agent_de_joueur', ($filtres['agent_de_joueur'] ?? false) ? 'Oui' : null],
        'Salons' => ['salon', ($filtres['salon'] ?? false) ? 'Oui' : null],
        'Newsletter' => ['newsletter', request('newsletter') ? 'Oui' : null],
        'Stop relances' => ['stop_relances', request('stop_relances') ? 'Oui' : null],
    ])->filter(fn ($paire) => filled($paire[1]))->all();

    // Statut dérivé du contact (profil élève sinon prospect/candidat)
    $couleursStatut = [
        'eleve' => ['#eef0fe', '#3730a3', 'Élève'],
        'reinscrit' => ['#e7f6f2', '#0f766e', 'Réinscrit'],
        'alumni' => ['#f4ecfd', '#6d28d9', 'Alumni'],
        'abandon' => ['#fdecef', '#be123c', 'Abandon'],
        'candidat' => ['#fdf3e3', '#92400e', 'Candidat'],
    ];

    $pastilles = [
        'newsletter' => ['Newsletter', '#e7f6f2', '#0f766e', 'mail'],
        'offres_partenaires' => ['Offres partenaires', '#eef0fe', '#3730a3', 'tag'],
        'agent_de_joueur' => ['Agent de joueur', '#fdf3e3', '#92400e', 'users'],
        'salon' => ['Salon', '#f4ecfd', '#6d28d9', 'building'],
        'stop_relances' => ['Stop relances', '#fdecef', '#be123c', 'alert'],
    ];

    $colonnes = [
        'nom' => 'Contact',
        'telephone' => 'Téléphone',
        'ville' => 'Ville',
        'formation' => 'Formation / écoles',
        'origine' => 'Origine',
        'statut' => 'Statut',
    ];
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span>CRM</span>
    <span class="sep">/</span>
    <span class="current">Contacts</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Contacts</h1>
            <span class="badge badge-brand">{{ number_format($contacts->total(), 0, ',', ' ') }} contacts</span>
        </div>
        <p class="page-sub">Base CRM — prospects, leads et contacts issus des salons et réunions d'information.</p>
    </div>
    <div class="page-actions">
        @if (Route::has('import.index'))
            <a href="{{ route('import.index') }}" class="btn btn-ghost">
                @include('partials.icon', ['n' => 'inbox', 's' => 15, 'w' => 2])Importer
            </a>
        @endif
        <button type="submit" form="filtres-contacts" name="export" value="1" class="btn btn-ghost">
            @include('partials.icon', ['n' => 'download', 's' => 15, 'w' => 2])Exporter
        </button>
        @if (Route::has('contacts.create'))
            <a href="{{ route('contacts.create') }}" class="btn">
                @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouveau contact
            </a>
        @endif
    </div>
</div>

{{-- ---------- Segments ---------- --}}
<div class="segments">
    @foreach ($segments as $cle => $libelle)
        <a href="{{ request()->fullUrlWithQuery(['segment' => $cle === 'tous' ? null : $cle, 'page' => null]) }}"
           class="segment {{ $segmentActif === $cle ? 'is-active' : '' }}">
            {{ $libelle }}
            @isset($comptesSegments[$cle])
                <span class="segment-count">{{ number_format($comptesSegments[$cle], 0, ',', ' ') }}</span>
            @endisset
        </a>
    @endforeach
</div>

{{-- ---------- Recherche et filtres ---------- --}}
<form method="get" id="filtres-contacts" class="filter-card">
    @if ($segmentActif !== 'tous')
        <input type="hidden" name="segment" value="{{ $segmentActif }}">
    @endif

    <div class="filter-row">
        <div class="filter-search">
            @include('partials.icon', ['n' => 'search', 's' => 16, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:13px;top:11px'])
            <input type="text" name="recherche" value="{{ $filtres['recherche'] ?? '' }}"
                   placeholder="Nom, prénom, email ou téléphone" aria-label="Recherche">
        </div>

        <select name="id_formation" aria-label="Formation">
            <option value="">Formation : toutes</option>
            @foreach ($formations ?? [] as $formation)
                <option value="{{ $formation->id_formation }}" @selected(request('id_formation') == $formation->id_formation)>{{ $formation->niveau }}</option>
            @endforeach
        </select>

        <select name="etablissement" aria-label="Établissement candidaté">
            <option value="">Établissement candidaté : tous</option>
            @foreach ($etablissements ?? [] as $etablissement)
                <option value="{{ $etablissement->nom_etablissement }}" @selected(request('etablissement') === $etablissement->nom_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <button type="button" class="btn btn-ghost" data-toggle-advanced aria-expanded="false">
            @include('partials.icon', ['n' => 'levels', 's' => 15, 'w' => 2, 'style' => 'transform:rotate(90deg)'])<span>Plus de filtres</span>
        </button>
    </div>

    <div class="filter-advanced" hidden>
        <div class="filter-advanced-grid">
            <label>
                <span>Ville</span>
                <input type="text" name="ville" value="{{ request('ville') }}" placeholder="Toutes">
            </label>
            <label>
                <span>Code postal</span>
                <input type="text" name="code_postal" value="{{ request('code_postal') }}" placeholder="Tous">
            </label>
            <label>
                <span>Réunion d'information</span>
                <select name="id_reunion_information">
                    <option value="">Toutes</option>
                    @foreach ($reunions ?? [] as $reunion)
                        <option value="{{ $reunion->id_reunion_information }}" @selected(request('id_reunion_information') == $reunion->id_reunion_information)>
                            {{ $reunion->lieu }} — {{ $reunion->date?->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Devenu élève</span>
                <select name="est_eleve">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('est_eleve') === '1')>Oui</option>
                    <option value="0" @selected(request('est_eleve') === '0')>Non</option>
                </select>
            </label>
        </div>

        <div class="filter-toggles">
            @foreach (['agent_de_joueur' => 'Agents de joueur', 'salon' => 'Salons', 'newsletter' => 'Newsletter', 'offres_partenaires' => 'Offres partenaires', 'stop_relances' => 'Stop relances'] as $champ => $libelle)
                <label class="toggle-chip">
                    <input type="checkbox" name="{{ $champ }}" value="1" @checked($filtres[$champ] ?? request($champ))>
                    {{ $libelle }}
                </label>
            @endforeach
        </div>
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
        <a href="{{ route('contacts.index') }}" class="filter-reset">Réinitialiser</a>
        <button type="submit" class="btn">Rechercher</button>
    </div>
</form>

{{-- ---------- Tableau ---------- --}}
<div class="table-card">
    <div class="table-head">
        <span class="table-count" data-selection-idle>
            {{ $contacts->firstItem() }}–{{ $contacts->lastItem() }} sur {{ number_format($contacts->total(), 0, ',', ' ') }}
        </span>
        <div class="bulk-bar" data-selection-active hidden>
            <span class="bulk-count"><strong data-selection-count>0</strong> sélectionné(s)</span>
            <div class="bulk-actions">
                <button type="button">Envoyer un email</button>
                <button type="button">Créer une relance</button>
                <button type="button">Inscrire à une réunion</button>
                <button type="button" class="btn-danger">Stop relances</button>
            </div>
        </div>
    </div>

    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-check"><input type="checkbox" data-check-all aria-label="Tout sélectionner"></th>
                    @foreach ($colonnes as $colonne => $libelle)
                        @php $actif = request('tri') === $colonne; $sens = ($actif && request('sens') === 'asc') ? 'desc' : 'asc'; @endphp
                        <th class="{{ $actif ? 'is-sorted' : '' }}">
                            <a href="{{ request()->fullUrlWithQuery(['tri' => $colonne, 'sens' => $sens]) }}">
                                {{ $libelle }}
                                @include('partials.icon', ['n' => 'levels', 's' => 11, 'w' => 2.6, 'style' => 'transform:rotate(90deg)'])
                            </a>
                        </th>
                    @endforeach
                    <th>Opt-in</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contacts as $contact)
                    @php
                        $initiales = mb_strtoupper(mb_substr($contact->prenom ?? '?', 0, 1).mb_substr($contact->nom ?? '', 0, 1));
                        $profil = $contact->eleve?->profil;
                        [$statutBg, $statutInk, $statutLabel] = $couleursStatut[$profil] ?? ['#eef1f6', '#475569', 'Prospect'];
                        $ecoles = $contact->ecoles->pluck('etablissement')->join(', ');
                        $reunion = $contact->inscriptionsReunion->first()?->reunion;
                        $origine = $reunion ? "Réunion d'info" : ($contact->salon ? 'Salon' : ($contact->agent_de_joueur ? 'Partenaire' : 'Site web'));
                    @endphp
                    <tr>
                        <td class="col-check"><input type="checkbox" name="contacts[]" value="{{ $contact->getKey() }}" data-check-row aria-label="Sélectionner {{ $contact->nom_complet }}"></td>
                        <td>
                            <div class="cell-user">
                                <span class="cell-avatar">{{ $initiales }}</span>
                                <span class="cell-user-text">
                                    <a href="{{ route('contacts.show', $contact) }}" class="cell-name">{{ $contact->civilite }} {{ $contact->nom_complet }}</a>
                                    <span class="cell-sub">{{ $contact->email }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="num">{{ $contact->telephone ?: '—' }}</td>
                        <td class="nowrap">
                            <span>{{ $contact->ville ?: '—' }}</span>
                            <span class="cell-sub">{{ $contact->code_postal }}</span>
                        </td>
                        <td class="cell-wide">
                            <span>{{ $contact->formation?->niveau ?? '—' }}</span>
                            <span class="cell-sub">{{ $ecoles ?: '—' }}</span>
                        </td>
                        <td><span class="tag">{{ $origine }}</span></td>
                        <td><span class="pill" style="background:{{ $statutBg }};color:{{ $statutInk }}">{{ $statutLabel }}</span></td>
                        <td class="nowrap">
                            <span class="optin">
                                @foreach ($pastilles as $champ => [$titre, $fond, $encre, $ico])
                                    @if ($contact->{$champ})
                                        <span class="optin-dot" style="background:{{ $fond }}" title="{{ $titre }}">
                                            @include('partials.icon', ['n' => $ico, 's' => 13, 'c' => $encre, 'w' => 2])
                                        </span>
                                    @endif
                                @endforeach
                            </span>
                        </td>
                        <td class="col-actions">
                            <a href="{{ route('contacts.show', $contact) }}" class="row-btn" title="Voir la fiche">
                                @include('partials.icon', ['n' => 'eye', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <a href="{{ route('contacts.edit', $contact) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="empty-cell">
                            @include('partials.icon', ['n' => 'contact', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun contact ne correspond à ces filtres.</span>
                            <a href="{{ route('contacts.index') }}">Réinitialiser la recherche</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-foot">
        {{ $contacts->withQueryString()->links() }}
    </div>
</div>

<p class="legend">Pastilles : newsletter, offres partenaires, agent de joueur, salon, stop relances.</p>

<style>
    /* Base CRM : styles spécifiques (le reste vient de layouts/app.blade.php) */
    .title-row { display: flex; align-items: center; gap: 10px; }
    .title-row h1 { margin: 0; }

    .segments { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
    .segment {
        display: inline-flex; align-items: center; gap: 7px; padding: 7px 13px; border-radius: 999px;
        border: 1px solid var(--border); background: #fff; color: #585e72; font-size: 12.5px; font-weight: 600;
    }
    .segment:hover { border-color: #c3c6f5; color: var(--brand-deep); }
    .segment.is-active { border-color: #c3c6f5; background: var(--brand-light); color: var(--brand-deep); }
    .segment-count { padding: 1px 7px; border-radius: 999px; font-size: 11px; font-weight: 700; background: #f0f1f6; color: var(--muted); }
    .segment.is-active .segment-count { background: #fff; color: var(--brand-deep); }

    .filter-card { max-width: none; background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 14px; margin-bottom: 14px; }
    .filter-row { display: flex; gap: 9px; flex-wrap: wrap; align-items: center; }
    .filter-search { position: relative; flex: 1 1 300px; min-width: 0; }
    .filter-search input { width: 100%; max-width: none; padding-left: 38px; background: #fafbfd; }
    .filter-row select { flex: 0 1 200px; min-width: 150px; max-width: none; cursor: pointer; color: #585e72; }
    .filter-row .btn-ghost { flex-shrink: 0; }

    .filter-advanced { margin-top: 13px; padding-top: 13px; border-top: 1px solid var(--border-soft); }
    .filter-advanced-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 11px; }
    .filter-advanced label { margin: 0; min-width: 0; }
    .filter-advanced label > span { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .filter-advanced input, .filter-advanced select { width: 100%; max-width: none; }
    .filter-toggles { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
    .toggle-chip {
        display: inline-flex; align-items: center; gap: 7px; margin: 0; padding: 7px 12px; border-radius: 999px;
        border: 1px solid var(--border); background: #fff; font-size: 12.5px; font-weight: 600; color: #585e72; cursor: pointer;
    }
    .toggle-chip:hover { border-color: #c3c6f5; background: #fafbff; }
    .toggle-chip input { width: 14px; height: 14px; margin: 0; }

    .filter-foot { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 13px; padding-top: 13px; border-top: 1px solid var(--border-soft); }
    .filter-foot-label { font-size: 11.5px; font-weight: 600; color: var(--muted); }
    .chip {
        display: inline-flex; align-items: center; gap: 6px; padding: 5px 9px 5px 11px; border-radius: 999px;
        border: 1px solid #dfe2f4; background: #f7f8ff; color: var(--brand-deep); font-size: 12px; font-weight: 600;
    }
    .chip:hover { background: var(--brand-light); color: var(--brand-deep); }
    .filter-reset { margin-left: auto; padding: 7px 12px; border-radius: 9px; color: var(--muted); font-size: 12.5px; font-weight: 600; }
    .filter-reset:hover { background: #f4f5fa; color: var(--ink); }

    .table-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .table-head { display: flex; align-items: center; gap: 10px; padding: 12px 15px; border-bottom: 1px solid var(--border-soft); flex-wrap: wrap; min-height: 50px; }
    .table-count { font-size: 12.5px; color: var(--muted); }
    .bulk-bar { display: flex; align-items: center; gap: 10px; width: 100%; flex-wrap: wrap; }
    .bulk-count { font-size: 12.5px; color: var(--brand-deep); font-weight: 600; }
    .bulk-actions { display: flex; gap: 7px; margin-left: auto; flex-wrap: wrap; }

    .table-scroll { overflow-x: auto; }
    .data-table { min-width: 1120px; margin: 0; border: 0; border-radius: 0; box-shadow: none; }
    .data-table th { position: sticky; top: 0; z-index: 1; white-space: nowrap; }
    .data-table th a { display: inline-flex; align-items: center; gap: 5px; color: inherit; }
    .data-table th a:hover { color: var(--brand); }
    .data-table th svg { opacity: .35; }
    .data-table th.is-sorted a { color: var(--brand); }
    .data-table th.is-sorted svg { opacity: 1; }
    .data-table td { vertical-align: middle; }
    .nowrap { white-space: nowrap; }
    .col-check { width: 42px; }
    .col-check input { width: 15px; height: 15px; margin: 0; cursor: pointer; }
    .col-actions { width: 72px; text-align: right; white-space: nowrap; }
    .row-btn { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; border: 1px solid var(--border); margin-left: 4px; }
    .row-btn:hover { border-color: #c3c6f5; background: #fafbff; }

    .cell-user { display: flex; align-items: center; gap: 11px; }
    .cell-avatar { width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; background: var(--brand-light); color: var(--brand-deep); font-size: 11.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
    .cell-user-text { min-width: 0; }
    .cell-name { display: block; font-size: 13.5px; font-weight: 600; color: var(--ink); }
    .cell-name:hover { color: var(--brand); }
    .cell-sub { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
    .cell-wide { max-width: 230px; }
    .num { font-variant-numeric: tabular-nums; color: #585e72; white-space: nowrap; }

    .tag { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 6px; background: #f3f4f9; color: #475569; font-size: 11.5px; font-weight: 600; }
    .pill { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 600; }
    .optin { display: inline-flex; gap: 5px; }
    .optin-dot { width: 24px; height: 24px; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center; }
    tr:has(input[data-check-row]:checked) { background: #fafbff; }

    .empty-cell { text-align: center; padding: 42px 16px; }
    .empty-cell span { display: block; margin-top: 10px; font-size: 13px; color: var(--muted); }
    .empty-cell a { display: inline-block; margin-top: 8px; font-size: 12.5px; font-weight: 600; }

    .table-foot { padding: 13px 15px; }
    .table-foot .pagination { margin: 0; }
    .legend { margin: 14px 2px 0; font-size: 12px; color: var(--muted); }
</style>

<script>
    (function () {
        var toggle = document.querySelector('[data-toggle-advanced]');
        var advanced = document.querySelector('.filter-advanced');
        if (toggle && advanced) {
            var rempli = Array.prototype.some.call(advanced.querySelectorAll('input, select'), function (champ) {
                return champ.type === 'checkbox' ? champ.checked : champ.value !== '';
            });
            if (rempli) { advanced.hidden = false; toggle.querySelector('span').textContent = 'Moins de filtres'; toggle.setAttribute('aria-expanded', 'true'); }
            toggle.addEventListener('click', function () {
                advanced.hidden = !advanced.hidden;
                toggle.setAttribute('aria-expanded', advanced.hidden ? 'false' : 'true');
                toggle.querySelector('span').textContent = advanced.hidden ? 'Plus de filtres' : 'Moins de filtres';
            });
        }

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
            if (all) { all.checked = n === rows.length; all.indeterminate = n > 0 && n < rows.length; }
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
