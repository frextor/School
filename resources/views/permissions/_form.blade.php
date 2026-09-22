{{--
    Formulaire d'une permission, partagé par la création et la modification.
    Attend $permission (éventuellement vide) et $permissionsParentes.
--}}
<section class="form-card">
    <div class="form-head">
        <h2>La permission</h2>
        <span class="form-sub">Elle ouvre une route de l'application aux rôles qui la portent.</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span>Nom</span>
            <input type="text" name="nom_permission" required placeholder="Consulter les élèves"
                   value="{{ old('nom_permission', $permission->nom_permission) }}">
        </label>

        <label class="field">
            <span>Permission parente</span>
            <select name="ParentID">
                <option value="">Aucune — permission de premier niveau</option>
                @foreach ($permissionsParentes as $parente)
                    <option value="{{ $parente->id_permission }}" @selected(old('ParentID', $permission->ParentID) == $parente->id_permission)>{{ $parente->nom_permission }}</option>
                @endforeach
            </select>
            <small class="field-aide">Sert à regrouper les permissions d'un même module.</small>
        </label>

        <label class="field field-full">
            <span>Route</span>
            <input type="text" name="route" required placeholder="eleves.index"
                   value="{{ old('route', $permission->route) }}">
            <small class="field-aide">Le nom de la route Laravel protégée par cette permission.</small>
        </label>
    </div>
</section>
