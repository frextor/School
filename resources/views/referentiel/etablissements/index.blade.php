@extends('layouts.app')

@section('title', 'Établissements')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Établissements</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Établissements</h1>
            <span class="badge badge-brand">{{ number_format($etablissements->total(), 0, ',', ' ') }} établissements</span>
        </div>
        <p class="page-sub">Campus et sites de l'établissement.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('referentiel.etablissements.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouvel établissement
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Ville</th>
                    <th>Adresse</th>
                    <th>Statut</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($etablissements as $etablissement)
                    <tr>
                        <td class="cell-name">{{ $etablissement->nom_etablissement }}</td>
                        <td>{{ $etablissement->code_ville }}</td>
                        <td>{{ $etablissement->adresse }}</td>
                        <td>
                            @if ($etablissement->visible)
                                <span class="badge badge-success">Affiché</span>
                            @else
                                <span class="badge">Caché</span>
                            @endif
                        </td>
                        <td class="col-actions">
                            <a href="{{ route('referentiel.etablissements.edit', $etablissement) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-cell">
                            @include('partials.icon', ['n' => 'school', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun établissement.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">
        {{ $etablissements->links() }}
    </div>
</div>
@endsection
