@extends('layouts.app')

@section('title', 'Nouvelle permission')

@section('content')
    <a href="{{ route('permissions.index') }}">&larr; Retour</a>
    <h1>Nouvelle permission</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('permissions.store') }}">
        @csrf

        <label for="nom_permission">Nom</label>
        <input type="text" name="nom_permission" id="nom_permission" value="{{ old('nom_permission') }}" required>

        <label for="route">Route</label>
        <input type="text" name="route" id="route" value="{{ old('route') }}" required>

        <label for="ParentID">Permission parente</label>
        <select name="ParentID" id="ParentID">
            <option value="">-- Aucune --</option>
            @foreach ($permissionsParentes as $parente)
                <option value="{{ $parente->id_permission }}" @selected(old('ParentID') == $parente->id_permission)>{{ $parente->nom_permission }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
