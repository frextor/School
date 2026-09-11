@extends('layouts.app')

@section('title', 'Trombinoscope')

@section('content')
    <a href="{{ route('espace-intervenant.classes') }}">&larr; Retour</a>
    <h1>Élèves — {{ $classe->classe }}</h1>

    <table>
        <thead><tr><th>Nom</th><th>Email</th></tr></thead>
        <tbody>
            @forelse ($eleves as $eleve)
                <tr>
                    <td>{{ $eleve->contact?->nom_complet }}</td>
                    <td>{{ $eleve->contact?->email }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Aucun élève dans cette classe.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
