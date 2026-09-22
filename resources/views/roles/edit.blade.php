@extends('layouts.app')

@section('title', 'Rôle · '.$role->nom_role)

@section('content')
@php $choisies = collect(old('permissions', $selectedPermissions))->map(fn ($v) => (int) $v); @endphp

<div class="crumb">
    <a href="{{ route('roles.index') }}">Rôles</a>
    <span class="sep">/</span>
    <span class="current">{{ $role->nom_role }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $role->nom_role }}</h1>
            @if ($role->locked)
                <span class="badge">verrouillé</span>
            @endif
        </div>
        <p class="page-sub">{{ $choisies->count() }} permission{{ $choisies->count() > 1 ? 's' : '' }} accordée{{ $choisies->count() > 1 ? 's' : '' }}</p>
    </div>
</div>

<form method="post" action="{{ route('roles.update', $role) }}" class="form-page">
    @csrf
    @method('PUT')
    @include('roles._form', ['role' => $role, 'choisies' => $choisies])
    <div class="form-actions">
        <a href="{{ route('roles.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

@unless ($role->locked)
    <form method="post" action="{{ route('roles.destroy', $role) }}" class="danger-zone form-page"
          onsubmit="return confirm('Supprimer le rôle « {{ $role->nom_role }} » ? Les administrateurs qui le portent perdront ces droits.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-ghost is-danger">
            @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer ce rôle
        </button>
    </form>
@endunless
@endsection
