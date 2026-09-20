@extends('layouts.app')

@section('title', 'Mon espace')

@section('content')
<div class="page-head">
    <div>
        <h1>Bonjour {{ $intervenant?->prenom ?: '' }}</h1>
        <p class="page-sub">Vos cours de la semaine, vos classes et votre récapitulatif d'heures.</p>
    </div>
</div>

<div class="ho-grid">
    <section class="ho-card ho-next">
        <span class="ho-label">Prochain cours</span>
        @if ($prochainCours)
            <h2>{{ $prochainCours->cours?->nom_cours ?: 'Cours' }}</h2>
            <p class="ho-quand">{{ ucfirst($prochainCours->date_debut->translatedFormat('l j F')) }} · {{ $prochainCours->date_debut->format('H:i') }} – {{ $prochainCours->date_fin->format('H:i') }}</p>
            <div class="ho-meta">
                @if ($prochainCours->classe?->classe)
                    <span>@include('partials.icon', ['n' => 'users', 's' => 14, 'c' => '#585e72', 'w' => 2]){{ $prochainCours->classe->classe }}</span>
                @endif
                @if ($prochainCours->salle?->nom_salle)
                    <span>@include('partials.icon', ['n' => 'door', 's' => 14, 'c' => '#585e72', 'w' => 2]){{ $prochainCours->salle->nom_salle }}</span>
                @endif
            </div>
        @else
            <h2 class="ho-rien">Aucun cours à venir</h2>
            <p class="ho-quand">Rien n'est planifié pour l'instant.</p>
        @endif
        <a href="{{ route('espace-intervenant.planning') }}" class="ho-lien">
            Voir mon emploi du temps @include('partials.icon', ['n' => 'arrow-right', 's' => 14, 'c' => 'var(--brand)', 'w' => 2])
        </a>
    </section>

    <section class="ho-card ho-chiffre">
        <span class="ho-label">Cette semaine</span>
        <strong>{{ $coursSemaine }}</strong>
        <span class="ho-unite">cours planifiés</span>
    </section>

    <section class="ho-card ho-chiffre">
        <span class="ho-label">Mes classes</span>
        <strong>{{ $nbClasses }}</strong>
        <span class="ho-unite">classes suivies</span>
        <a href="{{ route('espace-intervenant.classes') }}" class="ho-lien">
            Voir mes classes @include('partials.icon', ['n' => 'arrow-right', 's' => 14, 'c' => 'var(--brand)', 'w' => 2])
        </a>
    </section>

    @if (Route::has('recapitulatif.index'))
        <section class="ho-card">
            <span class="ho-label">Heures effectuées</span>
            <h2>Récapitulatif</h2>
            <p class="ho-quand">Déclarez et consultez vos heures.</p>
            <a href="{{ route('recapitulatif.index') }}" class="ho-lien">
                Ouvrir le récapitulatif @include('partials.icon', ['n' => 'arrow-right', 's' => 14, 'c' => 'var(--brand)', 'w' => 2])
            </a>
        </section>
    @endif
</div>

@endsection
