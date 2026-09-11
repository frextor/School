@extends('layouts.app')

@section('title', 'Modifier — '.ucfirst($type))

@section('content')
    <a href="{{ route('referentiel-vacances.index', $type) }}">&larr; Retour</a>
    <h1>Modifier « {{ $item->titre }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel-vacances.update', [$type, $item]) }}">
        @csrf
        @method('PUT')
        <label for="titre">Titre</label>
        <input type="text" name="titre" id="titre" value="{{ old('titre', $item->titre) }}" required>

        <label for="date_debut">Début</label>
        <input type="datetime-local" name="date_debut" id="date_debut" value="{{ old('date_debut', $item->date_debut?->format('Y-m-d\TH:i')) }}" required>

        <label for="date_fin">Fin</label>
        <input type="datetime-local" name="date_fin" id="date_fin" value="{{ old('date_fin', $item->date_fin?->format('Y-m-d\TH:i')) }}" required>

        <label for="id_etablissement">Établissement (optionnel)</label>
        <select name="id_etablissement" id="id_etablissement">
            <option value="">-- Tous --</option>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $item->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem"><button type="submit" class="btn">Enregistrer</button></p>
    </form>
@endsection
