@extends('layouts.app')

@section('title', 'Référentiel des heures')

@section('content')
    <h1>Référentiel des heures d'enseignement</h1>

    <form method="get" action="{{ route('referentiel.ref.index') }}" class="filters">
        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" onchange="this.form.submit()">
            <option value="">-- Choisir --</option>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected($idEtablissement == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="id_unite_enseignement">Unité d'enseignement</label>
        <select name="id_unite_enseignement" id="id_unite_enseignement" onchange="this.form.submit()">
            <option value="">-- Choisir --</option>
            @foreach ($unites as $unite)
                <option value="{{ $unite->id_unite_enseignement }}" @selected($idUe == $unite->id_unite_enseignement)>{{ $unite->nom_unite_enseignement }}</option>
            @endforeach
        </select>

        <label for="annee">Année</label>
        <input type="text" name="annee" id="annee" value="{{ $annee }}" placeholder="ex: 2025-2026" onchange="this.form.submit()">

        <button type="submit">Afficher</button>
    </form>

    @if ($idEtablissement && $idUe && $annee !== '')
        <h2>Par niveau</h2>
        <table>
            <thead>
                <tr>
                    <th>Semestre</th><th>Niveau</th><th>Cours</th><th>Intervenant</th>
                    <th>CC</th><th>CR</th><th>TD</th><th>EI</th><th>Volume</th><th>ECTS</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lignesNiveau as $ligne)
                    <tr>
                        <td>{{ strtoupper($ligne->semestre) }}</td>
                        <td>{{ $ligne->niveau->nom_niveau ?? '-' }}</td>
                        <td>{{ $ligne->cours->nom_cours ?? '-' }}</td>
                        <td>{{ $ligne->intervenant ? $ligne->intervenant->nom.' '.$ligne->intervenant->prenom : '-' }}</td>
                        <td>{{ $ligne->cc }}</td>
                        <td>{{ $ligne->cr }}</td>
                        <td>{{ $ligne->td }}</td>
                        <td>{{ $ligne->ei }}</td>
                        <td>{{ $ligne->volume }}</td>
                        <td>{{ $ligne->ects }}</td>
                        <td>
                            <form method="post" action="{{ route('referentiel.ref.niveau.destroy', $ligne) }}" onsubmit="return confirm('Supprimer cette ligne ?')">
                                @csrf @method('DELETE')
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11">Aucune ligne.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>Ajouter une ligne (niveau)</h3>
        <form method="post" action="{{ route('referentiel.ref.niveau.store') }}" class="filters">
            @csrf
            <input type="hidden" name="id_etablissement" value="{{ $idEtablissement }}">
            <input type="hidden" name="id_unite_enseignement" value="{{ $idUe }}">
            <input type="hidden" name="annee" value="{{ $annee }}">

            <label>Niveau</label>
            <select name="id_niveau" required>
                @foreach ($niveaux as $niveau)
                    <option value="{{ $niveau->id_niveau }}">{{ $niveau->nom_niveau }}</option>
                @endforeach
            </select>

            <label>Semestre</label>
            <select name="semestre" required>
                <option value="s1">S1</option>
                <option value="s2">S2</option>
            </select>

            <label>Cours</label>
            <select name="id_cours" required>
                @foreach ($cours as $c)
                    <option value="{{ $c->id_cours }}">{{ $c->nom_cours }}</option>
                @endforeach
            </select>

            <label>Intervenant</label>
            <select name="id_intervenant" required>
                @foreach ($intervenants as $intervenant)
                    <option value="{{ $intervenant->id_intervenant }}">{{ $intervenant->nom }} {{ $intervenant->prenom }}</option>
                @endforeach
            </select>

            <label>CC</label><input type="number" step="0.01" name="cc" value="0">
            <label>CR</label><input type="number" step="0.01" name="cr" value="0">
            <label>TD</label><input type="number" step="0.01" name="td" value="0">
            <label>EI</label><input type="number" step="0.01" name="ei" value="0">
            <label>Volume</label><input type="number" step="0.01" name="volume" value="0">
            <label>ECTS</label><input type="number" step="0.01" name="ects" value="0">

            <button type="submit" class="btn">Ajouter</button>
        </form>

        <h2 style="margin-top:2rem">Par classe</h2>
        <table>
            <thead>
                <tr>
                    <th>Semestre</th><th>Niveau</th><th>Classe</th><th>Cours</th><th>Intervenant</th>
                    <th>CC</th><th>CR</th><th>TD</th><th>EI</th><th>Volume</th><th>ECTS</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lignesClasse as $ligne)
                    <tr>
                        <td>{{ strtoupper($ligne->semestre) }}</td>
                        <td>{{ $ligne->niveau->nom_niveau ?? '-' }}</td>
                        <td>{{ $ligne->classe->classe ?? '-' }}</td>
                        <td>{{ $ligne->cours->nom_cours ?? '-' }}</td>
                        <td>{{ $ligne->intervenant ? $ligne->intervenant->nom.' '.$ligne->intervenant->prenom : '-' }}</td>
                        <td>{{ $ligne->cc }}</td>
                        <td>{{ $ligne->cr }}</td>
                        <td>{{ $ligne->td }}</td>
                        <td>{{ $ligne->ei }}</td>
                        <td>{{ $ligne->volume }}</td>
                        <td>{{ $ligne->ects }}</td>
                        <td>
                            <form method="post" action="{{ route('referentiel.ref.classe.destroy', $ligne) }}" onsubmit="return confirm('Supprimer cette ligne ?')">
                                @csrf @method('DELETE')
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12">Aucune ligne.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>Ajouter une ligne (classe)</h3>
        <form method="post" action="{{ route('referentiel.ref.classe.store') }}" class="filters">
            @csrf
            <input type="hidden" name="id_etablissement" value="{{ $idEtablissement }}">
            <input type="hidden" name="id_unite_enseignement" value="{{ $idUe }}">
            <input type="hidden" name="annee" value="{{ (int) $annee }}">

            <label>Niveau</label>
            <select name="id_niveau" required>
                @foreach ($niveaux as $niveau)
                    <option value="{{ $niveau->id_niveau }}">{{ $niveau->nom_niveau }}</option>
                @endforeach
            </select>

            <label>Classe</label>
            <select name="id_classe" required>
                @foreach ($classes as $classe)
                    <option value="{{ $classe->id_classe }}">{{ $classe->classe }}</option>
                @endforeach
            </select>

            <label>Semestre</label>
            <select name="semestre" required>
                <option value="s1">S1</option>
                <option value="s2">S2</option>
            </select>

            <label>Cours</label>
            <select name="id_cours" required>
                @foreach ($cours as $c)
                    <option value="{{ $c->id_cours }}">{{ $c->nom_cours }}</option>
                @endforeach
            </select>

            <label>Intervenant</label>
            <select name="id_intervenant" required>
                @foreach ($intervenants as $intervenant)
                    <option value="{{ $intervenant->id_intervenant }}">{{ $intervenant->nom }} {{ $intervenant->prenom }}</option>
                @endforeach
            </select>

            <label>CC</label><input type="number" step="0.01" name="cc" value="0">
            <label>CR</label><input type="number" step="0.01" name="cr" value="0">
            <label>TD</label><input type="number" step="0.01" name="td" value="0">
            <label>EI</label><input type="number" step="0.01" name="ei" value="0">
            <label>Volume</label><input type="number" step="0.01" name="volume" value="0">
            <label>ECTS</label><input type="number" step="0.01" name="ects" value="0">

            <button type="submit" class="btn">Ajouter</button>
        </form>
    @else
        <p style="margin-top:1rem;color:var(--muted)">Choisissez un établissement, une UE et une année pour afficher/modifier le référentiel.</p>
    @endif
@endsection
