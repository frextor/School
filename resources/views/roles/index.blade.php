@extends('layouts.app')

@section('title', 'Rôles')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Rôles</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Rôles</h1>
            <span class="badge badge-brand">{{ $roles->total() }} rôle{{ $roles->total() > 1 ? 's' : '' }}</span>
        </div>
        <p class="page-sub">Un rôle réunit les permissions accordées à un groupe d'administrateurs : direction, secrétariat, comptabilité.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('roles.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouveau rôle
        </a>
    </div>
</div>

<form method="get" action="{{ route('roles.index') }}" class="filter-card">
    <div class="filter-row">
        <div class="filter-search">
            @include('partials.icon', ['n' => 'search', 's' => 15, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:13px;top:12px'])
            <input type="text" name="recherche" value="{{ request('recherche') }}" placeholder="Rechercher un rôle">
        </div>
        <button type="submit" class="btn">Rechercher</button>
        @if (request('recherche'))
            <a href="{{ route('roles.index') }}" class="btn btn-ghost">Réinitialiser</a>
        @endif
    </div>
</form>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rôle</th>
                    <th style="width:200px">Nom machine</th>
                    <th style="width:150px">Permissions</th>
                    <th style="width:130px">Créé le</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td class="cell-name">
                            {{ $role->nom_role }}
                            @if ($role->locked)
                                {{-- Un rôle verrouillé structure les droits : le supprimer
                                     priverait d'un coup tous ses porteurs de leurs accès. --}}
                                <span class="badge" title="Rôle système, non supprimable">verrouillé</span>
                            @endif
                        </td>
                        <td><code>{{ $role->nom_machine }}</code></td>
                        <td>
                            @if ($role->permissions_count)
                                <span class="badge badge-brand">{{ $role->permissions_count }}</span>
                            @else
                                <span class="badge">aucune</span>
                            @endif
                        </td>
                        <td>{{ $role->date_creation?->format('d/m/Y') ?? '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('roles.edit', $role) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                            </a>
                            @unless ($role->locked)
                                <form method="post" action="{{ route('roles.destroy', $role) }}" class="inline-form"
                                      onsubmit="return confirm('Supprimer le rôle « {{ $role->nom_role }} » ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="row-btn is-danger" title="Supprimer">
                                        @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#b91c1c', 'w' => 2])
                                    </button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-cell">
                            @include('partials.icon', ['n' => 'shield', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun rôle ne correspond à cette recherche.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">{{ $roles->links() }}</div>
</div>
@endsection
