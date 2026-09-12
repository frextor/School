@extends('layouts.app')

@section('title', 'Classes')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Classes</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Classes</h1>
            <span class="badge badge-brand">{{ number_format($classes->total(), 0, ',', ' ') }} classes</span>
        </div>
        <p class="page-sub">Classes par niveau et établissement.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('referentiel.classes.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouvelle classe
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Classe</th>
                    <th>Niveau</th>
                    <th>Établissement</th>
                    <th>Couleur</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($classes as $classe)
                    <tr>
                        <td class="cell-name">{{ $classe->classe }}</td>
                        <td>{{ $classe->niveau?->nom_niveau ?? '—' }}</td>
                        <td>{{ $classe->etablissement?->nom_etablissement ?? '—' }}</td>
                        <td><span style="display:inline-block;width:16px;height:16px;border-radius:4px;background:{{ $classe->couleur }}"></span></td>
                        <td class="col-actions">
                            <a href="{{ route('referentiel.classes.edit', $classe) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </a>
                            <form method="post" action="{{ route('referentiel.classes.destroy', $classe) }}" style="display:inline" onsubmit="return confirm('Supprimer cette classe ?')">
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
                            @include('partials.icon', ['n' => 'tag', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune classe.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">
        {{ $classes->links() }}
    </div>
</div>
@endsection
