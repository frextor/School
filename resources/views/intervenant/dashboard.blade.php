@extends('layouts.app')

@section('title', 'Mon espace')

@section('content')
@php
    $initiales = mb_strtoupper(mb_substr($intervenant?->prenom ?: 'E', 0, 1).mb_substr($intervenant?->nom ?: '', 0, 1));
    $effectif = $classes->sum('eleves_count');
@endphp

<section class="he">
    <div class="he-id">
        <span class="he-avatar">{{ $initiales }}</span>
        <span class="he-txt">
            <span class="he-hello">Bonjour {{ $intervenant?->prenom }}</span>
            <span class="he-sub">{{ $classes->count() }} classe{{ $classes->count() > 1 ? 's' : '' }} · {{ $effectif }} élève{{ $effectif > 1 ? 's' : '' }}</span>
        </span>
    </div>

    <div class="he-stats">
        <div class="he-stat">
            <strong>{{ $coursSemaine }}</strong>
            <span>cours cette semaine</span>
        </div>
        <div class="he-stat">
            <strong>{{ $classes->count() }}</strong>
            <span>classe{{ $classes->count() > 1 ? 's' : '' }}</span>
        </div>
        <div class="he-stat">
            <strong>{{ $effectif }}</strong>
            <span>élèves suivis</span>
        </div>
    </div>
</section>

<div class="ho-grid">
    <section class="ho-card ho-large">
        <div class="ho-tete">
            <span class="ho-label">{{ $coursPasses ? 'Mes derniers cours' : 'Mes prochains cours' }}</span>
            <a href="{{ route('espace-intervenant.planning') }}" class="ho-tete-lien">
                Emploi du temps @include('partials.icon', ['n' => 'arrow-right', 's' => 13, 'c' => 'var(--brand)', 'w' => 2])
            </a>
        </div>

        @forelse ($prochainsCours as $seance)
            <div class="ho-cours">
                <span class="ho-cours-h">
                    <span class="ho-cours-jour">{{ ucfirst($seance->date_debut->translatedFormat('D j')) }}</span>
                    <span class="ho-cours-heure">{{ $seance->date_debut->format('H:i') }}</span>
                </span>
                <span class="ho-cours-txt">
                    <span class="ho-cours-nom">{{ $seance->cours?->nom_cours ?: 'Cours' }}</span>
                    <span class="ho-cours-meta">
                        {{ collect([
                            $seance->classe?->classe,
                            $seance->salle?->nom_salle ? 'Salle '.$seance->salle->nom_salle : null,
                        ])->filter()->implode(' · ') ?: '—' }}
                    </span>
                </span>
                <span class="ho-cours-duree">{{ $seance->date_debut->diffInMinutes($seance->date_fin) }} min</span>
            </div>
        @empty
            <p class="ho-vide">Aucun cours ne vous est affecté.</p>
        @endforelse

        @if ($coursPasses && $prochainsCours->isNotEmpty())
            <p class="ho-note-bas">Aucun cours à venir : voici vos dernières séances.</p>
        @endif
    </section>

    <section class="ho-card ho-large">
        <div class="ho-tete">
            <span class="ho-label">Mes classes</span>
            <a href="{{ route('espace-intervenant.classes') }}" class="ho-tete-lien">
                Tout voir @include('partials.icon', ['n' => 'arrow-right', 's' => 13, 'c' => 'var(--brand)', 'w' => 2])
            </a>
        </div>

        @forelse ($classes->take(5) as $classe)
            <a href="{{ route('espace-intervenant.roster', $classe) }}" class="ho-classe">
                <span class="ho-pastille" style="background: {{ $classe->couleur ?: '#4f46e5' }}"></span>
                <span class="ho-classe-txt">
                    <span class="ho-classe-nom">{{ $classe->classe }}</span>
                    <span class="ho-classe-meta">{{ $classe->niveau?->nom_niveau ?: '—' }}</span>
                </span>
                <span class="ho-classe-eff">{{ $classe->eleves_count }} élèves</span>
            </a>
        @empty
            <p class="ho-vide">Aucune classe rattachée à vos créneaux.</p>
        @endforelse
    </section>

    <section class="ho-card ho-full ho-bulletin">
        <span class="ho-icone">@include('partials.icon', ['n' => 'clock', 's' => 22, 'c' => 'var(--brand-deep)', 'w' => 1.8])</span>
        <span class="ho-bul-txt">
            <span class="ho-label">Récapitulatif d'heures</span>
            <span class="ho-bul-titre">Déclarer mes heures effectuées</span>
            <span class="ho-bul-meta">Date, classe, nature de l'heure et volume horaire.</span>
        </span>
        @if (Route::has('recapitulatif.index'))
            <a href="{{ route('recapitulatif.index') }}" class="btn">Ouvrir le récapitulatif</a>
        @endif
    </section>
</div>
@endsection
