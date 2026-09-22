@extends('layouts.app')

@section('title', 'Permission · '.$permission->nom_permission)

@section('content')
<div class="crumb">
    <a href="{{ route('permissions.index') }}">Permissions</a>
    <span class="sep">/</span>
    <span class="current">{{ $permission->nom_permission }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>{{ $permission->nom_permission }}</h1>
        <p class="page-sub">
            <code>{{ $permission->route }}</code>
            @if ($permission->parent) · rattachée à {{ $permission->parent->nom_permission }} @endif
        </p>
    </div>
</div>

<form method="post" action="{{ route('permissions.update', $permission) }}" class="form-page">
    @csrf
    @method('PUT')
    @include('permissions._form', ['permission' => $permission])
    <div class="form-actions">
        <a href="{{ route('permissions.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<form method="post" action="{{ route('permissions.destroy', $permission) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer la permission « {{ $permission->nom_permission }} » ?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer cette permission
    </button>
</form>
@endsection
