@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <h1>Bienvenue, {{ auth('admin')->user()->prenom }} 👋</h1>
    <p style="color:var(--muted);margin-top:-0.75rem">Voici un accès rapide aux modules les plus utilisés.</p>

    <div class="dash-grid">
        @foreach ([
            ['route' => 'eleves.index', 'icon' => '🧑‍🎓', 'eyebrow' => 'Pédagogie', 'label' => 'Élèves', 'desc' => 'Fiches, profils, statuts'],
            ['route' => 'candidats.index', 'icon' => '📝', 'eyebrow' => 'Pédagogie', 'label' => 'Candidats', 'desc' => 'Épreuves d\'admission, résultats'],
            ['route' => 'planning.index', 'icon' => '📅', 'eyebrow' => 'Pédagogie', 'label' => 'Planning', 'desc' => 'Créneaux de cours'],
            ['route' => 'contacts.index', 'icon' => '📇', 'eyebrow' => 'CRM', 'label' => 'Contacts', 'desc' => 'Prospects, leads'],
            ['route' => 'entreprises.index', 'icon' => '🏢', 'eyebrow' => 'CRM', 'label' => 'Entreprises', 'desc' => 'Partenaires, contacts pro'],
            ['route' => 'taches.index', 'icon' => '✅', 'eyebrow' => 'CRM', 'label' => 'Tâches / Relances', 'desc' => 'Suivi commercial'],
            ['route' => 'evaluations.index', 'icon' => '✏️', 'eyebrow' => 'Notation', 'label' => 'Évaluations & notes', 'desc' => 'Saisie des notes'],
            ['route' => 'bulletin-v2.index', 'icon' => '🖨️', 'eyebrow' => 'Notation', 'label' => 'Bulletins PDF', 'desc' => 'Génération de bulletins'],
            ['route' => 'referentiel.niveaux.index', 'icon' => '📚', 'eyebrow' => 'Référentiel', 'label' => 'Niveaux & cours', 'desc' => 'UE, cours, classes'],
            ['route' => 'referentiel.ref.index', 'icon' => '⏱️', 'eyebrow' => 'Référentiel', 'label' => 'Heures d\'enseignement', 'desc' => 'Volumes par niveau/classe'],
            ['route' => 'referentiel.intervenants.index', 'icon' => '👩‍🏫', 'eyebrow' => 'RH', 'label' => 'Intervenants', 'desc' => 'Fiches, compétences, cours'],
            ['route' => 'admins.index', 'icon' => '🔑', 'eyebrow' => 'Administration', 'label' => 'Administrateurs', 'desc' => 'Comptes et rôles'],
        ] as $card)
            <a href="{{ route($card['route']) }}" class="dash-box">
                <span class="dash-icon">{{ $card['icon'] }}</span>
                <span>
                    <span class="dash-eyebrow">{{ $card['eyebrow'] }}</span>
                    <span class="dash-label">{{ $card['label'] }}</span>
                    <span class="dash-desc">{{ $card['desc'] }}</span>
                </span>
            </a>
        @endforeach
    </div>

    <h2>Accès aux autres espaces</h2>
    <div class="dash-portals">
        <a href="{{ route('eleve.login') }}" class="dash-portal" target="_blank">
            <span class="dash-portal-icon">🧑‍🎓</span>
            <span class="dash-portal-label">Espace élève</span>
        </a>
        <a href="{{ route('intervenant.login') }}" class="dash-portal" target="_blank">
            <span class="dash-portal-icon">👩‍🏫</span>
            <span class="dash-portal-label">Espace intervenant</span>
        </a>
        <a href="{{ route('entreprise.login') }}" class="dash-portal" target="_blank">
            <span class="dash-portal-icon">🤝</span>
            <span class="dash-portal-label">Espace entreprise</span>
        </a>
    </div>
@endsection
