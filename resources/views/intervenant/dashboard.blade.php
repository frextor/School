@extends('layouts.app')

@section('title', 'Espace intervenant')

@section('content')
    <h1>Bienvenue {{ auth('intervenant')->user()->intervenant?->prenom }} {{ auth('intervenant')->user()->intervenant?->nom }}</h1>

    <p><a href="{{ route('recapitulatif.index') }}">Récapitulatif d'heures</a></p>

    <form method="post" action="{{ route('intervenant.logout') }}">
        @csrf
        <button type="submit">Se déconnecter</button>
    </form>
@endsection
