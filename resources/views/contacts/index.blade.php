@extends('layouts.app')

@section('title', 'Contacts')

@section('content')
@php
    $chips = collect([
        'Recherche' => ['recherche', $filtres['recherche'] ?? null],
        'Agents de joueur' => ['agent_de_joueur', ($filtres['agent_de_joueur'] ?? false) ? 'Oui' : null],
        'Salons' => ['salon', ($filtres['salon'] ?? false) ? 'Oui' : null],
    ])->filter(fn ($paire) => filled($paire[1]))->all();
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Contacts</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Contacts</h1>
            <span class="badge badge-brand">{{ number_format($contacts->total(), 0, ',', ' ') }} contacts</span>
        </div>
        <p class="page-sub">Prospects et leads CRM.</p>
    </div>
</div>

<form method="get" class="filter-card">
    <div class="filter-row">
        <div class="filter-search">
            @include('partials.icon', ['n' => 'search', 's' => 16, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:13px;top:11px'])
            <input type="text" name="recherche" value="{{ $filtres['recherche'] ?? '' }}" placeholder="Nom, prénom, email..." aria-label="Recherche">
        </div>
        <label class="check" style="margin:0"><input type="checkbox" name="agent_de_joueur" value="1" @checked($filtres['agent_de_joueur'] ?? false)> Agents de joueur</label>
        <label class="check" style="margin:0"><input type="checkbox" name="salon" value="1" @checked($filtres['salon'] ?? false)> Salons</label>
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
        <a href="{{ route('contacts.index') }}" class="filter-reset">Réinitialiser</a>
        <button type="submit" class="btn">Rechercher</button>
    </div>
</form>

<div class="table-card">
    <div class="table-head">
        <span class="table-count">{{ $contacts->firstItem() }}–{{ $contacts->lastItem() }} sur {{ number_format($contacts->total(), 0, ',', ' ') }}</span>
    </div>

    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Contact</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contacts as $contact)
                    @php
                        $initiales = mb_strtoupper(mb_substr($contact->prenom ?? '?', 0, 1).mb_substr($contact->nom ?? '', 0, 1));
                    @endphp
                    <tr>
                        <td>
                            <div class="cell-user">
                                <span class="cell-avatar">{{ $initiales }}</span>
                                <span class="cell-user-text">
                                    <a href="{{ route('contacts.show', $contact) }}" class="cell-name">{{ $contact->civilite }} {{ $contact->nom_complet }}</a>
                                    <span class="cell-sub">{{ $contact->email }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="num">{{ $contact->telephone ?: '—' }}</td>
                        <td><span class="badge badge-brand">{{ $contact->eleve ? ucfirst($contact->eleve->profil) : 'Contact' }}</span></td>
                        <td class="col-actions">
                            <a href="{{ route('contacts.show', $contact) }}" class="row-btn" title="Voir la fiche">
                                @include('partials.icon', ['n' => 'eye', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <a href="{{ route('contacts.edit', $contact) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-cell">
                            @include('partials.icon', ['n' => 'contact', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun contact ne correspond à ces filtres.</span>
                            <a href="{{ route('contacts.index') }}">Réinitialiser la recherche</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-foot">
        {{ $contacts->links() }}
    </div>
</div>
@endsection
