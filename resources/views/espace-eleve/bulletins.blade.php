@extends('layouts.app')

@section('title', 'Mes bulletins')

@section('content')
<div class="page-head">
    <div>
        <h1>Mes bulletins</h1>
        <p class="page-sub">Vos bulletins édités par l'école et les décisions de conseil de classe.</p>
    </div>
</div>

<div class="bu-grid">
    @forelse ($bulletinsPdf as $bulletin)
        <a href="{{ route('espace-eleve.bulletins.download', $bulletin) }}" target="_blank" class="bu-card">
            <span class="bu-icone">@include('partials.icon', ['n' => 'file', 's' => 20, 'c' => 'var(--brand-deep)', 'w' => 1.8])</span>
            <span class="bu-txt">
                <span class="bu-titre">{{ $bulletin->semestre ? 'Semestre '.$bulletin->semestre : 'Année complète' }}</span>
                <span class="bu-meta">
                    {{ $bulletin->annee }}-{{ $bulletin->annee + 1 }}
                    @if ($bulletin->date_insert) · édité le {{ $bulletin->date_insert->format('d/m/Y') }} @endif
                </span>
            </span>
            <span class="bu-action">
                @include('partials.icon', ['n' => 'download', 's' => 16, 'c' => 'var(--brand)', 'w' => 2])
            </span>
        </a>
    @empty
        <div class="bu-vide">
            @include('partials.icon', ['n' => 'printer', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
            <p>Aucun bulletin disponible</p>
            <span>Vos bulletins apparaîtront ici dès que l'école les aura édités.</span>
        </div>
    @endforelse
</div>

@if ($bulletinsAdmin->isNotEmpty())
    <h2 class="bu-sous-titre">Décisions de conseil de classe</h2>

    <div class="bu-decisions">
        @foreach ($bulletinsAdmin as $bulletin)
            <div class="bu-decision">
                <span class="bu-periode">
                    {{ $bulletin->annee }}{{ $bulletin->semestre ? ' · semestre '.$bulletin->semestre : '' }}
                </span>
                <span class="bu-verdict">{{ $bulletin->decision_jury ?: 'Décision non précisée' }}</span>
                @if ($bulletin->commentaire)
                    <p class="bu-commentaire">{{ $bulletin->commentaire }}</p>
                @endif
            </div>
        @endforeach
    </div>
@endif

<style>
    /* Mes bulletins : des documents à ouvrir, pas un tableau de base de données. */
    .bu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; }
    .bu-card {
        display: flex; align-items: center; gap: 13px; padding: 15px 16px; color: var(--ink);
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
        transition: border-color .14s ease, background .14s ease;
    }
    .bu-card:hover { border-color: #c3c6f5; background: #fafbff; color: var(--ink); }
    .bu-icone {
        width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0; background: var(--brand-light);
        display: flex; align-items: center; justify-content: center;
    }
    .bu-txt { min-width: 0; }
    .bu-titre { display: block; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .bu-meta { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; }
    .bu-action { margin-left: auto; flex-shrink: 0; }

    .bu-vide {
        grid-column: 1 / -1; text-align: center; padding: 48px 20px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
    }
    .bu-vide p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .bu-vide span { display: block; margin-top: 4px; font-size: 13px; color: var(--muted); }

    .bu-sous-titre { margin: 26px 0 12px; font-size: 15px; font-weight: 700; letter-spacing: -.015em; }
    .bu-decisions { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; }
    .bu-decision { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 15px 16px; }
    .bu-periode { display: block; font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); }
    .bu-verdict { display: block; margin-top: 5px; font-size: 15px; font-weight: 700; letter-spacing: -.015em; }
    .bu-commentaire { margin: 7px 0 0; font-size: 12.5px; color: #585e72; line-height: 1.5; text-wrap: pretty; }
</style>
@endsection
