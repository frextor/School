{{--
    Formulaire d'un enseignant, partagé par la création et la modification.
    Attend $intervenant (éventuellement vide), $cours, $etablissements.
--}}
@php
    $civilite = old('civilite', $intervenant->civilite ?: 'M');
    $coursChoisis = collect(old('cours', $intervenant->exists ? $intervenant->cours->pluck('id_cours')->all() : []))
        ->map(fn ($v) => (int) $v);
    $ecolesChoisies = collect(old('etablissements', $intervenant->exists ? $intervenant->etablissements->pluck('id_etablissement')->all() : []))
        ->map(fn ($v) => (int) $v);
    $naissance = old('date_naissance', $intervenant->date_naissance
        ? \Illuminate\Support\Carbon::parse($intervenant->date_naissance)->format('Y-m-d')
        : '');
@endphp

<section class="form-card">
    <div class="form-head"><h2>Identité</h2></div>

    <div class="form-grid">
        <div class="field">
            <span class="field-label">Civilité</span>
            <div class="seg-group">
                @foreach (['M' => 'M.', 'Mme' => 'Mme'] as $valeur => $libelle)
                    <label class="seg-opt">
                        <input type="radio" name="civilite" value="{{ $valeur }}" @checked($civilite === $valeur) required>
                        <span>{{ $libelle }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <label class="field">
            <span>Nom</span>
            <input type="text" name="nom" required value="{{ old('nom', $intervenant->nom) }}">
        </label>

        <label class="field">
            <span>Prénom</span>
            <input type="text" name="prenom" required value="{{ old('prenom', $intervenant->prenom) }}">
        </label>

        <label class="field">
            <span>Date de naissance</span>
            <input type="date" name="date_naissance" required value="{{ $naissance }}">
        </label>
    </div>
</section>

<section class="form-card">
    <div class="form-head">
        <h2>Coordonnées</h2>
        <span class="form-sub">L'adresse e-mail sert aussi d'identifiant à l'espace enseignant.</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span>Adresse e-mail</span>
            <input type="email" name="email" required value="{{ old('email', $intervenant->email) }}">
        </label>

        <label class="field">
            <span>Téléphone</span>
            <input type="text" name="telephone" value="{{ old('telephone', $intervenant->telephone) }}">
        </label>

        <label class="field">
            <span>Mobile</span>
            <input type="text" name="mobile" value="{{ old('mobile', $intervenant->mobile) }}">
        </label>

        <label class="field field-full">
            <span>Adresse</span>
            <input type="text" name="adresse" value="{{ old('adresse', $intervenant->adresse) }}">
        </label>

        <label class="field">
            <span>Code postal</span>
            <input type="text" name="code_postal" value="{{ old('code_postal', $intervenant->code_postal) }}">
        </label>

        <label class="field">
            <span>Ville</span>
            <input type="text" name="ville" value="{{ old('ville', $intervenant->ville) }}">
        </label>
    </div>
</section>

<section class="form-card">
    <div class="form-head">
        <h2>Enseignement</h2>
        <span class="form-sub" data-compte-cours></span>
    </div>

    <div class="form-body">
        <span class="field-label">Matières enseignées</span>
        @if ($cours->isEmpty())
            <p class="field-aide" style="margin:0">Aucune matière au catalogue.</p>
        @else
            <div class="pick-list">
                @foreach ($cours as $matiere)
                    <label class="pick">
                        <input type="checkbox" name="cours[]" value="{{ $matiere->id_cours }}"
                               data-cours @checked($coursChoisis->contains((int) $matiere->id_cours))>
                        <span>{{ $matiere->nom_cours }}</span>
                    </label>
                @endforeach
            </div>
        @endif

        <span class="field-label" style="margin-top:18px">Établissements</span>
        <div class="pick-list">
            @foreach ($etablissements as $etablissement)
                <label class="pick">
                    <input type="checkbox" name="etablissements[]" value="{{ $etablissement->id_etablissement }}"
                           @checked($ecolesChoisies->contains((int) $etablissement->id_etablissement))>
                    <span>{{ $etablissement->nom_etablissement }}</span>
                </label>
            @endforeach
        </div>
    </div>
</section>

<section class="form-card">
    <div class="form-head">
        <h2>Situation professionnelle</h2>
        <span class="form-sub">Facultatif.</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span>Profession</span>
            <input type="text" name="profession" value="{{ old('profession', $intervenant->profession) }}">
        </label>

        <label class="field">
            <span>Poste actuel</span>
            <input type="text" name="poste_actuel" value="{{ old('poste_actuel', $intervenant->poste_actuel) }}">
        </label>

        <label class="field">
            <span>Société</span>
            <input type="text" name="raison_sociale" value="{{ old('raison_sociale', $intervenant->societe?->raison_sociale) }}">
        </label>

        <label class="field">
            <span>Adresse de la société</span>
            <input type="text" name="adresse_societe" value="{{ old('adresse_societe', $intervenant->societe?->adresse_societe) }}">
        </label>

        <label class="field">
            <span>CV</span>
            <input type="file" name="cv" accept=".pdf,.doc,.docx">
            @if ($intervenant->exists && $intervenant->cheminCv())
                <small class="field-aide"><a href="{{ Storage::disk('public')->url($intervenant->cheminCv()) }}" target="_blank" rel="noopener">CV actuel</a> — déposer un fichier le remplace.</small>
            @endif
        </label>

        <label class="field">
            <span>Photo</span>
            <input type="file" name="photo" accept="image/*">
        </label>
    </div>
</section>

<script>
    (function () {
        var cases = Array.prototype.slice.call(document.querySelectorAll('[data-cours]'));
        var compte = document.querySelector('[data-compte-cours]');
        if (!compte) return;

        function maj() {
            var n = cases.filter(function (c) { return c.checked; }).length;
            compte.textContent = n
                ? n + ' matière' + (n > 1 ? 's' : '')
                : 'aucune matière : l\'enseignant n\'apparaîtra dans aucune affectation';
        }

        cases.forEach(function (c) { c.addEventListener('change', maj); });
        maj();
    })();
</script>
