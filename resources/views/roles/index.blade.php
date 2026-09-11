@extends('layouts.app')

@section('title', 'Rôles')

@section('content')
    <h1>Rôles</h1>
    <a class="btn" href="{{ route('roles.create') }}">+ Nouveau rôle</a>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Nom machine</th>
                <th>Permissions</th>
                <th>Créé le</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($roles as $role)
                <tr>
                    <td>{{ $role->nom_role }}</td>
                    <td>{{ $role->nom_machine }}</td>
                    <td>{{ $role->permissions_count }}</td>
                    <td>{{ $role->date_creation?->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('roles.edit', $role) }}">Modifier</a>
                        @unless ($role->locked)
                            <form method="post" action="{{ route('roles.destroy', $role) }}" style="display:inline" onsubmit="return confirm('Supprimer ce rôle ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Supprimer</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun rôle.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $roles->links() }}
@endsection
