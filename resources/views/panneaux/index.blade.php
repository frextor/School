@extends('layouts.app')

@section('title', 'Panneaux lumineux')

@section('content')
    <h1>Panneaux lumineux</h1>
    <a class="btn" href="{{ route('panneaux.create') }}">+ Nouveau panneau</a>

    <table>
        <thead>
            <tr>
                <th>Identifiant</th>
                <th>Titre</th>
                <th>Établissement</th>
                <th>Plage horaire (h)</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($panneaux as $panneau)
                <tr>
                    <td>{{ $panneau->identifiant_panneaux }}</td>
                    <td>{{ $panneau->titre }}</td>
                    <td>{{ $panneau->etablissement?->nom_etablissement }}</td>
                    <td>{{ $panneau->plage_horaire }}</td>
                    <td>
                        <a href="{{ route('panneaux.edit', $panneau) }}">Modifier</a>
                        <form method="post" action="{{ route('panneaux.destroy', $panneau) }}" style="display:inline" onsubmit="return confirm('Supprimer ce panneau ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun panneau.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $panneaux->links() }}
@endsection
