@extends('layouts.app')

@section('title', "Unités d'enseignement")

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Unités d'enseignement</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Unités d'enseignement</h1>
            <span class="badge badge-brand">{{ number_format($unites->total(), 0, ',', ' ') }} UE</span>
        </div>
        <p class="page-sub">Unités d'enseignement par niveau.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('referentiel.unites.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouvelle UE
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Couleur</th>
                    <th>Niveau</th>
                    <th>Années</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($unites as $unite)
                    <tr>
                        <td class="num">{{ $unite->code_unite }}</td>
                        <td class="cell-name">{{ $unite->nom_unite_enseignement }}</td>
                        <td><span style="display:inline-block;width:16px;height:16px;border-radius:4px;background:{{ $unite->couleur }}"></span></td>
                        <td>{{ $unite->niveau?->nom_niveau ?? '—' }}</td>
                        <td>{{ $unite->annees->pluck('annee')->join(', ') ?: '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('referentiel.unites.edit', $unite) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <form method="post" action="{{ route('referentiel.unites.destroy', $unite) }}" style="display:inline" onsubmit="return confirm('Supprimer cette UE ?')">
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
                        <td colspan="6" class="empty-cell">
                            @include('partials.icon', ['n' => 'book', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune unité d'enseignement.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">
        {{ $unites->links() }}
    </div>
</div>
@endsection
