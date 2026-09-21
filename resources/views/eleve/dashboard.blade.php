@extends('layouts.app')

@section('title', 'Mon espace')

@section('content')
@php
    $contact = $eleve?->contact;
    $prenom = $contact?->prenom ?: '';
    $initiales = mb_strtoupper(mb_substr($contact?->prenom ?: 'E', 0, 1).mb_substr($contact?->nom ?: '', 0, 1));
    $note20 = fn ($v) => $v === null ? '—' : number_format((float) $v, 2, ',', ' ');
@endphp

{{-- Bandeau d'identité : ancre l'écran et porte les chiffres clés, au lieu
     de trois cartes blanches à moitié vides. --}}
<section class="he">
    <div class="he-id">
        <span class="he-avatar">{{ $initiales }}</span>
        <span class="he-txt">
            <span class="he-hello">Bonjour {{ $prenom }}</span>
            <span class="he-sub">
                {{ $eleve?->classe?->classe ? 'Classe '.$eleve->classe->classe : 'Élève' }}
                @if ($eleve?->niveau?->nom_niveau) · {{ $eleve->niveau->nom_niveau }} @endif
            </span>
        </span>
    </div>

    <div class="he-stats">
        <div class="he-stat">
            <strong>{{ $coursSemaine }}</strong>
            <span>cours cette semaine</span>
        </div>
        <div class="he-stat">
            <strong>{{ $note20($moyenne) }}</strong>
            <span>moyenne sur 20</span>
        </div>
        <div class="he-stat">
            <strong>{{ $nbBulletins }}</strong>
            <span>bulletin{{ $nbBulletins > 1 ? 's' : '' }}</span>
        </div>
    </div>
</section>

<div class="ho-grid">
    <section class="ho-card ho-large">
        <div class="ho-tete">
            <span class="ho-label">{{ $coursPasses ? 'Mes derniers cours' : 'Mes prochains cours' }}</span>
            <a href="{{ route('espace-eleve.planning') }}" class="ho-tete-lien">
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
                            $seance->salle?->nom_salle ? 'Salle '.$seance->salle->nom_salle : null,
                            trim(($seance->intervenant?->nom ?? '').' '.($seance->intervenant?->prenom ?? '')) ?: null,
                        ])->filter()->implode(' · ') ?: '—' }}
                    </span>
                </span>
                <span class="ho-cours-duree">{{ $seance->date_debut->diffInMinutes($seance->date_fin) }} min</span>
            </div>
        @empty
            <p class="ho-vide">Aucun cours n'est planifié pour votre classe.</p>
        @endforelse

        @if ($coursPasses && $prochainsCours->isNotEmpty())
            <p class="ho-note-bas">Aucun cours à venir : voici vos dernières séances.</p>
        @endif
    </section>

    <section class="ho-card ho-large">
        <div class="ho-tete">
            <span class="ho-label">Mes dernières notes</span>
            @if ($dernieresNotes->isNotEmpty())
                <a href="{{ route('espace-eleve.evaluations') }}" class="ho-tete-lien">
                    Toutes mes notes @include('partials.icon', ['n' => 'arrow-right', 's' => 13, 'c' => 'var(--brand)', 'w' => 2])
                </a>
            @endif
        </div>

        @forelse ($dernieresNotes as $note)
            @php $valeur = is_numeric($note->note) ? (float) $note->note : null; @endphp
            <div class="ho-note">
                <span class="ho-note-txt">
                    <span class="ho-note-m">{{ $note->evaluation?->matiere?->nom_cours ?: 'Matière' }}</span>
                    <span class="ho-note-d">{{ $note->date_saisie?->format('d/m/Y') ?: '' }}</span>
                </span>
                <span class="ho-badge @if ($valeur !== null && $valeur < 10) is-low @endif">{{ $note20($valeur) }}<small>/20</small></span>
            </div>
        @empty
            <p class="ho-vide">Aucune note publiée pour le moment.</p>
        @endforelse
    </section>

    {{-- Bulletin : carte large et horizontale, pour ne pas laisser un vide. --}}
    <section class="ho-card ho-full ho-bulletin">
        <span class="ho-icone">@include('partials.icon', ['n' => 'file', 's' => 22, 'c' => 'var(--brand-deep)', 'w' => 1.8])</span>
        <span class="ho-bul-txt">
            <span class="ho-label">Mon dernier bulletin</span>
            @if ($dernierBulletin)
                <span class="ho-bul-titre">
                    {{ $dernierBulletin->semestre ? 'Semestre '.$dernierBulletin->semestre : 'Année complète' }}
                    · {{ $dernierBulletin->annee }}-{{ $dernierBulletin->annee + 1 }}
                </span>
                <span class="ho-bul-meta">
                    @if ($dernierBulletin->date_insert) Édité le {{ $dernierBulletin->date_insert->format('d/m/Y') }} @endif
                </span>
            @else
                <span class="ho-bul-titre is-vide">Aucun bulletin pour l'instant</span>
                <span class="ho-bul-meta">Vos bulletins apparaîtront ici dès que l'école les aura édités.</span>
            @endif
        </span>

        @if ($dernierBulletin)
            <a href="{{ route('espace-eleve.bulletins.download', $dernierBulletin) }}" target="_blank" class="btn">
                @include('partials.icon', ['n' => 'download', 's' => 15, 'c' => '#fff', 'w' => 2])Ouvrir le PDF
            </a>
        @else
            <a href="{{ route('espace-eleve.bulletins') }}" class="btn btn-ghost">Voir mes bulletins</a>
        @endif
    </section>
</div>
@endsection
