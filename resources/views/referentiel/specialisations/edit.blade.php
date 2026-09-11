@extends('layouts.app')

@section('title', 'Modifier spécialisation')

@section('content')
    <a href="{{ route('referentiel.specialisations.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $specialisation->nom_specialisation }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.specialisations.update', $specialisation) }}">
        @csrf
        @method('PUT')
        <label for="nom_specialisation">Nom</label>
        <input type="text" name="nom_specialisation" id="nom_specialisation" value="{{ old('nom_specialisation', $specialisation->nom_specialisation) }}" required>
        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
