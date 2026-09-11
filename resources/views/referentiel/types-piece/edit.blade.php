@extends('layouts.app')

@section('title', 'Modifier type de document')

@section('content')
    <a href="{{ route('referentiel.types-piece.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $type->type }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.types-piece.update', $type) }}">
        @csrf
        @method('PUT')
        <label for="type">Type</label>
        <input type="text" name="type" id="type" value="{{ old('type', $type->type) }}" required>

        <label><input type="checkbox" name="periode_associee" value="1" @checked(old('periode_associee', $type->periode_associee))> Période associée</label>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
