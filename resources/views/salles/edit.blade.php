@extends('layouts.app')

@section('title', 'Modifier salle')

@section('content')
    <a href="{{ route('salles.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $salle->nom_salle }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="post" action="{{ route('salles.update', $salle) }}">
        @csrf
        @method('PUT')
        <label for="code_salle">Code</label>
        <input type="text" name="code_salle" id="code_salle" maxlength="5" value="{{ old('code_salle', $salle->code_salle) }}" required>

        <label for="nom_salle">Nom</label>
        <input type="text" name="nom_salle" id="nom_salle" value="{{ old('nom_salle', $salle->nom_salle) }}" required>

        <label for="nombre_place">Nombre de places</label>
        <input type="number" name="nombre_place" id="nombre_place" value="{{ old('nombre_place', $salle->nombre_place) }}" required>

        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" required>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $salle->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem"><button type="submit" class="btn">Enregistrer</button></p>
    </form>
@endsection
