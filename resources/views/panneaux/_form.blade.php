{{--
    Formulaire d'un panneau, partagé par la création et la modification.
    Attend $panneau (éventuellement vide), $etablissements, $classes, $groupes.
--}}
@php
    $classesChoisies = collect(old('classes', $panneau->exists ? $panneau->classes->pluck('id_classe')->all() : []))
        ->map(fn ($v) => (int) $v);
    $groupesChoisis = collect(old('groupes', $panneau->exists ? $panneau->groupes->pluck('id_groupe')->all() : []))
        ->map(fn ($v) => (int) $v);
@endphp

<section class="panel pn-bloc">
    <div class="panel-head"><h2>Le panneau</h2></div>
    <div class="pn-grid">
        <label class="stack">
            <span>Titre affiché à l'écran</span>
            <input type="text" name="titre" maxlength="255" required
                   placeholder="Hall d'accueil, Couloir du collège…"
                   value="{{ old('titre', $panneau->titre) }}">
        </label>

        <label class="stack">
            <span>Identifiant</span>
            <input type="text" name="identifiant_panneaux" maxlength="255" required
                   placeholder="HALL-01" data-identifiant
                   value="{{ old('identifiant_panneaux', $panneau->identifiant_panneaux) }}">
            <small class="pn-aide" data-identifiant-aide>Il apparaît dans l'adresse du panneau : <code>/panneau/HALL-01</code></small>
        </label>

        <label class="stack">
            <span>Établissement</span>
            <select name="id_etablissement" required>
                @foreach ($etablissements as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $panneau->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
        </label>

        <label class="stack">
            <span>Année scolaire</span>
            <input type="text" name="annee" maxlength="250" placeholder="2026"
                   value="{{ old('annee', $panneau->annee) }}">
        </label>

        <label class="stack">
            <span>Heures affichées</span>
            <input type="number" name="plage_horaire" min="1" max="72" required
                   value="{{ old('plage_horaire', $panneau->plage_horaire ?: 8) }}">
            <small class="pn-aide">Les cours des N prochaines heures, à partir de l'instant présent.</small>
        </label>

        <label class="stack">
            <span>Rafraîchissement (secondes)</span>
            <input type="number" name="delai_horaire" min="15" max="3600" required
                   value="{{ old('delai_horaire', $panneau->delai_horaire ?: 60) }}">
            <small class="pn-aide">L'écran se recharge tout seul à cet intervalle.</small>
        </label>
    </div>
</section>

<section class="panel pn-bloc">
    <div class="panel-head">
        <h2>Ce qu'il affiche</h2>
        <span class="panel-sub">Sans sélection, le panneau montre tous les cours de l'établissement.</span>
    </div>

    <div class="pn-pad">
        <div class="pn-sel-tete">
            <span class="pn-label">Classes</span>
            <span class="pn-compte" data-compte-classes></span>
        </div>
        @if ($classes->isEmpty())
            <p class="pn-vide">Aucune classe enregistrée.</p>
        @else
            <div class="pn-cases">
                @foreach ($classes as $classe)
                    <label class="pn-case">
                        <input type="checkbox" name="classes[]" value="{{ $classe->id_classe }}"
                               data-case-classe @checked($classesChoisies->contains((int) $classe->id_classe))>
                        <span>{{ $classe->classe }}</span>
                    </label>
                @endforeach
            </div>
        @endif

        @if ($groupes->isNotEmpty())
            <div class="pn-sel-tete pn-sel-tete-2">
                <span class="pn-label">Groupes d'élèves</span>
            </div>
            <div class="pn-cases">
                @foreach ($groupes as $groupe)
                    <label class="pn-case">
                        <input type="checkbox" name="groupes[]" value="{{ $groupe->id_groupe }}"
                               @checked($groupesChoisis->contains((int) $groupe->id_groupe))>
                        <span>{{ $groupe->nom_groupe }}</span>
                    </label>
                @endforeach
            </div>
        @endif
    </div>
</section>

<style>
    /* Panneau : réglages puis périmètre, en cases plutôt qu'en listes
       multiples où il fallait garder Ctrl enfoncé. */
    .pn-form { max-width: 920px; margin: 0; }
    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-head { display: flex; align-items: baseline; gap: 10px; flex-wrap: wrap; padding: 14px 16px 12px; border-bottom: 1px solid var(--border-soft); }
    .panel-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head .panel-sub { font-size: 12px; color: var(--muted); }
    .pn-bloc + .pn-bloc { margin-top: 14px; }

    .pn-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; padding: 16px; }
    .pn-pad { padding: 16px; }
    .pn-form .stack { display: block; margin: 0; min-width: 0; }
    .pn-form .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .pn-form input, .pn-form select { width: 100%; max-width: none; }
    .pn-aide { display: block; margin-top: 5px; font-size: 11.5px; color: var(--muted); }
    .pn-aide code { font-size: 11px; }
    .pn-aide.is-erreur { color: var(--danger); }

    .pn-sel-tete { display: flex; align-items: baseline; gap: 10px; margin-bottom: 9px; }
    .pn-sel-tete-2 { margin-top: 18px; padding-top: 15px; border-top: 1px solid var(--border-soft); }
    .pn-label { font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); }
    .pn-compte { font-size: 12px; color: var(--muted); }

    .pn-cases { display: flex; flex-wrap: wrap; gap: 8px; }
    .pn-case {
        display: inline-flex; align-items: center; gap: 7px; margin: 0; padding: 7px 13px;
        border: 1px solid var(--border); border-radius: 999px; background: #fff;
        font-size: 12.5px; font-weight: 600; color: #585e72; cursor: pointer;
    }
    .pn-case:hover { border-color: #c3c6f5; }
    .pn-case input { width: 14px; height: 14px; max-width: none; margin: 0; accent-color: var(--brand); cursor: pointer; }
    .pn-case:has(input:checked) { border-color: var(--brand); background: var(--brand-light); color: var(--brand-deep); }
    .pn-vide { margin: 0; font-size: 13px; color: var(--muted); }

    .pn-pied { display: flex; align-items: center; gap: 10px; margin-top: 16px; }
    .pn-pied .btn:last-child { margin-left: auto; }
</style>

<script>
    (function () {
        // Compteur des classes cochées : sans repère, on ne sait plus si le
        // panneau est restreint ou s'il montre tout l'établissement.
        var cases = Array.prototype.slice.call(document.querySelectorAll('[data-case-classe]'));
        var compte = document.querySelector('[data-compte-classes]');

        function majCompte() {
            if (!compte) return;
            var n = cases.filter(function (c) { return c.checked; }).length;
            compte.textContent = n ? n + ' sélectionnée' + (n > 1 ? 's' : '') : 'aucune : tout l\'établissement';
        }

        cases.forEach(function (c) { c.addEventListener('change', majCompte); });
        majCompte();

        // L'identifiant se retrouve dans l'adresse : on prévient tout de suite
        // s'il est déjà pris, plutôt qu'au moment d'enregistrer.
        var champ = document.querySelector('[data-identifiant]');
        var aide = document.querySelector('[data-identifiant-aide]');
        if (!champ || !aide) return;

        var modele = aide.innerHTML;
        var minuteur = null;

        champ.addEventListener('input', function () {
            clearTimeout(minuteur);
            minuteur = setTimeout(function () {
                var valeur = champ.value.trim();
                aide.classList.remove('is-erreur');

                if (!valeur) {
                    aide.innerHTML = modele;
                    return;
                }

                fetch('{{ route('panneaux.check-identifiant') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        identifiant_panneaux: valeur,
                        id_panneau: {{ $panneau->id_panneau ?? 'null' }},
                    }),
                })
                    .then(function (r) { return r.json(); })
                    .then(function (d) {
                        if (d.status === 'success') {
                            aide.textContent = d.message;
                            aide.classList.add('is-erreur');
                        } else {
                            aide.innerHTML = 'Adresse du panneau : <code>/panneau/' + valeur + '</code>';
                        }
                    })
                    .catch(function () { aide.innerHTML = modele; });
            }, 350);
        });
    })();
</script>
