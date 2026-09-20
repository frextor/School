@extends('layouts.app')

@section('title', 'Mon espace')

@section('content')
@php
    $contact = $eleve?->contact;
    $prenom = $contact?->prenom ?: 'à vous';
@endphp

<div class="page-head">
    <div>
        <h1>Bonjour {{ $prenom }}</h1>
        <p class="page-sub">
            {{ $eleve?->classe?->classe ? 'Classe '.$eleve->classe->classe : 'Élève' }}
            @if ($eleve?->niveau?->nom_niveau) · {{ $eleve->niveau->nom_niveau }} @endif
        </p>
    </div>
</div>

<div class="ho-grid">
    {{-- Prochain cours : la question que l'on se pose en ouvrant l'espace. --}}
    <section class="ho-card ho-next">
        <span class="ho-label">Prochain cours</span>
        @if ($prochainCours)
            <h2>{{ $prochainCours->cours?->nom_cours ?: 'Cours' }}</h2>
            <p class="ho-quand">{{ ucfirst($prochainCours->date_debut->translatedFormat('l j F')) }} · {{ $prochainCours->date_debut->format('H:i') }} – {{ $prochainCours->date_fin->format('H:i') }}</p>
            <div class="ho-meta">
                @if ($prochainCours->salle?->nom_salle)
                    <span>@include('partials.icon', ['n' => 'door', 's' => 14, 'c' => '#585e72', 'w' => 2]){{ $prochainCours->salle->nom_salle }}</span>
                @endif
                @if ($prochainCours->intervenant)
                    <span>@include('partials.icon', ['n' => 'school', 's' => 14, 'c' => '#585e72', 'w' => 2]){{ trim($prochainCours->intervenant->nom.' '.$prochainCours->intervenant->prenom) }}</span>
                @endif
            </div>
        @else
            <h2 class="ho-rien">Aucun cours à venir</h2>
            <p class="ho-quand">Rien n'est planifié pour l'instant.</p>
        @endif
        <a href="{{ route('espace-eleve.planning') }}" class="ho-lien">
            Voir mon emploi du temps @include('partials.icon', ['n' => 'arrow-right', 's' => 14, 'c' => 'var(--brand)', 'w' => 2])
        </a>
    </section>

    <section class="ho-card ho-chiffre">
        <span class="ho-label">Cette semaine</span>
        <strong>{{ $coursSemaine }}</strong>
        <span class="ho-unite">cours planifiés</span>
    </section>

    {{-- Dernières notes publiées. --}}
    <section class="ho-card ho-notes">
        <span class="ho-label">Mes dernières notes</span>

        @forelse ($dernieresNotes as $note)
            @php $valeur = is_numeric($note->note) ? (float) $note->note : null; @endphp
            <div class="ho-note">
                <span class="ho-note-m">{{ $note->evaluation?->matiere?->nom_cours ?: 'Matière' }}</span>
                <span class="ho-note-v @if ($valeur !== null && $valeur < 10) is-low @endif">
                    {{ $valeur !== null ? number_format($valeur, 2, ',', ' ').' / 20' : '—' }}
                </span>
            </div>
        @empty
            <p class="ho-vide">Aucune note publiée pour le moment.</p>
        @endforelse

        @if ($dernieresNotes->isNotEmpty())
            <a href="{{ route('espace-eleve.evaluations') }}" class="ho-lien">
                Toutes mes notes @include('partials.icon', ['n' => 'arrow-right', 's' => 14, 'c' => 'var(--brand)', 'w' => 2])
            </a>
        @endif
    </section>

    <section class="ho-card">
        <span class="ho-label">Mon dernier bulletin</span>
        @if ($dernierBulletin)
            <h2>{{ $dernierBulletin->annee }}-{{ $dernierBulletin->annee + 1 }}</h2>
            <p class="ho-quand">
                {{ $dernierBulletin->semestre ? 'Semestre '.$dernierBulletin->semestre : 'Année complète' }}
                @if ($dernierBulletin->date_insert) · édité le {{ $dernierBulletin->date_insert->format('d/m/Y') }} @endif
            </p>
            <a href="{{ route('espace-eleve.bulletins.download', $dernierBulletin) }}" target="_blank" class="ho-lien">
                Ouvrir le PDF @include('partials.icon', ['n' => 'download', 's' => 14, 'c' => 'var(--brand)', 'w' => 2])
            </a>
        @else
            <h2 class="ho-rien">Aucun bulletin</h2>
            <p class="ho-quand">Vos bulletins apparaîtront ici dès qu'ils seront édités.</p>
        @endif
    </section>
</div>

@endsection
