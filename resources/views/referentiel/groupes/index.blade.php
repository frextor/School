@extends('layouts.app')

@section('title', 'Groupes')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Groupes</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Groupes</h1>
            <span class="badge badge-brand">{{ number_format($groupes->total(), 0, ',', ' ') }} groupes</span>
        </div>
        <p class="page-sub">Groupes de répartition des élèves.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('referentiel.groupes.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouveau groupe
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Créé le</th>
                    <th>Modifié le</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($groupes as $groupe)
                    <tr>
                        <td class="cell-name">{{ $groupe->nom_groupe }}</td>
                        <td class="num">{{ $groupe->date_creation?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="num">{{ $groupe->date_modification?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('referentiel.groupes.edit', $groupe) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <form method="post" action="{{ route('referentiel.groupes.destroy', $groupe) }}" style="display:inline" onsubmit="return confirm('Supprimer ce groupe ?')">
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
                            @include('partials.icon', ['n' => 'group', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun groupe.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">
        {{ $groupes->links() }}
    </div>
</div>
@endsection
