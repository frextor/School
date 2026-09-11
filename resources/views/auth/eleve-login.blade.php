@extends('layouts.app')

@section('title', 'Mon espace personnel — Connexion')

@section('content')
    @include('auth._login-card', [
        'icon' => '🧑‍🎓',
        'title' => 'Mon espace personnel',
        'subtitle' => config('app.name').' — planning, résultats et suivi de scolarité',
        'action' => route('eleve.login.attempt'),
        'usernameLabel' => 'Identifiant',
    ])
@endsection
