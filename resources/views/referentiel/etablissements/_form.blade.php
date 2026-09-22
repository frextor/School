{{--
    Formulaire d'un établissement, partagé par la création et la modification.
    Attend $etablissement (éventuellement vide).
--}}
<section class="form-card">
    <div class="form-head">
        <h2>L'établissement</h2>
        <span class="form-sub">Un établissement, c'est un campus : il porte ses classes, ses salles et ses élèves.</span>
    </div>

    <div class="form-grid">
        <label class="field field-full">
            <span>Nom de l'établissement</span>
            <input type="text" name="nom_etablissement" maxlength="100" required
                   placeholder="Groupe Scolaire Al Amal"
                   value="{{ old('nom_etablissement', $etablissement->nom_etablissement) }}">
            <small class="field-aide">Le nom tel qu'il apparaîtra partout : listes, bulletins, panneaux d'affichage.</small>
        </label>

        <label class="field">
            <span>Code</span>
            <input type="text" name="code_ville" maxlength="2" required
                   placeholder="CA" style="text-transform:uppercase"
                   value="{{ old('code_ville', $etablissement->code_ville) }}">
            <small class="field-aide">Deux lettres, reprises dans les références internes.</small>
        </label>

        <label class="field">
            <span>Adresse</span>
            <input type="text" name="adresse" maxlength="600" required
                   placeholder="24 boulevard Zerktouni, Casablanca"
                   value="{{ old('adresse', $etablissement->adresse) }}">
        </label>
    </div>

    <div class="form-body" style="padding-top:0">
        <label class="check-card">
            <input type="checkbox" name="visible" value="1" @checked(old('visible', $etablissement->exists ? $etablissement->visible : true))>
            <span>
                <strong>Établissement actif</strong>
                Un établissement masqué reste en base mais disparaît des listes déroulantes : inscriptions, emploi du temps, affectation de classes.
            </span>
        </label>
    </div>
</section>
