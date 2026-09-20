@extends('layouts.app')

@section('title', 'Mon emploi du temps')

@section('content')
@php
    $urlSemaine = fn ($lundi) => route('espace-intervenant.planning', ['semaine' => $lundi->format('Y-m-d')]);
@endphp

<div class="page-head">
    <div>
        <h1>Mon emploi du temps</h1>
        <p class="page-sub">Mes cours de la semaine, avec la classe et la salle.</p>
    </div>
    @if (Route::has('espace-intervenant.classes'))
        <div class="page-actions">
            <a class="btn btn-ghost" href="{{ route('espace-intervenant.classes') }}">Mes classes</a>
        </div>
    @endif
</div>

@include('partials.calendrier-semaine', [
    'semaine' => $semaine,
    'debutSemaine' => $debutSemaine,
    'urlSemaine' => $urlSemaine,
    'vide' => 'Aucun cours planifié cette semaine.',
])
@endsection
