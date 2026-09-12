@php
    // Menu admin : groupes repliables (<details>) + filtre client. Mêmes routes
    // qu'avant, icônes SVG (partials.icon) à la place des emoji.
    $navGroups = [
        ['Élèves & candidats', [
            ['eleves.index', 'eleves.*', 'users', 'Élèves'],
            ['candidats.index', 'candidats.*', 'file', 'Candidats'],
            ['epreuves.index', 'epreuves.*', 'calc', "Épreuves d'admission"],
        ]],
        ['CRM', [
            ['contacts.index', 'contacts.*', 'contact', 'Contacts'],
            ['entreprises.index', 'entreprises.*', 'building', 'Entreprises'],
            ['taches.index', 'taches.*', 'check', 'Tâches / relances'],
            ['recherche.search', 'recherche.*', 'search', 'Recherche'],
            ['archives.reunions', 'archives.*', 'archive', 'Archives réunions'],
            ['import.index', 'import.*', 'inbox', 'Import contacts'],
        ]],
        ['Pédagogie', [
            ['referentiel.niveaux.index', 'referentiel.niveaux.*', 'levels', 'Niveaux'],
            ['referentiel.unites.index', 'referentiel.unites.*', 'book', "Unités d'enseignement"],
            ['referentiel.cours.index', 'referentiel.cours.*', 'book', 'Cours'],
            ['referentiel.matieres.index', 'referentiel.matieres.*', 'calc', 'Matières'],
            ['referentiel.classes.index', 'referentiel.classes.*', 'tag', 'Classes'],
            ['referentiel.groupes.index', 'referentiel.groupes.*', 'group', 'Groupes'],
            ['referentiel.periodes-formation.index', 'referentiel.periodes-formation.*', 'cal', 'Périodes de formation'],
            ['referentiel.ref.index', 'referentiel.ref.*', 'clock', 'Référentiel des heures'],
            ['referentiel.parametrage.index', 'referentiel.parametrage.*', 'chart', 'Volumes de formation'],
        ]],
        ['Notation & bulletins', [
            ['evaluations.index', 'evaluations.*', 'pencil', 'Évaluations & notes'],
            ['types-evaluation.index', 'types-evaluation.*', 'tag', "Types d'évaluation"],
            ['bulletins.index', 'bulletins.index', 'file', 'Bulletins (décisions)'],
            ['bulletin-v2.index', 'bulletin-v2.*', 'printer', 'Bulletins PDF'],
        ]],
        ['Établissements & RH', [
            ['referentiel.etablissements.index', 'referentiel.etablissements.*', 'school', 'Établissements'],
            ['referentiel.intervenants.index', 'referentiel.intervenants.*', 'school', 'Intervenants'],
            ['referentiel.signatures.index', 'referentiel.signatures.*', 'pen', 'Signatures'],
            ['salles.index', 'salles.*', 'door', 'Salles'],
            ['planning.index', 'planning.*', 'cal', 'Planning'],
            ['panneaux.index', 'panneaux.*', 'bulb', 'Panneaux lumineux'],
        ]],
        ['Administration', [
            ['admins.index', 'admins.*', 'key', 'Administrateurs'],
            ['roles.index', 'roles.*', 'shield', 'Rôles'],
            ['permissions.index', 'permissions.*', 'lock', 'Permissions'],
            ['emails.index', 'emails.*', 'mail', "Modèles d'emails"],
            ['configuration.site', 'configuration.*', 'gear', 'Config. du site'],
            ['notifications.index', 'notifications.*', 'bell', 'Notifications'],
        ]],
    ];
@endphp

<aside class="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="brand">
        <span class="brand-mark">@include('partials.icon', ['n' => 'cap', 's' => 17, 'c' => '#fff', 'w' => 2])</span>
        <span class="brand-text">
            <span class="brand-name">{{ config('app.name') }}</span>
            <span class="brand-sub">School Tech</span>
        </span>
    </a>

    <div class="nav-search">
        @include('partials.icon', ['n' => 'search', 's' => 14, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:10px;top:9px'])
        <input type="text" id="nav-filter" placeholder="Filtrer le menu" autocomplete="off" aria-label="Filtrer le menu">
    </div>

    <nav id="admin-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-link @if(request()->routeIs('admin.dashboard'))active @endif">
            @include('partials.icon', ['n' => 'home', 's' => 16])Tableau de bord
        </a>

        @foreach ($navGroups as [$groupTitle, $items])
            @php $groupActive = collect($items)->contains(fn ($i) => request()->routeIs($i[1])); @endphp
            <details class="nav-group" @if($groupActive || $loop->first) open @endif>
                <summary>
                    {{ $groupTitle }}
                    @include('partials.icon', ['n' => 'chevron-down', 's' => 13, 'w' => 2.5, 'style' => 'transition:transform .15s ease'])
                </summary>
                <div class="nav-items">
                    @foreach ($items as [$routeName, $pattern, $ico, $label])
                        <a href="{{ route($routeName) }}" class="nav-link @if(request()->routeIs($pattern))active @endif">
                            @include('partials.icon', ['n' => $ico, 's' => 15, 'c' => '#9aa0b0']){{ $label }}
                        </a>
                    @endforeach
                </div>
            </details>
        @endforeach
    </nav>
</aside>
