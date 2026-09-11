@extends('layouts.app')

@section('title', 'Modifier permission')

@section('content')
    <a href="{{ route('permissions.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $permission->nom_permission }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('permissions.update', $permission) }}">
        @csrf
        @method('PUT')

        <label for="nom_permission">Nom</label>
        <input type="text" name="nom_permission" id="nom_permission" value="{{ old('nom_permission', $permission->nom_permission) }}" required>

        <label for="route">Route</label>
        <input type="text" name="route" id="route" value="{{ old('route', $permission->route) }}" required>

        <label for="ParentID">Permission parente</label>
        <select name="ParentID" id="ParentID">
            <option value="">-- Aucune --</option>
            @foreach ($permissionsParentes as $parente)
                <option value="{{ $parente->id_permission }}" @selected(old('ParentID', $permission->ParentID) == $parente->id_permission)>{{ $parente->nom_permission }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
