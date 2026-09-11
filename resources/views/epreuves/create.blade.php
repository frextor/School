@extends('layouts.app')

@section('title', 'Nouvelle épreuve')

@section('content')
    <a href="{{ route('epreuves.index') }}">&larr; Retour</a>
    <h1>Nouvelle épreuve d'admission</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('epreuves.store') }}">
        @csrf

        <label for="date">Date</label>
        <input type="date" name="date" id="date" value="{{ old('date') }}" required>

        <label for="heure">Heure</label>
        <input type="time" name="heure" id="heure" value="{{ old('heure') }}" required>

        <label for="lieu">Lieu</label>
        <input type="text" name="lieu" id="lieu" maxlength="16" value="{{ old('lieu') }}" required>

        <label for="effectif">Effectif max</label>
        <input type="number" name="effectif" id="effectif" value="{{ old('effectif', 20) }}" required>

        <label><input type="checkbox" name="distanciel" value="1" @checked(old('distanciel'))> Distanciel</label>

        <label for="url_distanciel">URL distanciel</label>
        <input type="url" name="url_distanciel" id="url_distanciel" value="{{ old('url_distanciel') }}">

        <label for="id_formation">Formations concernées</label>
        <select name="id_formation[]" id="id_formation" multiple size="6">
            @foreach ($formations as $formation)
                <option value="{{ $formation->id_formation }}" @selected(collect(old('id_formation', []))->contains($formation->id_formation))>{{ $formation->niveau }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
