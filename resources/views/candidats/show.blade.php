@extends('layouts.app')

@section('title', 'Fiche candidat')

@section('content')
    <a href="{{ route('candidats.index') }}">&larr; Retour</a>
    <h1>{{ $candidat->contact?->nom_complet }}</h1>

    @if ($errors->any())
        <div class="status error">
            <ul style="margin:0;padding-left:1.1rem">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table>
        <tr><th>Email</th><td>{{ $candidat->contact?->email }}</td></tr>
        <tr><th>Niveau</th><td>{{ $candidat->niveau?->nom_niveau }}</td></tr>
        <tr><th>Archivé</th><td>{{ $candidat->visible ? 'Oui' : 'Non' }}</td></tr>
    </table>

    @if ($estAdmis)
        <div class="status" style="margin-top:1rem">
            <strong>Admis</strong> — ce candidat a un résultat d'admission favorable.
        </div>

        <h3 style="margin-top:1rem">Inscrire comme élève</h3>
        <form method="post" action="{{ route('candidats.inscrire-eleve', $candidat) }}" style="display:flex;gap:0.5rem;align-items:end">
            @csrf
            <span>
                <label for="ie_niveau">Niveau</label><br>
                <select name="id_niveau" id="ie_niveau" required>
                    <option value="">-- Choisir --</option>
                    @foreach ($niveaux as $niveau)
                        <option value="{{ $niveau->id_niveau }}" @selected(old('id_niveau', $candidat->id_niveau) == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
                    @endforeach
                </select>
            </span>
            <span>
                <label for="ie_classe">Classe</label><br>
                <select name="id_classe" id="ie_classe" required disabled>
                    <option value="">-- Choisir un niveau d'abord --</option>
                </select>
            </span>
            <button type="submit" class="btn">Inscrire comme élève</button>
        </form>

        <script>
            (function () {
                var classes = @json($classes->map(fn ($c) => ['id_classe' => $c->id_classe, 'classe' => $c->classe, 'id_niveau' => $c->id_niveau]));
                var niveauSelect = document.getElementById('ie_niveau');
                var classeSelect = document.getElementById('ie_classe');

                function refresh() {
                    var idNiveau = parseInt(niveauSelect.value, 10);
                    var options = classes.filter(function (c) { return c.id_niveau === idNiveau; });

                    if (!options.length) {
                        classeSelect.innerHTML = '<option value="">Aucune classe pour ce niveau</option>';
                        classeSelect.disabled = true;
                        return;
                    }

                    classeSelect.innerHTML = '<option value="">-- Choisir --</option>' + options.map(function (c) {
                        return '<option value="' + c.id_classe + '">' + c.classe + '</option>';
                    }).join('');
                    classeSelect.disabled = false;
                }

                niveauSelect.addEventListener('change', refresh);
                if (niveauSelect.value) refresh();
            })();
        </script>
    @endif

    <h2>Épreuves d'admission</h2>
    <table>
        <thead><tr><th>Date</th><th>Lieu</th><th>Présent</th></tr></thead>
        <tbody>
            @forelse ($candidat->epreuvesInscriptions as $inscription)
                <tr>
                    <td>{{ $inscription->epreuve?->date_epreuve?->format('d/m/Y H:i') }}</td>
                    <td>{{ $inscription->epreuve?->lieu }}</td>
                    <td>{{ $inscription->presence ? 'Oui' : 'Non' }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Aucune épreuve programmée.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Résultats</h2>
    <table>
        <thead><tr><th>Épreuve</th><th>Anglais</th><th>Culture G.</th><th>Rédaction</th><th>Entretien</th><th>Décision</th></tr></thead>
        <tbody>
            @forelse ($candidat->resultatsEpreuves as $resultat)
                <tr>
                    <td>{{ $resultat->epreuve?->date_epreuve?->format('d/m/Y') }}</td>
                    <td>{{ $resultat->anglais }}</td>
                    <td>{{ $resultat->culture_generale }}</td>
                    <td>{{ $resultat->epreuve_redaction }}</td>
                    <td>{{ $resultat->entretien }}</td>
                    <td>{{ $resultat->decision }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Aucun résultat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem;display:flex;gap:0.5rem">
        @if ($candidat->visible)
            <form method="post" action="{{ route('candidats.unarchive', $candidat) }}">
                @csrf
                <button type="submit">Désarchiver</button>
            </form>
        @else
            <form method="post" action="{{ route('candidats.archive', $candidat) }}">
                @csrf
                <button type="submit">Archiver</button>
            </form>
        @endif

        <form method="post" action="{{ route('candidats.destroy', $candidat) }}" onsubmit="return confirm('Supprimer ce candidat ?')">
            @csrf
            @method('DELETE')
            <button type="submit">Supprimer</button>
        </form>
    </div>
@endsection
