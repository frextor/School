@extends('layouts.app')

@section('title', 'Signatures')

@section('content')
    <h1>Signatures</h1>
    <a class="btn" href="{{ route('referentiel.signatures.create') }}">+ Nouvelle signature</a>

    <table>
        <thead>
            <tr>
                <th>Établissement</th>
                <th>Directeur</th>
                <th>Fonction</th>
                <th>Fichier</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($signatures as $signature)
                <tr>
                    <td>{{ $signature->etablissement?->nom_etablissement }}</td>
                    <td>{{ $signature->civilite }} {{ $signature->nom_directeur }}</td>
                    <td>{{ $signature->fonction }}</td>
                    <td>
                        @if ($signature->cheminFichier())
                            <img src="{{ Storage::disk('public')->url($signature->cheminFichier()) }}" style="max-height:40px">
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('referentiel.signatures.edit', $signature) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.signatures.destroy', $signature) }}" style="display:inline" onsubmit="return confirm('Supprimer cette signature ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune signature.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $signatures->links() }}
@endsection
