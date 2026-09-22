@extends('layouts.app')

@section('title', 'Administrateur · '.$admin->username)

@section('content')
<div class="crumb">
    <a href="{{ route('admins.index') }}">Administrateurs</a>
    <span class="sep">/</span>
    <span class="current">{{ $admin->nom }} {{ $admin->prenom }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $admin->nom }} {{ $admin->prenom }}</h1>
            <span class="badge badge-brand">{{ $admin->profil }}</span>
        </div>
        <p class="page-sub">{{ $admin->email }} · identifiant <code>{{ $admin->username }}</code></p>
    </div>
</div>

<form method="post" action="{{ route('admins.update', $admin) }}" enctype="multipart/form-data" class="form-page">
    @csrf
    @method('PUT')
    @include('admins._form', ['admin' => $admin])
    <div class="form-actions">
        <a href="{{ route('admins.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<form method="post" action="{{ route('admins.destroy', $admin) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer le compte de {{ $admin->nom }} {{ $admin->prenom }} ? Cette personne perdra tout accès.')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer ce compte
    </button>
</form>
@endsection
