@extends('layouts.app')

@section('title', 'Nouveau rôle')

@section('content')
@php
    $role = new \App\Models\Role();
    $choisies = collect(old('permissions', []))->map(fn ($v) => (int) $v);
@endphp

<div class="crumb">
    <a href="{{ route('roles.index') }}">Rôles</a>
    <span class="sep">/</span>
    <span class="current">Nouveau rôle</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouveau rôle</h1>
        <p class="page-sub">Un ensemble de permissions à accorder d'un bloc aux administrateurs qui portent ce rôle.</p>
    </div>
</div>

<form method="post" action="{{ route('roles.store') }}" class="form-page">
    @csrf
    @include('roles._form', ['role' => $role, 'choisies' => $choisies])
    <div class="form-actions">
        <a href="{{ route('roles.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer le rôle</button>
    </div>
</form>
@endsection
