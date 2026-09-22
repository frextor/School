{{-- Formulaire d'une salle, partagé par la création et la modification. --}}
<section class="form-card">
    <div class="form-head">
        <h2>La salle</h2>
        <span class="form-sub">Le code apparaît dans l'emploi du temps et sur les panneaux d'affichage.</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span>Code</span>
            <input type="text" name="code_salle" maxlength="5" required placeholder="A12"
                   value="{{ old('code_salle', $salle->code_salle) }}">
            <small class="field-aide">Cinq caractères au plus.</small>
        </label>

        <label class="field">
            <span>Nom</span>
            <input type="text" name="nom_salle" required placeholder="Salle de sciences"
                   value="{{ old('nom_salle', $salle->nom_salle) }}">
        </label>

        <label class="field">
            <span>Capacité</span>
            <input type="number" name="nombre_place" min="1" max="999" required
                   value="{{ old('nombre_place', $salle->nombre_place ?: 30) }}">
            <small class="field-aide">Nombre d'élèves que la salle accueille.</small>
        </label>

        <label class="field">
            <span>Établissement</span>
            <select name="id_etablissement" required>
                @foreach ($etablissements as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $salle->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
        </label>
    </div>
</section>
