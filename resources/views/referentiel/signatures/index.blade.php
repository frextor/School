@extends('layouts.app')

@section('title', 'Signatures')

@section('content')
@php
    // Groupées par établissement : la règle « une seule principale » se lit
    // alors d'un coup d'œil, école par école.
    $parEcole = $signatures->groupBy(fn ($s) => $s->etablissement?->nom_etablissement ?: 'Établissement non précisé');

    // « M » s'abrège avec un point, « Mme » non.
    $civilite = fn ($valeur) => $valeur === 'M' ? 'M.' : $valeur;
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Signatures</span>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Signatures</h1>
            @if ($signatures->total())
                <span class="badge badge-brand">{{ $signatures->total() }} signature{{ $signatures->total() > 1 ? 's' : '' }}</span>
            @endif
        </div>
        <p class="page-sub">Le signataire des documents officiels de chaque établissement : nom, fonction et signature scannée.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('referentiel.signatures.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouvelle signature
        </a>
    </div>
</div>

@forelse ($parEcole as $nomEcole => $lignes)
    <div class="sg-ecole">
        <h2>{{ $nomEcole }}</h2>
        <span>{{ $lignes->count() }} signataire{{ $lignes->count() > 1 ? 's' : '' }}</span>
    </div>

    <div class="sg-grid">
        @foreach ($lignes as $signature)
            <article class="sg-card {{ $signature->principal ? 'is-principale' : '' }}">
                @if ($signature->principal)
                    <span class="sg-ruban">
                        @include('partials.icon', ['n' => 'check-simple', 's' => 12, 'c' => 'var(--brand-deep)', 'w' => 2.6])Principale
                    </span>
                @endif

                {{-- L'image d'abord : une signature se reconnaît à son tracé,
                     pas à la ligne de texte qui l'accompagne. --}}
                <div class="sg-vignette">
                    @if ($signature->cheminFichier())
                        <img src="{{ Storage::disk('public')->url($signature->cheminFichier()) }}"
                             alt="Signature de {{ $signature->nom_directeur }}">
                    @else
                        <span class="sg-sans">
                            @include('partials.icon', ['n' => 'pen', 's' => 20, 'c' => '#c9cdd9', 'w' => 1.7])
                            Aucune image
                        </span>
                    @endif
                </div>

                <div class="sg-id">
                    <span class="sg-nom">{{ $civilite($signature->civilite) }} {{ $signature->nom_directeur }}</span>
                    <span class="sg-fonction">{{ $signature->fonction }}</span>
                </div>

                <div class="sg-pied">
                    @unless ($signature->principal)
                        <form method="post" action="{{ route('referentiel.signatures.principale', $signature) }}" class="sg-form">
                            @csrf
                            <button type="submit" class="sg-designer">Définir comme principale</button>
                        </form>
                    @else
                        <span class="sg-note">Apposée par défaut sur les documents</span>
                    @endunless

                    <a href="{{ route('referentiel.signatures.edit', $signature) }}" class="row-btn" title="Modifier">
                        @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                    </a>
                    <form method="post" action="{{ route('referentiel.signatures.destroy', $signature) }}" class="sg-form"
                          onsubmit="return confirm('Supprimer la signature de {{ $signature->nom_directeur }} ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="row-btn is-danger" title="Supprimer">
                            @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#b91c1c', 'w' => 2])
                        </button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
@empty
    <div class="sg-vide">
        @include('partials.icon', ['n' => 'pen', 's' => 28, 'c' => '#c9cdd9', 'w' => 1.6])
        <p>Aucune signature enregistrée</p>
        <span>Enregistrez le directeur de chaque établissement et sa signature scannée : elle pourra être apposée sur les documents officiels.</span>
        <a href="{{ route('referentiel.signatures.create') }}">Créer une signature</a>
    </div>
@endforelse

@if ($signatures->hasPages())
    <div class="sg-pagination">{{ $signatures->links() }}</div>
@endif

<style>
    /* Signatures : une carte par signataire, l'image en évidence. */
    .sg-ecole { display: flex; align-items: baseline; gap: 10px; margin: 20px 2px 10px; }
    .sg-ecole:first-of-type { margin-top: 4px; }
    .sg-ecole h2 { margin: 0; font-size: 13px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); }
    .sg-ecole span { font-size: 12px; color: var(--faint); }

    .sg-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 13px; }
    .sg-card {
        position: relative; background: var(--surface); border: 1px solid var(--border);
        border-radius: 14px; padding: 15px; min-width: 0;
    }
    .sg-card.is-principale { border-color: #c3c6f5; box-shadow: var(--shadow-sm); }
    .sg-ruban {
        position: absolute; top: 12px; right: 12px; display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; border-radius: 999px; background: var(--brand-light); color: var(--brand-deep);
        font-size: 10.5px; font-weight: 700; letter-spacing: .03em;
    }

    .sg-vignette {
        display: flex; align-items: center; justify-content: center; height: 92px; padding: 8px;
        border: 1px dashed var(--border); border-radius: 11px; background: #fcfcfe;
    }
    .sg-vignette img { max-height: 100%; max-width: 100%; object-fit: contain; }
    .sg-sans { display: flex; flex-direction: column; align-items: center; gap: 5px; font-size: 11.5px; color: var(--faint); }

    .sg-id { margin-top: 13px; min-width: 0; }
    .sg-nom { display: block; font-size: 15px; font-weight: 700; letter-spacing: -.015em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sg-fonction { display: block; margin-top: 2px; font-size: 12.5px; color: var(--muted); }

    .sg-pied { display: flex; align-items: center; gap: 7px; margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--border-soft); }
    .sg-form { max-width: none; display: inline-block; margin: 0; }
    .sg-pied .sg-form:first-child { margin-right: auto; }
    .sg-note { margin-right: auto; font-size: 11.5px; color: var(--muted); }
    /* `button[type="submit"]` du layout l'emporterait sur une simple classe :
       le sélecteur est qualifié, sinon le bouton s'affiche en gros bouton indigo. */
    button.sg-designer {
        padding: 7px 12px; border-radius: 9px; border: 1px solid var(--border); background: #fff;
        color: var(--brand) !important; font-family: inherit; font-size: 12.5px; font-weight: 600;
        cursor: pointer; box-shadow: none;
    }
    button.sg-designer:hover { border-color: #c3c6f5; background: #fafbff; box-shadow: none; }
    .sg-pied .row-btn.is-danger:hover { border-color: #fecaca; background: var(--danger-bg); }

    .sg-vide {
        text-align: center; padding: 50px 20px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
    }
    .sg-vide p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .sg-vide span { display: block; margin: 4px auto 0; max-width: 58ch; font-size: 13px; color: var(--muted); }
    .sg-vide a { display: inline-block; margin-top: 10px; font-size: 12.5px; font-weight: 600; }

    .sg-pagination { margin-top: 16px; }
</style>
@endsection
