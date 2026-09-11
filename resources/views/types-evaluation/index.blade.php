@extends('layouts.app')

@section('title', "Types d'évaluation")

@section('content')
    <h1>Types d'évaluation</h1>

    <form method="post" action="{{ route('types-evaluation.store') }}">
        @csrf
        <input type="text" name="type" placeholder="Nouveau type" required>
        <button type="submit">Ajouter</button>
    </form>

    <table>
        <thead><tr><th>Type</th><th></th></tr></thead>
        <tbody>
            @forelse ($types as $type)
                <tr>
                    <td>{{ $type->type }}</td>
                    <td>
                        <form method="post" action="{{ route('types-evaluation.destroy', $type) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="2">Aucun type.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $types->links() }}
@endsection
