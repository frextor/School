@extends('layouts.app')

@section('title', 'Nouvel établissement')

@section('content')
    <a href="{{ route('referentiel.etablissements.index') }}">&larr; Retour</a>
    <h1>Nouvel établissement</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.etablissements.store') }}">
        @csrf

        <label for="ville">Ville</label>
        <input type="text" name="ville" id="ville" value="{{ old('ville') }}" required>
        <p style="color:#666;font-size:0.85rem">Le nom affiché sera « {{ config('school.name') }} VILLE ».</p>

        <label for="code_ville">Code ville (2 lettres)</label>
        <input type="text" name="code_ville" id="code_ville" maxlength="2" value="{{ old('code_ville') }}" required>

        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}" required>

        <label><input type="checkbox" name="visible" value="1" @checked(old('visible'))> Visible</label>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
