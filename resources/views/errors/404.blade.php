@extends('layouts.app')

@section('title', 'Page non trouvée')

@section('content')
    <div style="text-align:center; margin-top:4rem">
        <h1>Page non trouvée</h1>
        <p>Nous sommes désolés, la page que vous avez demandée est introuvable.</p>
        <a class="btn" href="{{ route('admin.dashboard') }}">Retourner au tableau de bord</a>
    </div>
@endsection
