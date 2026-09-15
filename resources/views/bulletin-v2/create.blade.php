@extends('layouts.app')

@section('title', 'Générer un bulletin')

@section('content')
    <a href="{{ route('bulletin-v2.index') }}">&larr; Retour</a>
    <h1>Générer un bulletin</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('bulletin-v2.generate') }}">
        @csrf

        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" required>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}">{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="id_niveau">Niveau</label>
        <select name="id_niveau" id="id_niveau" required>
            <option value="">-- Choisir --</option>
            @foreach ($niveaux as $niveau)
                <option value="{{ $niveau->id_niveau }}">{{ $niveau->nom_niveau }}</option>
            @endforeach
        </select>

        <label for="id_classe">Classe</label>
        <select id="id_classe" disabled>
            <option value="">-- Choisir un niveau d'abord --</option>
        </select>

        <label for="id_eleve">Élève</label>
        <select name="id_eleve" id="id_eleve" required disabled>
            <option value="">-- Choisir une classe d'abord --</option>
        </select>

        <label for="annee">Année</label>
        <input type="number" name="annee" id="annee" value="{{ date('Y') }}" required>

        <label for="semestre">Semestre (vide = les deux)</label>
        <input type="number" name="semestre" id="semestre" min="1" max="2">

        <label for="session">Session</label>
        <input type="number" name="session" id="session" value="0">

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Générer le PDF</button>
        </p>
    </form>

    <script>
        // Cascade Niveau -> Classe -> Élève : évite de devoir connaître/saisir un ID élève à la main.
        (function () {
            var classes = @json($classes->map(fn ($c) => ['id_classe' => $c->id_classe, 'classe' => $c->classe, 'id_niveau' => $c->id_niveau]));
            var niveauSelect = document.getElementById('id_niveau');
            var classeSelect = document.getElementById('id_classe');
            var eleveSelect = document.getElementById('id_eleve');

            niveauSelect.addEventListener('change', function () {
                var idNiveau = parseInt(niveauSelect.value, 10);
                var options = classes.filter(function (c) { return c.id_niveau === idNiveau; });

                classeSelect.innerHTML = '';
                eleveSelect.innerHTML = '<option value="">-- Choisir une classe d\'abord --</option>';
                eleveSelect.disabled = true;

                if (!options.length) {
                    classeSelect.innerHTML = '<option value="">Aucune classe pour ce niveau</option>';
                    classeSelect.disabled = true;
                    return;
                }

                classeSelect.innerHTML = '<option value="">-- Choisir --</option>' + options.map(function (c) {
                    return '<option value="' + c.id_classe + '">' + c.classe + '</option>';
                }).join('');
                classeSelect.disabled = false;
            });

            classeSelect.addEventListener('change', function () {
                var idClasse = classeSelect.value;
                eleveSelect.innerHTML = '<option value="">Chargement...</option>';
                eleveSelect.disabled = true;

                if (!idClasse) {
                    eleveSelect.innerHTML = '<option value="">-- Choisir une classe d\'abord --</option>';
                    return;
                }

                fetch('{{ url('bulletin-v2/classes') }}/' + idClasse + '/eleves')
                    .then(function (r) { return r.json(); })
                    .then(function (eleves) {
                        if (!eleves.length) {
                            eleveSelect.innerHTML = '<option value="">Aucun élève dans cette classe</option>';
                            return;
                        }
                        eleveSelect.innerHTML = '<option value="">-- Choisir --</option>' + eleves.map(function (e) {
                            return '<option value="' + e.id_eleve + '">' + (e.nom ?? '') + ' ' + (e.prenom ?? '') + '</option>';
                        }).join('');
                        eleveSelect.disabled = false;
                    });
            });
        })();
    </script>
@endsection
