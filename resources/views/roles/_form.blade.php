{{--
    Formulaire d'un rôle, partagé par la création et la modification.
    Attend $role (éventuellement vide), $permissions et $choisies.
--}}
<section class="form-card">
    <div class="form-head"><h2>Le rôle</h2></div>

    <div class="form-grid">
        <label class="field field-full">
            <span>Nom du rôle</span>
            <input type="text" name="nom_role" required placeholder="Secrétariat, Direction, Comptabilité…"
                   value="{{ old('nom_role', $role->nom_role) }}">
            @if ($role->exists)
                <small class="field-aide">Nom machine : <code>{{ $role->nom_machine }}</code></small>
            @else
                <small class="field-aide">Le nom machine est dérivé automatiquement à la création.</small>
            @endif
        </label>
    </div>
</section>

<section class="form-card">
    <div class="form-head">
        <h2>Permissions accordées</h2>
        <span class="form-sub" data-compte></span>
    </div>

    <div class="form-body">
        @if ($permissions->isEmpty())
            <p class="field-aide" style="margin:0">Aucune permission n'est encore définie.</p>
        @else
            <div class="pick-list">
                @foreach ($permissions as $permission)
                    <label class="pick">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id_permission }}"
                               data-perm @checked($choisies->contains($permission->id_permission))>
                        <span>{{ $permission->nom_permission }}</span>
                    </label>
                @endforeach
            </div>
        @endif
    </div>
</section>

<script>
    (function () {
        // Sans compteur, on ne sait pas si un rôle donne deux droits ou trente.
        var cases = Array.prototype.slice.call(document.querySelectorAll('[data-perm]'));
        var compte = document.querySelector('[data-compte]');
        if (!compte) return;

        function maj() {
            var n = cases.filter(function (c) { return c.checked; }).length;
            compte.textContent = n
                ? n + ' permission' + (n > 1 ? 's' : '') + ' sur ' + cases.length
                : 'aucune permission : le rôle n\'ouvre aucun écran';
        }

        cases.forEach(function (c) { c.addEventListener('change', maj); });
        maj();
    })();
</script>
