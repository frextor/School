@extends('layouts.app')

@section('title', "Récapitulatif d'heures")

@section('content')
    <a href="{{ route('intervenant.dashboard') }}">&larr; Retour</a>
    <h1>Saisir un récapitulatif d'heures</h1>
    <p><a href="{{ route('recapitulatif.resume') }}">Voir le récapitulatif global</a></p>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('recapitulatif.store') }}">
        @csrf

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Établissement</th>
                    <th>Classe</th>
                    <th>Type de cours</th>
                    <th>Cours</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Volume horaire</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="date" name="date_recap[]" required></td>
                    <td>
                        <select name="etablissementH[]" required>
                            @foreach ($etablissements as $etablissement)
                                <option value="{{ $etablissement->id_etablissement }}">{{ $etablissement->nom_etablissement }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="classe_recap[]" required>
                            @foreach ($classes as $classe)
                                <option value="{{ $classe->id_classe }}">{{ $classe->classe }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="type_cour_recap[]" required>
                            @foreach ($typesCours as $type)
                                <option value="{{ $type->id_type_cours }}">{{ $type->type_cours }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="intitule_recap_id[]" required>
                            @foreach ($coursListe as $cours)
                                <option value="{{ $cours->id_cours }}">{{ $cours->nom_cours }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="time" name="time_debut_recap[]" required></td>
                    <td><input type="time" name="time_fin_recap[]" required></td>
                    <td><input type="time" name="volumeh[]" required></td>
                </tr>
            </tbody>
        </table>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
