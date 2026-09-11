@extends('layouts.app')

@section('title', 'Connexion administration')

@section('content')
    @include('auth._login-card', [
        'icon' => '🎓',
        'title' => 'Administration',
        'subtitle' => config('app.name').' — espace de gestion',
        'action' => route('admin.login.attempt'),
        'usernameLabel' => 'Identifiant',
        'remember' => true,
    ])
@endsection
