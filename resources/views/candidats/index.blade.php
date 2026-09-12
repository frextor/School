@extends('layouts.app')

@section('title', ($filtres['archives'] ?? false) ? 'Candidats archivés' : 'Candidats')

@section('content')
@php
    $archives = $filtres['archives'] ?? false;
    $titre = $archives ? 'Candidats archivés' : 'Candidats';

    $chips = collect([
        'Recherche' => ['recherche', $filtres['recherche'] ?? null],
        'Sans épreuve' => ['sans_epreuve', ($filtres['sans_epreuve'] ?? false) ? 'Oui' : null],
    ])->filter(fn ($paire) => filled($paire[1]))->all();
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">{{ $titre }}</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $titre }}</h1>
            <span class="badge badge-brand">{{ number_format($candidats->total(), 0, ',', ' ') }} candidats</span>
        </div>
        <p class="page-sub">{{ $archives ? 'Dossiers archivés.' : "Candidatures en cours d'instruction." }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('epreuves.index') }}" class="btn btn-ghost">
            @include('partials.icon', ['n' => 'calc', 's' => 15, 'w' => 2])Épreuves d'admission
        </a>
    </div>
</div>

<form method="get" class="filter-card">
    <div class="filter-row">
        <div class="filter-search">
            @include('partials.icon', ['n' => 'search', 's' => 16, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:13px;top:11px'])
            <input type="text" name="recherche" value="{{ $filtres['recherche'] ?? '' }}" placeholder="Nom, prénom..." aria-label="Recherche">
        </div>
        <label class="check" style="margin:0"><input type="checkbox" name="sans_epreuve" value="1" @checked($filtres['sans_epreuve'] ?? false)> Sans épreuve programmée</label>
        <label class="check" style="margin:0"><input type="checkbox" name="archives" value="1" @checked($archives) onchange="this.form.submit()"> Voir les archivés</label>
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
        <a href="{{ route('candidats.index') }}" class="filter-reset">Réinitialiser</a>
        <button type="submit" class="btn">Rechercher</button>
    </div>
</form>

<div class="table-card">
    <div class="table-head">
        <span class="table-count">{{ $candidats->firstItem() }}–{{ $candidats->lastItem() }} sur {{ number_format($candidats->total(), 0, ',', ' ') }}</span>
    </div>

    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Candidat</th>
                    <th>Niveau</th>
                    <th>Email</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($candidats as $candidat)
                    @php
                        $contact = $candidat->contact;
                        $nom = $contact?->nom_complet ?? '—';
                        $initiales = mb_strtoupper(mb_substr($contact?->prenom ?? '?', 0, 1).mb_substr($contact?->nom ?? '', 0, 1));
                    @endphp
                    <tr>
                        <td>
                            <div class="cell-user">
                                <span class="cell-avatar">{{ $initiales }}</span>
                                <span class="cell-user-text">
                                    <a href="{{ route('candidats.show', $candidat) }}" class="cell-name">{{ $nom }}</a>
                                </span>
                            </div>
                        </td>
                        <td>{{ $candidat->niveau?->nom_niveau ?? '—' }}</td>
                        <td>{{ $contact?->email ?? '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('candidats.show', $candidat) }}" class="row-btn" title="Voir la fiche">
                                @include('partials.icon', ['n' => 'eye', 's' => 14, 'c' => '#585e72'])
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-cell">
                            @include('partials.icon', ['n' => 'contact', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun candidat ne correspond à ces filtres.</span>
                            <a href="{{ route('candidats.index') }}">Réinitialiser la recherche</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-foot">
        {{ $candidats->links() }}
    </div>
</div>
@endsection
