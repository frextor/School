@extends('layouts.app')

@section('title', 'Intervenants')

@section('content')
@php
    $chips = collect([
        'Recherche' => ['recherche', $filtres['recherche'] ?? null],
    ])->filter(fn ($paire) => filled($paire[1]))->all();
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Intervenants</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Intervenants</h1>
            <span class="badge badge-brand">{{ number_format($intervenants->total(), 0, ',', ' ') }} intervenants</span>
        </div>
        <p class="page-sub">Fiches, compétences et cours enseignés.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('referentiel.intervenants.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouvel intervenant
        </a>
    </div>
</div>

<form method="get" class="filter-card">
    <div class="filter-row">
        <div class="filter-search">
            @include('partials.icon', ['n' => 'search', 's' => 16, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:13px;top:11px'])
            <input type="text" name="recherche" value="{{ $filtres['recherche'] ?? '' }}" placeholder="Nom, prénom..." aria-label="Recherche">
        </div>
    </div>

    <div class="filter-foot">
        @if (count($chips))
            <span class="filter-foot-label">Filtres actifs</span>
            @foreach ($chips as $label => [$param, $valeur])
                <a href="{{ request()->fullUrlWithQuery([$param => null]) }}" class="chip">
                    {{ $label }} : {{ $valeur }}
                    @include('partials.icon', ['n' => 'plus', 's' => 12, 'w' => 2.4, 'style' => 'transform:rotate(45deg)'])
                </a>
            @endforeach
        @endif
        <a href="{{ route('referentiel.intervenants.index') }}" class="filter-reset">Réinitialiser</a>
        <button type="submit" class="btn">Rechercher</button>
    </div>
</form>

<div class="table-card">
    <div class="table-head">
        <span class="table-count">{{ $intervenants->firstItem() }}–{{ $intervenants->lastItem() }} sur {{ number_format($intervenants->total(), 0, ',', ' ') }}</span>
    </div>

    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Intervenant</th>
                    <th>Société</th>
                    <th>Téléphone</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($intervenants as $intervenant)
                    @php
                        $initiales = mb_strtoupper(mb_substr($intervenant->prenom ?? '?', 0, 1).mb_substr($intervenant->nom ?? '', 0, 1));
                    @endphp
                    <tr>
                        <td>
                            <div class="cell-user">
                                <span class="cell-avatar">{{ $initiales }}</span>
                                <span class="cell-user-text">
                                    <a href="{{ route('referentiel.intervenants.show', $intervenant) }}" class="cell-name">{{ $intervenant->civilite }} {{ $intervenant->nom }} {{ $intervenant->prenom }}</a>
                                    <span class="cell-sub">{{ $intervenant->email }}</span>
                                </span>
                            </div>
                        </td>
                        <td>{{ $intervenant->societe?->raison_sociale ?? '—' }}</td>
                        <td class="num">{{ $intervenant->telephone ?: $intervenant->mobile ?: '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('referentiel.intervenants.show', $intervenant) }}" class="row-btn" title="Voir la fiche">
                                @include('partials.icon', ['n' => 'eye', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <form method="post" action="{{ route('referentiel.intervenants.destroy', $intervenant) }}" style="display:inline" onsubmit="return confirm('Supprimer cet intervenant ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="row-btn" title="Supprimer" style="color:var(--danger)">
                                    @include('partials.icon', ['n' => 'alert', 's' => 14, 'c' => 'currentColor'])
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-cell">
                            @include('partials.icon', ['n' => 'school', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun intervenant ne correspond à ces filtres.</span>
                            <a href="{{ route('referentiel.intervenants.index') }}">Réinitialiser la recherche</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">
        {{ $intervenants->links() }}
    </div>
</div>
@endsection
