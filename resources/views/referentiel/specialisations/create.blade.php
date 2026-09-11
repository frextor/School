@extends('layouts.app')

@section('title', 'Nouvelle spécialisation')

@section('content')
    <a href="{{ route('referentiel.specialisations.index') }}">&larr; Retour</a>
    <h1>Nouvelle spécialisation</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.specialisations.store') }}">
        @csrf
        <label for="nom_specialisation">Nom</label>
        <input type="text" name="nom_specialisation" id="nom_specialisation" value="{{ old('nom_specialisation') }}" required>
        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
