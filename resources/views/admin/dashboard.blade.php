@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
@php
    // Raccourcis regroupés par domaine (même contenu qu'avant, ordonné et teinté par section).
    $sections = [
        ['Élèves & candidats', 'indigo', [
            ['eleves.index', 'users', 'Élèves', 'Fiches, profils, statuts'],
            ['candidats.index', 'file', 'Candidats', 'Admissions et résultats'],
            ['epreuves.index', 'calc', "Épreuves d'admission", 'Sessions et convocations'],
        ]],
        ['CRM', 'teal', [
            ['contacts.index', 'contact', 'Contacts', 'Prospects et leads'],
            ['entreprises.index', 'building', 'Entreprises', 'Partenaires, contacts pro'],
            ['taches.index', 'check', 'Tâches / relances', 'Suivi commercial'],
            ['import.index', 'inbox', 'Import contacts', 'Fichiers CSV'],
        ]],
        ['Pédagogie', 'amber', [
            ['planning.index', 'cal', 'Planning', 'Créneaux de cours'],
            ['referentiel.niveaux.index', 'book', 'Niveaux & cours', 'UE, cours, classes'],
            ['referentiel.ref.index', 'clock', "Référentiel des heures", 'Volumes par niveau'],
            ['referentiel.groupes.index', 'group', 'Groupes', 'Répartition des élèves'],
        ]],
        ['Notation & bulletins', 'rose', [
            ['evaluations.index', 'pencil', 'Évaluations & notes', 'Saisie des notes'],
            ['bulletin-v2.index', 'printer', 'Bulletins PDF', 'Génération et envoi'],
            ['bulletins.index', 'file', 'Bulletins (décisions)', 'Jurys et mentions'],
        ]],
        ['Établissements & RH', 'violet', [
            ['referentiel.intervenants.index', 'school', 'Intervenants', 'Fiches, compétences, cours'],
            ['referentiel.etablissements.index', 'building', 'Établissements', 'Campus et sites'],
            ['salles.index', 'door', 'Salles', 'Capacités, équipements'],
        ]],
        ['Administration', 'slate', [
            ['admins.index', 'key', 'Administrateurs', 'Comptes et rôles'],
            ['configuration.site', 'gear', 'Config. du site', 'Paramètres généraux'],
            ['emails.index', 'mail', "Modèles d'emails", 'Contenus automatiques'],
        ]],
    ];
    $portals = [
        ['eleve.login', 'cap', 'Espace élève', 'Planning, notes, documents'],
        ['intervenant.login', 'school', 'Espace intervenant', 'Classes, émargement, saisie'],
        ['entreprise.login', 'building', 'Espace entreprise', 'Alternants, conventions'],
    ];
@endphp

<div class="page-head">
    <div>
        <div class="eyebrow">Espace de gestion</div>
        <h1>Bienvenue, {{ auth('admin')->user()->prenom }}</h1>
        <p class="page-sub">Accès rapide aux modules, regroupés par domaine.</p>
    </div>
    <div class="page-actions">
        @if (Route::has('exports.index'))
            <a href="{{ route('exports.index') }}" class="btn btn-ghost">
                @include('partials.icon', ['n' => 'download', 's' => 15, 'w' => 2])Exports
            </a>
        @endif
        <a href="{{ route('eleves.create') }}" class="btn">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouvel élève
        </a>
    </div>
</div>

@foreach ($sections as [$title, $tint, $cards])
    <section class="dash-section">
        <div class="section-head">
            <h2>{{ $title }}</h2>
            <span class="rule"></span>
            <span class="section-count">{{ count($cards) }} {{ count($cards) > 1 ? 'modules' : 'module' }}</span>
        </div>
        <div class="dash-grid">
            @foreach ($cards as [$routeName, $ico, $label, $desc])
                <a href="{{ route($routeName) }}" class="dash-box">
                    <span class="dash-icon tint-{{ $tint }}">@include('partials.icon', ['n' => $ico, 's' => 18])</span>
                    <span class="dash-text">
                        <span class="dash-label">{{ $label }}</span>
                        <span class="dash-desc">{{ $desc }}</span>
                    </span>
                    @include('partials.icon', ['n' => 'chevron-right', 's' => 15, 'c' => '#c9cdd9', 'w' => 2, 'style' => 'margin-left:auto'])
                </a>
            @endforeach
        </div>
    </section>
@endforeach

<section class="dash-section" style="margin-top:34px">
    <div class="section-head">
        <h2>Accès aux autres espaces</h2>
        <span class="rule"></span>
    </div>
    <div class="dash-portals">
        @foreach ($portals as [$routeName, $ico, $label, $desc])
            <a href="{{ route($routeName) }}" class="dash-portal" target="_blank" rel="noopener">
                <span class="portal-icon">@include('partials.icon', ['n' => $ico, 's' => 18, 'c' => '#4f46e5'])</span>
                <span class="dash-text">
                    <span class="dash-label">{{ $label }}</span>
                    <span class="dash-desc">{{ $desc }}</span>
                </span>
                @include('partials.icon', ['n' => 'external', 's' => 14, 'c' => '#b9bdcc', 'w' => 2, 'style' => 'margin-left:auto'])
            </a>
        @endforeach
    </div>
</section>
@endsection
