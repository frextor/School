@extends('layouts.app')

@section('title', 'Nouveau panneau')

@section('content')
@php $panneau = new \App\Models\PanneauLumineux(); @endphp

<div class="crumb">
    <a href="{{ route('panneaux.index') }}">Panneaux d'affichage</a>
    <span class="sep">/</span>
    <span class="current">Nouveau panneau</span>
</div>

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

<div class="page-head">
    <div>
        <h1>Nouveau panneau</h1>
        <p class="page-sub">Un panneau, c'est un écran de couloir. Après enregistrement, son adresse s'affiche : ouvrez-la une fois sur l'écran concerné.</p>
    </div>
</div>

<form method="post" action="{{ route('panneaux.store') }}" class="pn-form">
    @csrf

    @include('panneaux._form', ['panneau' => $panneau])

    <div class="pn-pied">
        <a href="{{ route('panneaux.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer le panneau</button>
    </div>
</form>
@endsection
