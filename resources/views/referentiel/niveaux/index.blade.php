@extends('layouts.app')

@section('title', 'Niveaux')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Niveaux</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Niveaux</h1>
            <span class="badge badge-brand">{{ number_format($niveaux->total(), 0, ',', ' ') }} niveaux</span>
        </div>
        <p class="page-sub">Niveaux de formation et campus associés.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('referentiel.niveaux.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouveau niveau
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
                    <th>Formation</th>
                    <th>Campus</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($niveaux as $niveau)
                    <tr>
                        <td class="num">{{ $niveau->code_niveau }}</td>
                        <td class="cell-name">{{ $niveau->nom_niveau }}</td>
                        <td>{{ $niveau->formation?->niveau ?? '—' }}</td>
                        <td>{{ $niveau->etablissements->pluck('nom_etablissement')->join(', ') ?: '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('referentiel.niveaux.edit', $niveau) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <form method="post" action="{{ route('referentiel.niveaux.destroy', $niveau) }}" style="display:inline" onsubmit="return confirm('Supprimer ce niveau ?')">
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
                        <td colspan="5" class="empty-cell">
                            @include('partials.icon', ['n' => 'levels', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun niveau.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">
        {{ $niveaux->links() }}
    </div>
</div>
@endsection
