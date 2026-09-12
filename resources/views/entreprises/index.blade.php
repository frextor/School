@extends('layouts.app')

@section('title', 'Entreprises')

@section('content')
@php
    $chips = collect([
        'Recherche' => ['recherche', $filtres['recherche'] ?? null],
    ])->filter(fn ($paire) => filled($paire[1]))->all();
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Entreprises</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Entreprises</h1>
            <span class="badge badge-brand">{{ number_format($entreprises->total(), 0, ',', ' ') }} entreprises</span>
        </div>
        <p class="page-sub">Partenaires et contacts professionnels.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('entreprises.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouvelle entreprise
        </a>
    </div>
</div>

<form method="get" class="filter-card">
    <div class="filter-row">
        <div class="filter-search">
            @include('partials.icon', ['n' => 'search', 's' => 16, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:13px;top:11px'])
            <input type="text" name="recherche" value="{{ $filtres['recherche'] ?? '' }}" placeholder="Nom de l'entreprise..." aria-label="Recherche">
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
        <a href="{{ route('entreprises.index') }}" class="filter-reset">Réinitialiser</a>
        <button type="submit" class="btn">Rechercher</button>
    </div>
</form>

<div class="table-card">
    <div class="table-head">
        <span class="table-count">{{ $entreprises->firstItem() }}–{{ $entreprises->lastItem() }} sur {{ number_format($entreprises->total(), 0, ',', ' ') }}</span>
    </div>

    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Entreprise</th>
                    <th>Ville</th>
                    <th>Secteur</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entreprises as $entreprise)
                    @php
                        $initiales = mb_strtoupper(mb_substr($entreprise->nom_entreprise ?? '?', 0, 2));
                    @endphp
                    <tr>
                        <td>
                            <div class="cell-user">
                                <span class="cell-avatar">{{ $initiales }}</span>
                                <span class="cell-user-text">
                                    <a href="{{ route('entreprises.show', $entreprise) }}" class="cell-name">{{ $entreprise->nom_entreprise }}</a>
                                    <span class="cell-sub">{{ $entreprise->email ?: '—' }}</span>
                                </span>
                            </div>
                        </td>
                        <td>{{ $entreprise->ville ?: '—' }}</td>
                        <td>{{ $entreprise->secteur?->nom_secteur ?? '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('entreprises.show', $entreprise) }}" class="row-btn" title="Voir la fiche">
                                @include('partials.icon', ['n' => 'eye', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <a href="{{ route('entreprises.edit', $entreprise) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-cell">
                            @include('partials.icon', ['n' => 'building', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune entreprise ne correspond à ces filtres.</span>
                            <a href="{{ route('entreprises.index') }}">Réinitialiser la recherche</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-foot">
        {{ $entreprises->links() }}
    </div>
</div>
@endsection
