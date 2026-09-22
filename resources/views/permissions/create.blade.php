@extends('layouts.app')

@section('title', 'Nouvelle permission')

@section('content')
@php $permission = new \App\Models\Permission(); @endphp

<div class="crumb">
    <a href="{{ route('permissions.index') }}">Permissions</a>
    <span class="sep">/</span>
    <span class="current">Nouvelle permission</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouvelle permission</h1>
        <p class="page-sub">Un droit élémentaire, à accorder ensuite via un rôle.</p>
    </div>
</div>

<form method="post" action="{{ route('permissions.store') }}" class="form-page">
    @csrf
    @include('permissions._form', ['permission' => $permission])
    <div class="form-actions">
        <a href="{{ route('permissions.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer la permission</button>
    </div>
</form>
@endsection
