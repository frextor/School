@extends('layouts.app')

@section('title', 'Signature · '.$signature->nom_directeur)

@section('content')
<div class="crumb">
    <a href="{{ route('referentiel.signatures.index') }}">Signatures</a>
    <span class="sep">/</span>
    <span class="current">{{ $signature->nom_directeur }}</span>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $erreur){{ $erreur }} @endforeach</span>
    </div>
@endif

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $signature->civilite === 'M' ? 'M.' : $signature->civilite }} {{ $signature->nom_directeur }}</h1>
            @if ($signature->principal)
                <span class="badge badge-brand">Signature principale</span>
            @endif
        </div>
        <p class="page-sub">{{ $signature->fonction }} · {{ $signature->etablissement?->nom_etablissement ?: 'Établissement non précisé' }}</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.signatures.update', $signature) }}" enctype="multipart/form-data" class="sg-form-page">
    @csrf
    @method('PUT')

    @include('referentiel.signatures._form', ['signature' => $signature])

    <div class="sg-pied">
        <a href="{{ route('referentiel.signatures.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<form method="post" action="{{ route('referentiel.signatures.destroy', $signature) }}" class="sg-suppr-bloc"
      onsubmit="return confirm('Supprimer la signature de {{ $signature->nom_directeur }} ?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer cette signature
    </button>
</form>

<style>
    .sg-suppr-bloc { max-width: 820px; margin: 18px 0 0; }
</style>
@endsection
