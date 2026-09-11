@extends('layouts.app')

@section('title', 'Configuration du site')

@section('content')
    <h1>Configuration du site</h1>

    <h2>Constantes</h2>
    <table>
        <thead><tr><th>Label</th><th>Valeur</th><th></th></tr></thead>
        <tbody>
            @forelse ($constants as $constant)
                <tr>
                    <td>{{ $constant->label }}</td>
                    <td>
                        <form method="post" action="{{ route('configuration.site.constants.update', $constant) }}" style="display:flex;gap:.5rem">
                            @csrf
                            @method('PUT')
                            <input type="text" name="value" value="{{ $constant->value }}">
                            <button type="submit">Enregistrer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="2">Aucune constante.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Sections du site</h2>
    <table>
        <thead><tr><th>Libellé</th><th>Actif</th><th></th></tr></thead>
        <tbody>
            @forelse ($accesses as $access)
                <tr>
                    <td>{{ $access->libelle }}</td>
                    <td>{{ $access->status ? 'Oui' : 'Non' }}</td>
                    <td>
                        <form method="post" action="{{ route('configuration.site.access.toggle', $access) }}">
                            @csrf
                            <button type="submit">{{ $access->status ? 'Désactiver' : 'Activer' }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">Aucune section.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
