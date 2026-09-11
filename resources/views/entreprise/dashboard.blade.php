@extends('layouts.app')

@section('title', 'Espace entreprise')

@section('content')
    <h1>Bienvenue {{ $entreprise?->nom_entreprise }}</h1>

    <p><a href="{{ route('entreprise.informations') }}">Mes informations</a></p>

    <form method="post" action="{{ route('entreprise.logout') }}">
        @csrf
        <button type="submit">Se déconnecter</button>
    </form>
@endsection
