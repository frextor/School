@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Permissions</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Permissions</h1>
            <span class="badge badge-brand">{{ $permissions->total() }}</span>
        </div>
        <p class="page-sub">L'unité de droit élémentaire : un écran, ou une action sur un écran. Les rôles en regroupent plusieurs.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('permissions.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouvelle permission
        </a>
    </div>
</div>

<form method="get" action="{{ route('permissions.index') }}" class="filter-card">
    <div class="filter-row">
        <div class="filter-search">
            @include('partials.icon', ['n' => 'search', 's' => 15, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:13px;top:12px'])
            <input type="text" name="recherche" value="{{ request('recherche') }}" placeholder="Rechercher une permission">
        </div>
        <button type="submit" class="btn">Rechercher</button>
        @if (request('recherche'))
            <a href="{{ route('permissions.index') }}" class="btn btn-ghost">Réinitialiser</a>
        @endif
    </div>
</form>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Permission</th>
                    <th>Route</th>
                    <th style="width:180px">Rattachée à</th>
                    <th style="width:120px">Créée le</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($permissions as $permission)
                    <tr>
                        <td class="cell-name">{{ $permission->nom_permission }}</td>
                        <td><code>{{ Str::limit($permission->route, 70) }}</code></td>
                        <td>
                            @if ($permission->parent)
                                <span class="badge">{{ $permission->parent->nom_permission }}</span>
                            @else
                                <span style="color:var(--faint)">—</span>
                            @endif
                        </td>
                        <td>{{ $permission->date_creation?->format('d/m/Y') ?? '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('permissions.edit', $permission) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                            </a>
                            <form method="post" action="{{ route('permissions.destroy', $permission) }}" class="inline-form"
                                  onsubmit="return confirm('Supprimer la permission « {{ $permission->nom_permission }} » ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="row-btn is-danger" title="Supprimer">
                                    @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#b91c1c', 'w' => 2])
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-cell">
                            @include('partials.icon', ['n' => 'lock', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune permission ne correspond à cette recherche.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">{{ $permissions->links() }}</div>
</div>
@endsection
