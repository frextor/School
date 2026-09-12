@extends('layouts.app')

@section('title', 'Connexion administration')

@section('content')
    @include('auth._login-card', [
        'iconName' => 'lock',
        'title' => 'Administration',
        'subtitle' => config('app.name').' — espace de gestion réservé aux administrateurs.',
        'action' => route('admin.login.attempt'),
        'usernameLabel' => 'Identifiant',
        'remember' => true,
    ])
@endsection
