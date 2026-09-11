@extends('layouts.app')

@section('title', 'Mon planning')

@section('content')
    <h1>Mon planning</h1>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Cours</th>
                <th>Établissement</th>
                <th>Classe</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($creneaux as $creneau)
                <tr>
                    <td>{{ $creneau->date_debut->format('d/m/Y H:i') }} — {{ $creneau->date_fin->format('H:i') }}</td>
                    <td>{{ $creneau->cours?->nom_cours }}</td>
                    <td>{{ $creneau->etablissement?->nom_etablissement }}</td>
                    <td>{{ $creneau->classe?->classe ?? $creneau->id_classe }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Aucun cours planifié.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
