@extends('layouts.app')

@section('title', 'Nouveau groupe')

@section('content')
    <a href="{{ route('referentiel.groupes.index') }}">&larr; Retour</a>
    <h1>Nouveau groupe</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.groupes.store') }}">
        @csrf
        <label for="nom_groupe">Nom du groupe</label>
        <input type="text" name="nom_groupe" id="nom_groupe" value="{{ old('nom_groupe') }}" required>
        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
