@extends('layouts.app')

@section('title', 'Mes classes')

@section('content')
    <h1>Mes classes</h1>

    <table>
        <thead><tr><th>Classe</th><th>Niveau</th><th></th></tr></thead>
        <tbody>
            @forelse ($classes as $classe)
                <tr>
                    <td>{{ $classe->classe }}</td>
                    <td>{{ $classe->niveau?->nom_niveau }}</td>
                    <td><a href="{{ route('espace-intervenant.roster', $classe) }}">Voir les élèves</a></td>
                </tr>
            @empty
                <tr><td colspan="3">Aucune classe.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
