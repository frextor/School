@extends('layouts.app')

@section('title', 'Espace entreprise — Connexion')

@section('content')
    @include('auth._login-card', [
        'icon' => '🤝',
        'title' => 'Espace entreprise',
        'subtitle' => config('app.name').' — suivi des contrats et conventions',
        'action' => route('entreprise.login.attempt'),
        'usernameLabel' => 'Email',
    ])
@endsection
