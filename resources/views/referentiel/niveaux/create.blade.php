@extends('layouts.app')

@section('title', 'Nouveau niveau')

@section('content')
    <a href="{{ route('referentiel.niveaux.index') }}">&larr; Retour</a>
    <h1>Nouveau niveau</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.niveaux.store') }}">
        @csrf

        <label for="code_niveau">Code</label>
        <input type="text" name="code_niveau" id="code_niveau" value="{{ old('code_niveau') }}" required>

        <label for="nom_niveau">Nom</label>
        <input type="text" name="nom_niveau" id="nom_niveau" value="{{ old('nom_niveau') }}" required>

        <label for="id_formation">Formation</label>
        <select name="id_formation" id="id_formation" required>
            @foreach ($formations as $formation)
                <option value="{{ $formation->id_formation }}" @selected(old('id_formation') == $formation->id_formation)>{{ $formation->niveau }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
