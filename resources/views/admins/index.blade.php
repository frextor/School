@extends('layouts.app')

@section('title', 'Administrateurs')

@section('content')
    <h1>Administrateurs</h1>
    <a class="btn" href="{{ route('admins.create') }}">+ Nouvel administrateur</a>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Identifiant</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Service</th>
                <th>Établissements</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($admins as $admin)
                <tr>
                    <td>{{ $admin->nom }} {{ $admin->prenom }}</td>
                    <td>{{ $admin->username }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ $admin->profil }}</td>
                    <td>{{ $admin->service?->libelle }}</td>
                    <td>{{ $admin->etablissements->pluck('nom_etablissement')->join(', ') ?: '-' }}</td>
                    <td>
                        <a href="{{ route('admins.edit', $admin) }}">Modifier</a>
                        <form method="post" action="{{ route('admins.destroy', $admin) }}" style="display:inline" onsubmit="return confirm('Supprimer cet administrateur ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Aucun administrateur.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $admins->links() }}
@endsection
