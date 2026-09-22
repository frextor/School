@extends('layouts.app')

@section('title', 'Salle · '.$salle->nom_salle)

@section('content')
<div class="crumb">
    <a href="{{ route('salles.index') }}">Salles</a>
    <span class="sep">/</span>
    <span class="current">{{ $salle->nom_salle }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $salle->nom_salle }}</h1>
            <span class="badge">{{ $salle->code_salle }}</span>
        </div>
        <p class="page-sub">{{ $salle->etablissement?->nom_etablissement }} · {{ $salle->nombre_place }} places</p>
    </div>
</div>

<form method="post" action="{{ route('salles.update', $salle) }}" class="form-page">
    @csrf
    @method('PUT')
    @include('salles._form', ['salle' => $salle])
    <div class="form-actions">
        <a href="{{ route('salles.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<form method="post" action="{{ route('salles.destroy', $salle) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer la salle « {{ $salle->nom_salle }} » ?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer cette salle
    </button>
</form>
@endsection
