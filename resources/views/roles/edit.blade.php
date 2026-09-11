@extends('layouts.app')

@section('title', 'Modifier rôle')

@section('content')
    <a href="{{ route('roles.index') }}">&larr; Retour</a>
    <h1>Modifier le rôle « {{ $role->nom_role }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('roles.update', $role) }}">
        @csrf
        @method('PUT')

        <label for="nom_role">Nom du rôle</label>
        <input type="text" name="nom_role" id="nom_role" value="{{ old('nom_role', $role->nom_role) }}" required>

        <label>Permissions</label>
        @foreach ($permissions as $permission)
            <label style="font-weight:normal">
                <input type="checkbox" name="permissions[]" value="{{ $permission->id_permission }}"
                    @checked(collect(old('permissions', $selectedPermissions))->contains($permission->id_permission))>
                {{ $permission->nom_permission }}
            </label>
        @endforeach

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
