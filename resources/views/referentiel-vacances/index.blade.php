@extends('layouts.app')

@section('title', 'Calendrier — '.ucfirst($type))

@section('content')
    <h1>{{ ucfirst($type) }}</h1>
    <a class="btn" href="{{ route('referentiel-vacances.create', $type) }}">+ Nouveau</a>

    <table>
        <thead><tr><th>Titre</th><th>Début</th><th>Fin</th><th></th></tr></thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->titre }}</td>
                    <td>{{ $item->date_debut?->format('d/m/Y H:i') }}</td>
                    <td>{{ $item->date_fin?->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('referentiel-vacances.edit', [$type, $item]) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel-vacances.destroy', [$type, $item]) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Aucun élément.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $items->links() }}
@endsection
