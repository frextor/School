@extends('layouts.app')

@section('title', 'Bulletins')

@section('content')
    <h1>Bulletins</h1>

    <form method="get" class="filters">
        <select name="id_etablissement">
            <option value="">-- Établissement --</option>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(request('id_etablissement') == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>
        <select name="id_referentiel">
            <option value="">-- Classe --</option>
            @foreach ($classes as $classe)
                <option value="{{ $classe->id_classe }}" @selected(request('id_referentiel') == $classe->id_classe)>{{ $classe->classe }}</option>
            @endforeach
        </select>
        <input type="number" name="annee" placeholder="Année" value="{{ request('annee') }}">
        <input type="number" name="semestre" placeholder="Semestre" value="{{ request('semestre') }}">
        <button type="submit">Filtrer</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Élève</th>
                <th>Classe</th>
                <th>Établissement</th>
                <th>Année/Semestre</th>
                <th>Décision</th>
                <th>Publié</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bulletins as $bulletin)
                <tr>
                    <td>{{ $bulletin->eleve?->contact?->nom_complet ?? ($bulletin->id_eleve === 0 ? '(classe entière)' : '-') }}</td>
                    <td>{{ $bulletin->classe?->classe }}</td>
                    <td>{{ $bulletin->etablissement?->nom_etablissement }}</td>
                    <td>{{ $bulletin->annee }} / S{{ $bulletin->semestre }}</td>
                    <td>{{ $bulletin->decision_jury ?: '-' }}</td>
                    <td>{{ $bulletin->est_publie == '1' ? 'Oui' : 'Non' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Aucun bulletin.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $bulletins->links() }}
@endsection
