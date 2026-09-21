{{--
    Liste d'appel : une ligne par élève, trois états exclusifs.
    Partagée par l'écran d'administration et par l'espace enseignant.

    Attendus :
    - $eleves      collection d'Eleve (avec `contact`)
    - $existantes  saisies d'assiduité déjà enregistrées, indexées par id_eleve
--}}
<div class="appel-list">
    @foreach ($eleves as $eleve)
        @php
            $existante = $existantes->get($eleve->id_eleve);
            $statut = $existante ? $existante->nature : 'present';
        @endphp
        <div class="appel-row">
            <span class="cell-avatar">{{ mb_strtoupper(mb_substr($eleve->contact?->prenom ?? '?', 0, 1).mb_substr($eleve->contact?->nom ?? '', 0, 1)) }}</span>
            <span class="appel-nom">
                {{ $eleve->contact?->nom_complet ?? 'Élève n°'.$eleve->id_eleve }}
                @if ($existante?->justifie)
                    <span class="appel-just">justifiée</span>
                @endif
            </span>

            <div class="appel-choix">
                @foreach (['present' => 'Présent', 'absence' => 'Absent', 'retard' => 'Retard'] as $valeur => $libelle)
                    <label class="appel-opt is-{{ $valeur }} {{ $statut === $valeur ? 'is-on' : '' }}">
                        <input type="radio" name="statuts[{{ $eleve->id_eleve }}]" value="{{ $valeur }}"
                               @checked($statut === $valeur)>
                        {{ $libelle }}
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

@once
<style>
    /* ---------- Liste d'appel (partials/appel-liste) ---------- */
    .appel-list { display: flex; flex-direction: column; }
    .appel-row { display: flex; align-items: center; gap: 12px; padding: 11px 16px; border-bottom: 1px solid #f6f7fa; flex-wrap: wrap; }
    .appel-row:last-child { border-bottom: 0; }
    .appel-row:hover { background: #fafbff; }
    .cell-avatar {
        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; background: var(--brand-light);
        color: var(--brand-deep); font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center;
    }
    .appel-nom { flex: 1 1 200px; min-width: 0; font-size: 13.5px; font-weight: 600; }
    .appel-just { margin-left: 7px; padding: 1px 7px; border-radius: 999px; background: #e7f6f2; color: #0f766e; font-size: 10.5px; font-weight: 700; }

    .appel-choix { display: flex; gap: 6px; flex-shrink: 0; }
    .appel-opt {
        margin: 0; padding: 7px 13px; border: 1px solid var(--border); border-radius: 999px; background: #fff;
        color: var(--muted); font-size: 12.5px; font-weight: 600; cursor: pointer;
        transition: border-color .14s ease, background .14s ease, color .14s ease;
    }
    .appel-opt input { position: absolute; opacity: 0; width: 0; height: 0; }
    .appel-opt:hover { border-color: #c3c6f5; }
    .appel-opt.is-present.is-on { border-color: #c3e6da; background: #e7f6f2; color: #0f766e; }
    .appel-opt.is-absence.is-on { border-color: #fecaca; background: var(--danger-bg); color: var(--danger-dark); }
    .appel-opt.is-retard.is-on { border-color: #f5d9a8; background: #fdf3e3; color: #b45309; }
</style>

<script>
    (function () {
        // Surlignage de l'option choisie.
        document.querySelectorAll('.appel-opt input').forEach(function (input) {
            input.addEventListener('change', function () {
                input.closest('.appel-choix').querySelectorAll('.appel-opt').forEach(function (opt) {
                    opt.classList.toggle('is-on', opt.querySelector('input').checked);
                });
            });
        });

        // Raccourcis « tous présents » / « tous absents ».
        document.querySelectorAll('[data-tous]').forEach(function (bouton) {
            bouton.addEventListener('click', function () {
                document.querySelectorAll('.appel-opt.is-' + bouton.dataset.tous + ' input').forEach(function (input) {
                    input.checked = true;
                    input.dispatchEvent(new Event('change'));
                });
            });
        });
    })();
</script>
@endonce
