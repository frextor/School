@extends('layouts.app')

@section('title', 'Mon planning')

@section('content')
    <h1>Mon planning</h1>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Cours</th>
                <th>Intervenant</th>
                <th>Salle</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($creneaux as $creneau)
                <tr>
                    <td>{{ $creneau->date_debut->format('d/m/Y H:i') }} — {{ $creneau->date_fin->format('H:i') }}</td>
                    <td>{{ $creneau->cours?->nom_cours }}</td>
                    <td>{{ $creneau->intervenant?->nom }} {{ $creneau->intervenant?->prenom }}</td>
                    <td>{{ $creneau->id_salle ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Aucun cours à venir.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
