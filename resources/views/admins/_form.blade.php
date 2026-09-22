{{--
    Formulaire d'un administrateur, partagé par la création et la modification.
    Attend $admin (éventuellement vide), $roles, $services, $etablissements.
--}}
@php
    $ecolesChoisies = collect(old('etablissements', $admin->exists ? $admin->etablissements->pluck('id_etablissement')->all() : []))
        ->map(fn ($v) => (int) $v);
    $principal = old('etablissement_principal', $admin->exists ? $admin->id_etablissement : null);
@endphp

<section class="form-card">
    <div class="form-head"><h2>La personne</h2></div>

    <div class="form-grid">
        <label class="field">
            <span>Nom</span>
            <input type="text" name="nom" required value="{{ old('nom', $admin->nom) }}">
        </label>

        <label class="field">
            <span>Prénom</span>
            <input type="text" name="prenom" required value="{{ old('prenom', $admin->prenom) }}">
        </label>

        <label class="field field-full">
            <span>Adresse e-mail</span>
            <input type="email" name="email" required value="{{ old('email', $admin->email) }}">
        </label>
    </div>
</section>

<section class="form-card">
    <div class="form-head">
        <h2>L'accès</h2>
        <span class="form-sub">Identifiant de connexion et droits ouverts.</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span>Identifiant</span>
            <input type="text" name="username" required autocomplete="off"
                   value="{{ old('username', $admin->username) }}">
        </label>

        <label class="field">
            <span>{{ $admin->exists ? 'Nouveau mot de passe' : 'Mot de passe' }}</span>
            <input type="password" name="password" autocomplete="new-password" @required(! $admin->exists)>
            @if ($admin->exists)
                <small class="field-aide">Laisser vide pour conserver le mot de passe actuel.</small>
            @endif
        </label>

        <label class="field">
            <span>Rôle</span>
            <select name="profil" required>
                @foreach ($roles as $role)
                    <option value="{{ $role->nom_machine }}" @selected(old('profil', $admin->profil) === $role->nom_machine)>{{ $role->nom_role }}</option>
                @endforeach
            </select>
            <small class="field-aide">Détermine les écrans accessibles.</small>
        </label>

        <label class="field">
            <span>Service</span>
            <select name="id_service" required>
                @foreach ($services as $service)
                    <option value="{{ $service->id_service }}" @selected(old('id_service', $admin->id_service) == $service->id_service)>{{ $service->libelle }}</option>
                @endforeach
            </select>
        </label>

        <label class="field field-full">
            <span>Photo</span>
            <input type="file" name="avatar" accept="image/*">
        </label>
    </div>
</section>

<section class="form-card">
    <div class="form-head">
        <h2>Établissements</h2>
        <span class="form-sub" data-compte-ecoles></span>
    </div>

    <div class="form-body">
        {{-- Les listes à sélection multiple obligeaient à garder Ctrl enfoncé
             pour cocher plusieurs campus : des cases font le même travail. --}}
        <div class="pick-list">
            @foreach ($etablissements as $etablissement)
                <label class="pick">
                    <input type="checkbox" name="etablissements[]" value="{{ $etablissement->id_etablissement }}"
                           data-ecole @checked($ecolesChoisies->contains((int) $etablissement->id_etablissement))>
                    <span>{{ $etablissement->nom_etablissement }}</span>
                </label>
            @endforeach
        </div>

        <label class="field" style="margin-top:16px;max-width:340px">
            <span>Établissement principal</span>
            <select name="etablissement_principal">
                <option value="">Aucun</option>
                @foreach ($etablissements as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected($principal == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
            <small class="field-aide">Celui qui s'affiche par défaut à la connexion.</small>
        </label>
    </div>
</section>

<script>
    (function () {
        var cases = Array.prototype.slice.call(document.querySelectorAll('[data-ecole]'));
        var compte = document.querySelector('[data-compte-ecoles]');
        if (!compte) return;

        function maj() {
            var n = cases.filter(function (c) { return c.checked; }).length;
            compte.textContent = n
                ? n + ' établissement' + (n > 1 ? 's' : '')
                : 'aucun coché : accès à tous les établissements';
        }

        cases.forEach(function (c) { c.addEventListener('change', maj); });
        maj();
    })();
</script>
