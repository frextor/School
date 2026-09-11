@extends('layouts.app')

@section('title', 'Tâches / Relances')

@section('content')
    <h1>Tâches / Relances</h1>

    <table>
        <thead>
            <tr>
                <th>Contact</th>
                <th>Type</th>
                <th>Commentaire</th>
                <th>Échéance</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($taches as $tache)
                <tr>
                    <td>{{ $tache->contact?->nom_complet }}</td>
                    <td>{{ $tache->type?->libelle ?? '-' }}</td>
                    <td>{{ Str::limit($tache->commentaire, 60) }}</td>
                    <td>{{ $tache->deadline?->format('d/m/Y H:i') }}</td>
                    <td>
                        <form method="post" action="{{ route('taches.close', $tache) }}" onsubmit="return confirm('Clôturer cette tâche ?')" style="display:inline">
                            @csrf
                            <button type="submit">Clôturer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune tâche ouverte.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $taches->links() }}
@endsection
