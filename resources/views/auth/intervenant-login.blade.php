@extends('layouts.app')

@section('title', 'Espace intervenant — Connexion')

@section('content')
    @include('auth._login-card', [
        'iconName' => 'school',
        'title' => 'Espace intervenant',
        'subtitle' => config('app.name').' — planning, classes et récapitulatif d\'heures',
        'action' => route('intervenant.login.attempt'),
        'usernameLabel' => 'Email',
        'footer' => view('auth._footer-link', [
            'text' => 'Pas encore de compte ?',
            'label' => 'Devenir intervenant',
            'route' => route('intervenant-inscription.create'),
        ]),
    ])
@endsection
