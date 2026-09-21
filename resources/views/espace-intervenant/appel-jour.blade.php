@extends('layouts.app')

@section('title', "Faire l'appel")

@section('content')
@php
    $lien = fn ($jour) => route('espace-intervenant.appel-jour', ['date' => $jour->format('Y-m-d')]);
@endphp

<div class="page-head">
    <div>
        <h1>Faire l'appel</h1>
        <p class="page-sub">Vos cours du jour. Ouvrez une séance pour pointer les présents, les absents et les retards.</p>
    </div>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

<div class="aj-bar">
    <div class="aj-nav">
        <a class="cal-fleche" href="{{ $lien($date->copy()->subDay()) }}" title="Jour précédent" aria-label="Jour précédent">
            @include('partials.icon', ['n' => 'chevron-left', 's' => 15, 'c' => '#585e72', 'w' => 2.2])
        </a>
        <a class="cal-fleche" href="{{ $lien($date->copy()->addDay()) }}" title="Jour suivant" aria-label="Jour suivant">
            @include('partials.icon', ['n' => 'chevron-right', 's' => 15, 'c' => '#585e72', 'w' => 2.2])
        </a>
        <a class="btn btn-ghost btn-sm" href="{{ $lien(now()) }}">Aujourd'hui</a>
    </div>
    <span class="aj-jour">{{ ucfirst($date->translatedFormat('l j F Y')) }}</span>
</div>

<div class="aj-grid">
    @forelse ($seances as $seance)
        @php
            $heure = $seance->date_debut->format('H:i');
            $saisiesSeance = $saisies[$heure.'|'.$seance->id_classe] ?? collect();
            $absents = $saisiesSeance->filter(fn ($a) => $a->nature === \App\Models\AbsenceEleve::NATURE_ABSENCE)->count();
            $retards = $saisiesSeance->filter(fn ($a) => $a->nature === \App\Models\AbsenceEleve::NATURE_RETARD)->count();
            $saisi = $saisiesSeance->isNotEmpty();
        @endphp
        <a href="{{ route('espace-intervenant.appel', $seance) }}" class="aj-card">
            <span class="aj-bande" style="background: {{ $seance->classe?->couleur ?: '#4f46e5' }}"></span>
            <span class="aj-corps">
                <span class="aj-heure">{{ $heure }} – {{ $seance->date_fin->format('H:i') }}</span>
                <span class="aj-matiere">{{ $seance->cours?->nom_cours ?: 'Cours' }}</span>
                <span class="aj-meta">
                    {{ collect([
                        $seance->classe?->classe,
                        $seance->salle?->nom_salle ? 'Salle '.$seance->salle->nom_salle : null,
                    ])->filter()->implode(' · ') ?: '—' }}
                </span>

                <span class="aj-pied">
                    @if ($saisi)
                        <span class="aj-etat is-saisi">
                            {{ $absents }} absent{{ $absents > 1 ? 's' : '' }}@if ($retards), {{ $retards }} retard{{ $retards > 1 ? 's' : '' }}@endif
                        </span>
                    @else
                        <span class="aj-etat">Aucune absence enregistrée</span>
                    @endif
                    <span class="aj-lien">
                        {{ $saisi ? "Modifier l'appel" : "Faire l'appel" }}
                        @include('partials.icon', ['n' => 'arrow-right', 's' => 13, 'c' => 'var(--brand)', 'w' => 2])
                    </span>
                </span>
            </span>
        </a>
    @empty
        <div class="aj-vide">
            @include('partials.icon', ['n' => 'cal', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
            <p>Aucun cours ce jour-là</p>
            <span>Changez de journée avec les flèches, ou consultez votre emploi du temps.</span>
            <a href="{{ route('espace-intervenant.planning') }}">Voir mon emploi du temps</a>
        </div>
    @endforelse
</div>

<p class="aj-note">
    Une séance sans absence ne laisse aucune trace : « Aucune absence enregistrée » signifie
    donc soit un appel tout présent, soit un appel pas encore fait.
</p>

<style>
    /* Appel du jour : une carte par séance, dans l'ordre des heures. */
    .aj-bar {
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 14px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 11px 14px;
    }
    .aj-nav { display: flex; align-items: center; gap: 7px; }
    .aj-jour { margin-left: auto; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }

    .aj-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px; }
    .aj-card {
        display: flex; overflow: hidden; color: var(--ink);
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
        transition: border-color .14s ease, box-shadow .14s ease, transform .14s ease;
    }
    .aj-card:hover { border-color: #c3c6f5; box-shadow: var(--shadow-md); transform: translateY(-1px); color: var(--ink); }
    .aj-bande { width: 5px; flex-shrink: 0; }
    .aj-corps { display: block; padding: 15px 16px; flex: 1; min-width: 0; }
    .aj-heure { display: block; font-size: 12px; font-weight: 700; color: var(--brand); font-variant-numeric: tabular-nums; }
    .aj-matiere { display: block; margin-top: 3px; font-size: 15.5px; font-weight: 700; letter-spacing: -.015em; }
    .aj-meta { display: block; font-size: 12.5px; color: var(--muted); margin-top: 2px; }
    .aj-pied { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 13px; padding-top: 11px; border-top: 1px solid var(--border-soft); }
    .aj-etat { font-size: 12px; font-weight: 600; color: var(--muted); }
    .aj-etat.is-saisi { color: var(--danger); }
    .aj-lien { display: inline-flex; align-items: center; gap: 5px; margin-left: auto; font-size: 12px; font-weight: 600; color: var(--brand); white-space: nowrap; }

    .aj-vide {
        grid-column: 1 / -1; text-align: center; padding: 48px 20px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
    }
    .aj-vide p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .aj-vide span { display: block; margin: 4px auto 0; max-width: 46ch; font-size: 13px; color: var(--muted); }
    .aj-vide a { display: inline-block; margin-top: 10px; font-size: 12.5px; font-weight: 600; }

    .aj-note { margin: 14px 0 0; font-size: 11.5px; color: var(--muted); max-width: 80ch; }
    .btn-sm { padding: .34rem .7rem; font-size: 12px; box-shadow: none; }
</style>
@endsection
