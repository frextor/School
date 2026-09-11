@extends('layouts.app')

@section('title', 'Modifier groupe')

@section('content')
    <a href="{{ route('referentiel.groupes.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $groupe->nom_groupe }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.groupes.update', $groupe) }}">
        @csrf
        @method('PUT')
        <label for="nom_groupe">Nom du groupe</label>
        <input type="text" name="nom_groupe" id="nom_groupe" value="{{ old('nom_groupe', $groupe->nom_groupe) }}" required>
        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
