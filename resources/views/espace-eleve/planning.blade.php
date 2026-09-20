@extends('layouts.app')

@section('title', 'Mon emploi du temps')

@section('content')
@php
    $urlSemaine = fn ($lundi) => route('espace-eleve.planning', ['semaine' => $lundi->format('Y-m-d')]);
@endphp

<div class="page-head">
    <div>
        <h1>Mon emploi du temps</h1>
        <p class="page-sub">Les cours de ma classe, semaine par semaine.</p>
    </div>
</div>

@include('partials.calendrier-semaine', [
    'semaine' => $semaine,
    'debutSemaine' => $debutSemaine,
    'urlSemaine' => $urlSemaine,
    'vide' => 'Aucun cours cette semaine.',
])
@endsection
