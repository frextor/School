{{--
    Formulaire d'une période scolaire, partagé par la création et la modification.
    Attend $periode (éventuellement vide), $niveaux, $classes.
--}}
@php
    $niveauxChoisis = collect(old('id_niveau', $periode->exists ? $periode->niveaux->pluck('id_niveau')->all() : []))
        ->map(fn ($v) => (int) $v);
    $classesChoisies = collect(old('id_classe', $periode->exists ? $periode->classes->pluck('id_classe')->all() : []))
        ->map(fn ($v) => (int) $v);
    $trimestres = $periode->exists && $periode->periodesTrimestrielles->isNotEmpty()
        ? $periode->periodesTrimestrielles
        : collect([null]);
@endphp

<section class="form-card">
    <div class="form-head">
        <h2>La période</h2>
        <span class="form-sub">Une année scolaire découpée, avec son volume horaire.</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span>Année scolaire</span>
            <input type="text" name="annee_scolaire" required placeholder="2025/2026"
                   value="{{ old('annee_scolaire', $periode->annee_scolaire) }}">
        </label>

        <label class="field">
            <span>Intitulé de la période</span>
            <input type="text" name="periode" required placeholder="Année complète, Semestre 1…"
                   value="{{ old('periode', $periode->periode) }}">
        </label>

        <label class="field">
            <span>Heures sur l'année</span>
            <input type="number" step="0.01" min="0" name="nb_heure_annuel"
                   value="{{ old('nb_heure_annuel', $periode->nb_heure_annuel) }}">
        </label>

        <label class="field">
            <span>Diplôme RNCP</span>
            <input type="text" name="diplome_rncp" value="{{ old('diplome_rncp', $periode->diplome_rncp) }}">
        </label>

        <label class="field">
            <span>Code diplôme</span>
            <input type="text" name="code_diplome" value="{{ old('code_diplome', $periode->code_diplome) }}">
        </label>
    </div>
</section>

<section class="form-card">
    <div class="form-head">
        <h2>Portée</h2>
        <span class="form-sub">Sans sélection, la période ne s'applique à personne.</span>
    </div>

    <div class="form-body">
        <span class="field-label">Niveaux concernés</span>
        <div class="pick-list">
            @foreach ($niveaux as $niveau)
                <label class="pick">
                    <input type="checkbox" name="id_niveau[]" value="{{ $niveau->id_niveau }}"
                           @checked($niveauxChoisis->contains((int) $niveau->id_niveau))>
                    <span>{{ $niveau->nom_niveau }}</span>
                </label>
            @endforeach
        </div>

        <span class="field-label" style="margin-top:18px">Classes concernées</span>
        @if ($classes->isEmpty())
            <p class="field-aide" style="margin:0">Aucune classe enregistrée.</p>
        @else
            <div class="pick-list">
                @foreach ($classes as $classe)
                    <label class="pick">
                        <input type="checkbox" name="id_classe[]" value="{{ $classe->id_classe }}"
                               @checked($classesChoisies->contains((int) $classe->id_classe))>
                        <span>{{ $classe->classe }}</span>
                    </label>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="form-card">
    <div class="form-head">
        <h2>Découpage trimestriel</h2>
        <span class="form-sub">Remplace entièrement le découpage précédent à l'enregistrement.</span>
    </div>

    <div class="form-body">
        <div data-trimestres>
            @foreach ($trimestres as $trimestre)
                <div class="tri-ligne">
                    <label class="field">
                        <span>Période</span>
                        <input type="text" name="periode_trimestrielle[]" placeholder="01/09/2025 - 31/12/2025"
                               value="{{ $trimestre?->periode }}">
                    </label>
                    <label class="field tri-heures">
                        <span>Heures</span>
                        <input type="number" step="0.01" min="0" name="nb_heure_trimestriel[]"
                               value="{{ $trimestre?->nb_heure }}">
                    </label>
                </div>
            @endforeach
        </div>

        {{-- Une année se découpe en trois trimestres au Maroc : il fallait
             pouvoir en ajouter, le gabarit n'en proposait qu'un. --}}
        <button type="button" class="ajout-ligne" data-ajouter-trimestre>
            @include('partials.icon', ['n' => 'plus', 's' => 14, 'w' => 2.2])Ajouter un trimestre
        </button>
    </div>
</section>

<style>
    .tri-ligne { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
    .tri-ligne + .tri-ligne { margin-top: 11px; }
    .tri-ligne .field { flex: 1 1 260px; }
    .tri-ligne .tri-heures { flex: 0 1 130px; }
    .ajout-ligne {
        display: inline-flex; align-items: center; gap: 6px; margin-top: 13px; padding: 8px 13px;
        border-radius: 9px; border: 1px dashed #c9cdd9; background: #fff; color: var(--brand);
        font-family: inherit; font-size: 12.5px; font-weight: 600; cursor: pointer; box-shadow: none;
    }
    .ajout-ligne:hover { border-color: #a5a8f0; background: #fafbff; box-shadow: none; }
    .ajout-ligne svg { stroke: currentColor; }
</style>

<script>
    (function () {
        var zone = document.querySelector('[data-trimestres]');
        var bouton = document.querySelector('[data-ajouter-trimestre]');
        if (!zone || !bouton) return;

        bouton.addEventListener('click', function () {
            var copie = zone.querySelector('.tri-ligne').cloneNode(true);
            copie.querySelectorAll('input').forEach(function (champ) { champ.value = ''; });
            zone.appendChild(copie);
            copie.querySelector('input').focus();
        });
    })();
</script>
