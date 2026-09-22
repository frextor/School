@extends('layouts.app')

@section('title', 'Nouvel administrateur')

@section('content')
@php $admin = new \App\Models\Admin(); @endphp

<div class="crumb">
    <a href="{{ route('admins.index') }}">Administrateurs</a>
    <span class="sep">/</span>
    <span class="current">Nouvel administrateur</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouvel administrateur</h1>
        <p class="page-sub">Un compte d'accès à l'administration, avec son rôle et ses établissements.</p>
    </div>
</div>

<form method="post" action="{{ route('admins.store') }}" enctype="multipart/form-data" class="form-page">
    @csrf
    @include('admins._form', ['admin' => $admin])
    <div class="form-actions">
        <a href="{{ route('admins.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer le compte</button>
    </div>
</form>
@endsection
