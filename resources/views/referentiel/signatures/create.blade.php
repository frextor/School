@extends('layouts.app')

@section('title', 'Nouvelle signature')

@section('content')
<div class="crumb">
    <a href="{{ route('referentiel.signatures.index') }}">Signatures</a>
    <span class="sep">/</span>
    <span class="current">Nouvelle signature</span>
</div>

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $erreur){{ $erreur }} @endforeach</span>
    </div>
@endif

<div class="page-head">
    <div>
        <h1>Nouvelle signature</h1>
        <p class="page-sub">Le signataire des documents officiels d'un établissement, et sa signature scannée.</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.signatures.store') }}" enctype="multipart/form-data" class="sg-form-page">
    @csrf

    @include('referentiel.signatures._form', ['signature' => $signature])

    <div class="sg-pied">
        <a href="{{ route('referentiel.signatures.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer la signature</button>
    </div>
</form>
@endsection
