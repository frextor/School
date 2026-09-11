@extends('layouts.app')

@section('title', "Épreuves d'admission")

@section('content')
    <h1>Épreuves d'admission</h1>
    <a class="btn" href="{{ route('epreuves.create') }}">+ Nouvelle épreuve</a>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Lieu</th>
                <th>Effectif</th>
                <th>Formations</th>
                <th>Inscrits</th>
                <th>Résultats</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($epreuves as $epreuve)
                <tr>
                    <td>{{ $epreuve->date_epreuve->format('d/m/Y H:i') }}</td>
                    <td>{{ $epreuve->lieu }}</td>
                    <td>{{ $epreuve->effectif }}</td>
                    <td>{{ $epreuve->formations->pluck('niveau')->join(', ') ?: '-' }}</td>
                    <td>{{ $epreuve->inscriptions_count }}</td>
                    <td>{{ $epreuve->resultats_count }}</td>
                    <td>
                        <a href="{{ route('epreuves.edit', $epreuve) }}">Modifier</a>
                        <form method="post" action="{{ route('epreuves.destroy', $epreuve) }}" style="display:inline" onsubmit="return confirm('Supprimer cette épreuve ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Aucune épreuve.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $epreuves->links() }}
@endsection
