@extends('layouts.app')

@section('title', 'Matières')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Matières</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Matières</h1>
            <span class="badge badge-brand">{{ number_format($matieres->total(), 0, ',', ' ') }} matières</span>
        </div>
        <p class="page-sub">Matières rattachées aux unités d'enseignement.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('referentiel.matieres.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouvelle matière
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
                    <th>UE</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($matieres as $matiere)
                    <tr>
                        <td class="num">{{ $matiere->code_matiere }}</td>
                        <td class="cell-name">{{ $matiere->nom_matiere }}</td>
                        <td>{{ $matiere->unite?->nom_unite_enseignement ?? '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('referentiel.matieres.edit', $matiere) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <form method="post" action="{{ route('referentiel.matieres.destroy', $matiere) }}" style="display:inline" onsubmit="return confirm('Supprimer cette matière ?')">
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
                            @include('partials.icon', ['n' => 'calc', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune matière.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">
        {{ $matieres->links() }}
    </div>
</div>
@endsection
