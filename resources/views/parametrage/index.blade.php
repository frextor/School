@extends('layouts.app')

@section('title', 'Volumes de formation')

@section('content')
    <h1>Paramétrage — volumes de formation</h1>
    <p style="color:var(--muted)">Effectif prévisionnel, nombre de classes et volume de cours par niveau, pour dimensionner une année de formation.</p>

    <form method="get" action="{{ route('referentiel.parametrage.index') }}" class="filters">
        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" onchange="this.form.submit()">
            <option value="">-- Choisir --</option>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected($idEtablissement == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>
    </form>

    @if ($idEtablissement)
        <form method="post" action="{{ route('referentiel.parametrage.save') }}">
            @csrf
            <input type="hidden" name="id_etablissement" value="{{ $idEtablissement }}">

            <table>
                <thead>
                    <tr>
                        <th>Niveau</th>
                        <th>Effectif</th>
                        <th>Nb. classes</th>
                        <th>Volume de cours</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($niveaux as $niveau)
                        @php $volume = $volumes->get($niveau->id_niveau) @endphp
                        <tr>
                            <td>
                                {{ $niveau->nom_niveau }}
                                <input type="hidden" name="lignes[{{ $loop->index }}][id_niveau]" value="{{ $niveau->id_niveau }}">
                            </td>
                            <td><input type="number" name="lignes[{{ $loop->index }}][effectif]" value="{{ $volume->effectif ?? '' }}" style="max-width:100px"></td>
                            <td><input type="number" name="lignes[{{ $loop->index }}][nb_classe]" value="{{ $volume->nb_classe ?? '' }}" style="max-width:100px"></td>
                            <td><input type="number" step="0.01" name="lignes[{{ $loop->index }}][volume_cours]" value="{{ $volume->volume_cours ?? '' }}" style="max-width:120px"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p style="margin-top:1rem">
                <button type="submit" class="btn">Enregistrer</button>
            </p>
        </form>

        <h2 style="margin-top:2rem">Totaux</h2>
        <table>
            <thead>
                <tr><th></th><th>Effectif</th><th>Nb. classes</th><th>Volume de cours</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Bachelor</td>
                    <td>{{ $totaux['bachelor']['effectif'] }}</td>
                    <td>{{ $totaux['bachelor']['nb_classe'] }}</td>
                    <td>{{ $totaux['bachelor']['volume_cours'] }}</td>
                </tr>
                <tr>
                    <td>Master</td>
                    <td>{{ $totaux['master']['effectif'] }}</td>
                    <td>{{ $totaux['master']['nb_classe'] }}</td>
                    <td>{{ $totaux['master']['volume_cours'] }}</td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong>{{ $totaux['bachelor']['effectif'] + $totaux['master']['effectif'] }}</strong></td>
                    <td><strong>{{ $totaux['bachelor']['nb_classe'] + $totaux['master']['nb_classe'] }}</strong></td>
                    <td><strong>{{ $totaux['bachelor']['volume_cours'] + $totaux['master']['volume_cours'] }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <p style="margin-top:1rem;color:var(--muted)">Choisissez un établissement pour afficher/modifier les volumes.</p>
    @endif
@endsection
