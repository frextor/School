{{--
    Formulaire d'une matière, partagé par la création et la modification.
    Attend $cours (éventuellement vide).
--}}
<section class="form-card">
    <div class="form-head">
        <h2>La matière</h2>
        <span class="form-sub">C'est elle que la notation, les coefficients, les bulletins et l'emploi du temps désignent.</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span>Code</span>
            <input type="text" name="code_cours" maxlength="100" required placeholder="MATH"
                   value="{{ old('code_cours', $cours->code_cours) }}">
            <small class="field-aide">Abréviation reprise dans les exports.</small>
        </label>

        <label class="field">
            <span>Nom</span>
            <input type="text" name="nom_cours" required placeholder="Mathématiques"
                   value="{{ old('nom_cours', $cours->nom_cours) }}">
        </label>
    </div>

    <div class="form-body" style="padding-top:0">
        <p class="field-aide" style="margin:0">
            Le coefficient et le volume horaire ne se règlent pas ici : ils dépendent du niveau,
            et se saisissent dans le <a href="{{ route('referentiel.ref.index') }}">référentiel pédagogique</a>.
        </p>
    </div>
</section>
