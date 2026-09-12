@extends('layouts.app')

@section('title', 'Cours')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Cours</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Cours</h1>
            <span class="badge badge-brand">{{ number_format($cours->total(), 0, ',', ' ') }} cours</span>
        </div>
        <p class="page-sub">Cours rattachés aux unités d'enseignement.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('referentiel.cours.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouveau cours
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
                    <th>Années</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cours as $cour)
                    <tr>
                        <td class="num">{{ $cour->code_cours }}</td>
                        <td class="cell-name">{{ $cour->nom_cours }}</td>
                        <td>{{ $cour->unite?->nom_unite_enseignement ?? '—' }}</td>
                        <td>{{ $cour->annees->pluck('annee')->join(', ') ?: '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('referentiel.cours.edit', $cour) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <form method="post" action="{{ route('referentiel.cours.destroy', $cour) }}" style="display:inline" onsubmit="return confirm('Supprimer ce cours ?')">
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
                            @include('partials.icon', ['n' => 'book', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun cours.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">
        {{ $cours->links() }}
    </div>
</div>
@endsection
