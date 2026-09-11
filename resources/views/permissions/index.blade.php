@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
    <h1>Permissions</h1>
    <a class="btn" href="{{ route('permissions.create') }}">+ Nouvelle permission</a>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Route</th>
                <th>Parent</th>
                <th>Créée le</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permissions as $permission)
                <tr>
                    <td>{{ $permission->nom_permission }}</td>
                    <td>{{ Str::limit($permission->route, 60) }}</td>
                    <td>{{ $permission->parent?->nom_permission ?? '-' }}</td>
                    <td>{{ $permission->date_creation?->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('permissions.edit', $permission) }}">Modifier</a>
                        <form method="post" action="{{ route('permissions.destroy', $permission) }}" style="display:inline" onsubmit="return confirm('Supprimer cette permission ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune permission.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $permissions->links() }}
@endsection
