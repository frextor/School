@extends('layouts.app')

@section('title', 'Nouveau type de document')

@section('content')
    <a href="{{ route('referentiel.types-piece.index') }}">&larr; Retour</a>
    <h1>Nouveau type de document</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.types-piece.store') }}">
        @csrf
        <label for="type">Type</label>
        <input type="text" name="type" id="type" value="{{ old('type') }}" required>

        <label><input type="checkbox" name="periode_associee" value="1" @checked(old('periode_associee'))> Période associée</label>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
