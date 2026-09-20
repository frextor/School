@extends('layouts.app')

@section('title', 'Mes notes')

@section('content')
@php
    $note20 = fn ($v) => $v === null ? '—' : number_format((float) $v, 2, ',', ' ');
@endphp

<div class="page-head">
    <div>
        <h1>Mes notes</h1>
        <p class="page-sub">Les notes publiées par vos enseignants, matière par matière.</p>
    </div>
</div>

@if ($total > 0)
    <div class="no-resume">
        <div class="no-chiffre">
            <span class="no-label">Moyenne des notes publiées</span>
            <strong class="@if ($moyenneGenerale !== null && $moyenneGenerale < 10) is-low @endif">{{ $note20($moyenneGenerale) }}<small> / 20</small></strong>
        </div>
        <div class="no-chiffre">
            <span class="no-label">Matières notées</span>
            <strong>{{ $matieres->count() }}</strong>
        </div>
        <div class="no-chiffre">
            <span class="no-label">Notes reçues</span>
            <strong>{{ $total }}</strong>
        </div>
        <p class="no-avertissement">
            Moyenne simple, à titre indicatif : la moyenne officielle du bulletin tient compte
            des coefficients de chaque matière.
        </p>
    </div>
@endif

<div class="no-grid">
    @forelse ($matieres as $nomMatiere => $matiere)
        @php $moyenne = $matiere['moyenne']; @endphp
        <section class="no-card">
            <div class="no-card-tete">
                <span class="no-matiere">{{ $nomMatiere }}</span>
                <span class="no-moyenne @if ($moyenne !== null && $moyenne < 10) is-low @endif">{{ $note20($moyenne) }}<small> / 20</small></span>
            </div>

            <div class="no-lignes">
                @foreach ($matiere['notes'] as $note)
                    @php $valeur = is_numeric($note->note) ? (float) $note->note : null; @endphp
                    <div class="no-ligne">
                        <span class="no-ligne-txt">
                            <span class="no-type">{{ $note->evaluation?->typeEvaluation?->type?->type ?: 'Évaluation' }}</span>
                            <span class="no-date">
                                {{ $note->date_saisie?->format('d/m/Y') ?: '—' }}
                                @if ($note->session) · session {{ $note->session }} @endif
                            </span>
                        </span>
                        <span class="no-valeur @if ($valeur !== null && $valeur < 10) is-low @endif">{{ $note20($valeur) }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <div class="no-vide">
            @include('partials.icon', ['n' => 'pencil', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
            <p>Aucune note publiée pour le moment.</p>
            <span>Vos notes apparaîtront ici dès que vos enseignants les auront publiées.</span>
        </div>
    @endforelse
</div>

<style>
    /* Mes notes : un bandeau de synthèse puis une carte par matière —
       l'écran empilait un tableau complet par matière, pour une seule ligne. */
    .no-resume {
        display: flex; align-items: flex-start; gap: 28px; flex-wrap: wrap;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
        padding: 16px 18px; margin-bottom: 14px;
    }
    .no-label { display: block; font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); }
    .no-chiffre strong { display: block; margin-top: 5px; font-size: 26px; font-weight: 700; letter-spacing: -.025em; line-height: 1; color: var(--brand); }
    .no-chiffre strong small { font-size: 13px; font-weight: 600; color: var(--muted); letter-spacing: 0; }
    .no-chiffre strong.is-low { color: var(--danger); }
    .no-avertissement { margin: 0 0 0 auto; max-width: 34ch; font-size: 11.5px; color: var(--muted); line-height: 1.5; text-wrap: pretty; }

    .no-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px; }
    .no-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .no-card-tete {
        display: flex; align-items: center; gap: 12px; padding: 13px 16px;
        border-bottom: 1px solid var(--border-soft); background: #fafbfd;
    }
    .no-matiere { font-size: 13.5px; font-weight: 700; letter-spacing: -.01em; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .no-moyenne { margin-left: auto; font-size: 15px; font-weight: 700; color: var(--brand); font-variant-numeric: tabular-nums; white-space: nowrap; }
    .no-moyenne small { font-size: 11px; font-weight: 600; color: var(--muted); }
    .no-moyenne.is-low { color: var(--danger); }

    .no-ligne { display: flex; align-items: center; gap: 12px; padding: 11px 16px; }
    .no-ligne + .no-ligne { border-top: 1px solid var(--border-soft); }
    .no-ligne-txt { min-width: 0; }
    .no-type { display: block; font-size: 13px; font-weight: 600; }
    .no-date { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
    .no-valeur { margin-left: auto; font-size: 14px; font-weight: 700; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .no-valeur.is-low { color: var(--danger); }

    .no-vide {
        grid-column: 1 / -1; text-align: center; padding: 48px 20px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
    }
    .no-vide p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .no-vide span { display: block; margin-top: 4px; font-size: 13px; color: var(--muted); }

    @media (max-width: 720px) {
        .no-avertissement { margin-left: 0; }
    }
</style>
@endsection
