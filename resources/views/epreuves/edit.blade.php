@extends('layouts.app')

@section('title', "Modifier l'épreuve")

@section('content')
    <a href="{{ route('epreuves.index') }}">&larr; Retour</a>
    <h1>Modifier l'épreuve du {{ $epreuve->date_epreuve->format('d/m/Y') }}</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('epreuves.update', $epreuve) }}">
        @csrf
        @method('PUT')

        <label for="date">Date</label>
        <input type="date" name="date" id="date" value="{{ old('date', $epreuve->date_epreuve->format('Y-m-d')) }}" required>

        <label for="heure">Heure</label>
        <input type="time" name="heure" id="heure" value="{{ old('heure', $epreuve->date_epreuve->format('H:i')) }}" required>

        <label for="lieu">Lieu</label>
        <input type="text" name="lieu" id="lieu" maxlength="16" value="{{ old('lieu', $epreuve->lieu) }}" required>

        <label for="effectif">Effectif max</label>
        <input type="number" name="effectif" id="effectif" value="{{ old('effectif', $epreuve->effectif) }}" required>

        <label><input type="checkbox" name="distanciel" value="1" @checked(old('distanciel', $epreuve->distanciel))> Distanciel</label>

        <label for="url_distanciel">URL distanciel</label>
        <input type="url" name="url_distanciel" id="url_distanciel" value="{{ old('url_distanciel', $epreuve->url_distanciel) }}">

        @php $formationsSelectionnees = old('id_formation', $epreuve->formations->pluck('id_formation')->all()); @endphp
        <label for="id_formation">Formations concernées</label>
        <select name="id_formation[]" id="id_formation" multiple size="6">
            @foreach ($formations as $formation)
                <option value="{{ $formation->id_formation }}" @selected(collect($formationsSelectionnees)->contains($formation->id_formation))>{{ $formation->niveau }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
