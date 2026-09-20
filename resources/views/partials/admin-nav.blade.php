@php
    // Menu admin : groupes repliables (<details>) + filtre client.
    //
    // Orientation K-12 marocain (Maternelle / Primaire / Collège / Lycée).
    // Écartés du menu, routes et contrôleurs conservés : unités d'enseignement
    // et `amos_matiere` (découpages du supérieur, la notation K-12 passe par
    // Cours), entreprises/alternance, archives de réunions, spécialisations,
    // types de pièce entreprise. Une ligne à remettre ici pour réactiver.
    $navGroups = [
        // Un groupe = un domaine métier, avec son responsable dans l'école :
        // la vie scolaire au quotidien, la pédagogie, la notation, la
        // facturation, le recrutement des familles, puis les ressources
        // et l'administration.
        ['Vie scolaire', [
            ['eleves.index', 'eleves.*', 'users', 'Élèves'],
            ['absences.appel', 'absences.appel', 'check-simple', "Faire l'appel"],
            ['absences.index', 'absences.index', 'check', 'Absences & retards'],
            ['referentiel.groupes.index', 'referentiel.groupes.*', 'group', "Groupes d'élèves"],
        ]],
        ['Pédagogie', [
            ['referentiel.classes.index', 'referentiel.classes.*', 'tag', 'Classes'],
            ['referentiel.niveaux.index', 'referentiel.niveaux.*', 'levels', 'Niveaux & cycles'],
            // `Cours` est la table que la notation, les coefficients, les bulletins et
            // l'emploi du temps utilisent : c'est bien elle qui porte les matières.
            // (`amos_matiere` est un découpage d'UE hérité du supérieur, sans usage en K-12.)
            ['referentiel.cours.index', 'referentiel.cours.*', 'book', 'Matières'],
            ['planning.index', 'planning.*', 'cal', 'Emploi du temps'],
            ['referentiel.periodes-formation.index', 'referentiel.periodes-formation.*', 'cal', 'Périodes scolaires'],
            ['referentiel.ref.index', 'referentiel.ref.*', 'clock', 'Heures par matière'],
            ['referentiel.parametrage.index', 'referentiel.parametrage.*', 'chart', 'Volumes horaires'],
        ]],
        ['Notation & bulletins', [
            ['evaluations.index', 'evaluations.*', 'pencil', 'Évaluations & notes'],
            ['bulletin-v2.index', 'bulletin-v2.*', 'printer', 'Bulletins de notes'],
            ['bulletins.index', 'bulletins.index', 'file', 'Conseils de classe & décisions'],
            ['types-evaluation.index', 'types-evaluation.*', 'tag', "Types d'évaluation"],
        ]],
        ['Facturation', [
            ['echeances.index', 'echeances.index', 'card', 'Échéanciers & impayés'],
            ['echeances.generer', 'echeances.generer', 'plus', 'Générer un échéancier'],
        ]],
        ['Admissions & CRM', [
            ['contacts.index', 'contacts.*', 'contact', 'Familles & prospects'],
            ['candidats.index', 'candidats.*', 'file', 'Candidats'],
            ['epreuves.index', 'epreuves.*', 'calc', "Épreuves d'admission"],
            ['taches.index', 'taches.*', 'check', 'Tâches & relances'],
            ['import.index', 'import.*', 'inbox', 'Import de familles'],
            ['recherche.search', 'recherche.*', 'search', 'Recherche'],
        ]],
        ['Personnel & locaux', [
            ['referentiel.intervenants.index', 'referentiel.intervenants.*', 'school', 'Enseignants'],
            ['salles.index', 'salles.*', 'door', 'Salles'],
            ['referentiel.etablissements.index', 'referentiel.etablissements.*', 'building', 'Établissements'],
            ['referentiel.signatures.index', 'referentiel.signatures.*', 'pen', 'Signatures'],
            ['panneaux.index', 'panneaux.*', 'bulb', "Panneaux d'affichage"],
        ]],
        ['Administration', [
            ['admins.index', 'admins.*', 'key', 'Administrateurs'],
            ['roles.index', 'roles.*', 'shield', 'Rôles'],
            ['permissions.index', 'permissions.*', 'lock', 'Permissions'],
            ['emails.index', 'emails.*', 'mail', "Modèles d'emails"],
            ['notifications.index', 'notifications.*', 'bell', 'Notifications'],
            ['configuration.site', 'configuration.*', 'gear', "Paramètres de l'école"],
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
