@extends('layouts.app')

@section('title', 'Créneau · '.($creneau->cours?->nom_cours ?? 'emploi du temps'))

@section('content')
<div class="crumb">
    <a href="{{ route('planning.index') }}">Emploi du temps</a>
    <span class="sep">/</span>
    <span class="current">{{ $creneau->cours?->nom_cours ?? 'Créneau' }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $creneau->cours?->nom_cours ?? 'Créneau' }}</h1>
            @if ($creneau->classe)
                <span class="badge badge-brand">{{ $creneau->classe->classe }}</span>
            @endif
        </div>
        <p class="page-sub">
            {{ \Illuminate\Support\Carbon::parse($creneau->date_debut)->translatedFormat('l j F Y, H\hi') }}
            @if ($creneau->intervenant) · {{ $creneau->intervenant->nom }} {{ $creneau->intervenant->prenom }} @endif
        </p>
    </div>
</div>

<form method="post" action="{{ route('planning.update', $creneau) }}" class="form-page">
    @csrf
    @method('PUT')
    @include('planning._form', ['creneau' => $creneau])
    <div class="form-actions">
        <a href="{{ route('planning.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<form method="post" action="{{ route('planning.destroy', $creneau) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer ce créneau de l\'emploi du temps ?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer ce créneau
    </button>
</form>
@endsection
