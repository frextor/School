@extends('layouts.app')

@section('title', 'Mon espace personnel')

@section('content')
    <h1>Bienvenue {{ auth('eleve')->user()->eleve?->contact?->prenom }} {{ auth('eleve')->user()->eleve?->contact?->nom }}</h1>

    <form method="post" action="{{ route('eleve.logout') }}">
        @csrf
        <button type="submit">Se déconnecter</button>
    </form>
@endsection
