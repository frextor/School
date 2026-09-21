@extends('layouts.app')

@section('title', 'Mes classes')

@section('content')
<div class="page-head">
    <div>
        <h1>Mes classes</h1>
        <p class="page-sub">Les classes où vous avez des cours planifiés. Ouvrez-en une pour voir ses élèves.</p>
    </div>
</div>

<div class="cl-grid">
    @forelse ($classes as $classe)
        <a href="{{ route('espace-intervenant.roster', $classe) }}" class="cl-card">
            <span class="cl-bande" style="background: {{ $classe->couleur ?: '#4f46e5' }}"></span>
            <span class="cl-corps">
                <span class="cl-nom">{{ $classe->classe }}</span>
                <span class="cl-niveau">{{ $classe->niveau?->nom_niveau ?: 'Niveau non précisé' }}</span>
                <span class="cl-pied">
                    <span class="cl-eff">
                        @include('partials.icon', ['n' => 'users', 's' => 14, 'c' => '#585e72', 'w' => 2])
                        {{ $classe->eleves_count }} élève{{ $classe->eleves_count > 1 ? 's' : '' }}
                    </span>
                    <span class="cl-lien">
                        Voir les élèves @include('partials.icon', ['n' => 'arrow-right', 's' => 13, 'c' => 'var(--brand)', 'w' => 2])
                    </span>
                </span>
            </span>
        </a>
    @empty
        <div class="cl-vide">
            @include('partials.icon', ['n' => 'users', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
            <p>Aucune classe pour l'instant</p>
            <span>Vos classes apparaîtront ici dès que des cours vous seront affectés à l'emploi du temps.</span>
        </div>
    @endforelse
</div>

<style>
    /* Mes classes : des cartes reprenant la couleur de la classe, plutôt
       qu'un tableau de trois colonnes. */
    .cl-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 14px; }
    .cl-card {
        display: flex; color: var(--ink); overflow: hidden;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
        transition: border-color .14s ease, box-shadow .14s ease, transform .14s ease;
    }
    .cl-card:hover { border-color: #c3c6f5; box-shadow: var(--shadow-md); transform: translateY(-1px); color: var(--ink); }
    .cl-bande { width: 5px; flex-shrink: 0; }
    .cl-corps { display: block; padding: 16px 17px; min-width: 0; flex: 1; }
    .cl-nom { display: block; font-size: 17px; font-weight: 700; letter-spacing: -.02em; }
    .cl-niveau { display: block; font-size: 12.5px; color: var(--muted); margin-top: 2px; }
    .cl-pied { display: flex; align-items: center; gap: 12px; margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--border-soft); }
    .cl-eff { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; color: #585e72; }
    .cl-lien { display: inline-flex; align-items: center; gap: 5px; margin-left: auto; font-size: 12px; font-weight: 600; color: var(--brand); white-space: nowrap; }

    .cl-vide {
        grid-column: 1 / -1; text-align: center; padding: 48px 20px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
    }
    .cl-vide p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .cl-vide span { display: block; margin-top: 4px; font-size: 13px; color: var(--muted); max-width: 46ch; margin-left: auto; margin-right: auto; }
</style>
@endsection
