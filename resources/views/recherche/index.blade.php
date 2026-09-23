@extends('layouts.app')

@section('title', 'Recherche')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Recherche</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Recherche</h1>
            @if ($terme !== '')
                <span class="badge badge-brand">{{ count($resultats) }} résultat{{ count($resultats) > 1 ? 's' : '' }}</span>
            @endif
        </div>
        <p class="page-sub">Élèves, familles et prospects, par nom, prénom, e-mail ou téléphone.</p>
    </div>
</div>

<form method="get" action="{{ route('recherche.search') }}" class="filter-card">
    <div class="filter-row">
        <div class="filter-search">
            @include('partials.icon', ['n' => 'search', 's' => 15, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:13px;top:12px'])
            <input type="text" name="rechercher" value="{{ $terme }}" placeholder="Nom, prénom, e-mail, téléphone…" autofocus>
        </div>
        <button type="submit" class="btn">Rechercher</button>
        @if ($terme !== '')
            <a href="{{ route('recherche.search') }}" class="btn btn-ghost">Effacer</a>
        @endif
    </div>
</form>

@if ($terme === '')
    <div class="vide-card">
        @include('partials.icon', ['n' => 'search', 's' => 28, 'c' => '#c9cdd9', 'w' => 1.6])
        <p>Saisissez un nom, un e-mail ou un numéro</p>
        <span>La recherche parcourt les élèves inscrits comme les familles et les prospects du CRM.</span>
    </div>
@else
    <div class="table-card">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th style="width:160px">Téléphone</th>
                        <th style="width:260px">E-mail</th>
                        <th>Établissement</th>
                        <th style="width:130px">Type</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($resultats as $resultat)
                        @php
                            $contact = $resultat['contact'];
                            $lien = $contact->eleve
                                ? route('eleves.show', $contact->eleve)
                                : route('contacts.show', $contact);
                        @endphp
                        <tr>
                            <td><a href="{{ $lien }}" class="cell-name">{{ $contact->nom_complet }}</a></td>
                            <td>{{ $contact->telephone ?: '—' }}</td>
                            <td>{{ $contact->email ?: '—' }}</td>
                            <td>{{ $resultat['etablissements'] ?: '—' }}</td>
                            <td>
                                <span class="badge {{ $contact->eleve ? 'badge-brand' : '' }}">{{ ucfirst($resultat['type']) }}</span>
                            </td>
                            <td class="col-actions">
                                <a href="{{ $lien }}" class="row-btn" title="Ouvrir la fiche">
                                    @include('partials.icon', ['n' => 'arrow-right', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">
                                @include('partials.icon', ['n' => 'search', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                                <span>Aucun résultat pour « {{ $terme }} ».</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
