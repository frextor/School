@php
    // Menu admin : groupes repliables (<details>) + filtre client.
    //
    // Orientation K-12 marocain (Maternelle / Primaire / Collège / Lycée) :
    // les écrans hérités de l'enseignement supérieur (candidats, épreuves
    // d'admission, entreprises/alternance) ne sont plus listés ici. Leurs
    // routes et contrôleurs restent en place et fonctionnels — il suffit de
    // remettre la ligne correspondante pour les réactiver.
    $navGroups = [
        ['Scolarité', [
            ['eleves.index', 'eleves.*', 'users', 'Élèves'],
            ['referentiel.classes.index', 'referentiel.classes.*', 'tag', 'Classes'],
            ['referentiel.niveaux.index', 'referentiel.niveaux.*', 'levels', 'Niveaux & cycles'],
            ['referentiel.groupes.index', 'referentiel.groupes.*', 'group', 'Groupes'],
        ]],
        ['Inscriptions & familles', [
            ['contacts.index', 'contacts.*', 'contact', 'Familles / prospects'],
            ['taches.index', 'taches.*', 'check', 'Tâches / relances'],
            ['recherche.search', 'recherche.*', 'search', 'Recherche'],
            ['import.index', 'import.*', 'inbox', 'Import de contacts'],
        ]],
        ['Pédagogie', [
            ['referentiel.matieres.index', 'referentiel.matieres.*', 'calc', 'Matières'],
            ['referentiel.cours.index', 'referentiel.cours.*', 'book', 'Cours'],
            ['referentiel.unites.index', 'referentiel.unites.*', 'book', "Unités d'enseignement"],
            ['referentiel.periodes-formation.index', 'referentiel.periodes-formation.*', 'cal', 'Périodes scolaires'],
            ['referentiel.ref.index', 'referentiel.ref.*', 'clock', 'Référentiel des heures'],
            ['referentiel.parametrage.index', 'referentiel.parametrage.*', 'chart', 'Volumes horaires'],
        ]],
        ['Notation & bulletins', [
            ['evaluations.index', 'evaluations.*', 'pencil', 'Évaluations & notes'],
            ['types-evaluation.index', 'types-evaluation.*', 'tag', "Types d'évaluation"],
            ['bulletins.index', 'bulletins.index', 'file', 'Bulletins (décisions)'],
            ['bulletin-v2.index', 'bulletin-v2.*', 'printer', 'Bulletins PDF'],
        ]],
        ['Établissements & personnel', [
            ['referentiel.etablissements.index', 'referentiel.etablissements.*', 'school', 'Établissements'],
            ['referentiel.intervenants.index', 'referentiel.intervenants.*', 'school', 'Enseignants'],
            ['referentiel.signatures.index', 'referentiel.signatures.*', 'pen', 'Signatures'],
            ['salles.index', 'salles.*', 'door', 'Salles'],
            ['planning.index', 'planning.*', 'cal', 'Emploi du temps'],
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
