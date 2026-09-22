{{--
    Formulaire d'une signature, partagé par la création et la modification.
    Attend $signature (éventuellement vide) et $etablissements.
--}}
@php
    $civilite = old('civilite', $signature->civilite ?: 'M');
    $imageActuelle = $signature->exists ? $signature->cheminFichier() : null;
@endphp

<section class="panel sg-bloc">
    <div class="panel-head"><h2>Le signataire</h2></div>

    <div class="sg-grid-champs">
        <div class="sg-champ">
            <span class="sg-label">Civilité</span>
            {{-- Deux valeurs : des boutons plutôt qu'une liste déroulante. --}}
            <div class="sg-segment">
                @foreach (['M' => 'M.', 'Mme' => 'Mme'] as $valeur => $libelle)
                    <label class="sg-seg">
                        <input type="radio" name="civilite" value="{{ $valeur }}" @checked($civilite === $valeur) required>
                        <span>{{ $libelle }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <label class="sg-champ">
            <span class="sg-label">Nom du directeur</span>
            <input type="text" name="nom_directeur" maxlength="70" required
                   placeholder="Nom et prénom" value="{{ old('nom_directeur', $signature->nom_directeur) }}">
        </label>

        <label class="sg-champ">
            <span class="sg-label">Fonction</span>
            <input type="text" name="fonction" maxlength="150" required
                   placeholder="Directeur, Directrice pédagogique…" value="{{ old('fonction', $signature->fonction) }}">
        </label>

        <label class="sg-champ">
            <span class="sg-label">Établissement</span>
            <select name="id_etablissement" required>
                @foreach ($etablissements as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $signature->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <label class="sg-principale">
        <input type="checkbox" name="principal" value="1" @checked(old('principal', $signature->principal))>
        <span>
            <strong>Signature principale de l'établissement</strong>
            Elle sera apposée par défaut sur les documents officiels. Un seul signataire principal par établissement : désigner celui-ci libère l'autre.
        </span>
    </label>
</section>

<section class="panel sg-bloc">
    <div class="panel-head">
        <h2>La signature scannée</h2>
        <span class="panel-sub">Image JPG ou PNG, 2 Mo au maximum. Un fond transparent (PNG) s'appose plus proprement sur un document.</span>
    </div>

    <div class="sg-image">
        <div class="sg-apercu" data-apercu>
            @if ($imageActuelle)
                <img src="{{ Storage::disk('public')->url($imageActuelle) }}" alt="Signature actuelle" data-image>
            @else
                <span class="sg-apercu-vide" data-vide>
                    @include('partials.icon', ['n' => 'pen', 's' => 22, 'c' => '#c9cdd9', 'w' => 1.7])
                    Aucune image
                </span>
                <img alt="Aperçu de la signature" data-image hidden>
            @endif
        </div>

        <div class="sg-image-actions">
            <label class="sg-fichier">
                <input type="file" name="signature" accept="image/jpeg,image/png" data-fichier>
                <span>{{ $imageActuelle ? "Remplacer l'image" : 'Choisir une image' }}</span>
            </label>
            <p class="sg-aide" data-nom-fichier>Aucun fichier sélectionné.</p>

            @if ($imageActuelle)
                <label class="sg-retirer">
                    <input type="checkbox" name="supprimer_signature" value="1">
                    <span>Supprimer l'image actuelle</span>
                </label>
            @endif
        </div>
    </div>
</section>

<style>
    .sg-form-page { max-width: 820px; margin: 0; }
    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-head { display: flex; align-items: baseline; gap: 10px; flex-wrap: wrap; padding: 14px 16px 12px; border-bottom: 1px solid var(--border-soft); }
    .panel-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head .panel-sub { font-size: 12px; color: var(--muted); }
    .sg-bloc + .sg-bloc { margin-top: 14px; }

    .sg-grid-champs { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; padding: 16px; }
    .sg-champ { display: block; margin: 0; min-width: 0; }
    .sg-label { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .sg-champ input[type="text"], .sg-champ select { width: 100%; max-width: none; }

    .sg-segment { display: flex; gap: 6px; }
    .sg-seg { margin: 0; }
    .sg-seg input { position: absolute; opacity: 0; width: 0; height: 0; }
    .sg-seg span {
        display: inline-flex; align-items: center; justify-content: center; min-width: 62px; padding: 9px 14px;
        border: 1px solid var(--border); border-radius: 9px; background: #fff;
        font-size: 13px; font-weight: 600; color: #585e72; cursor: pointer;
    }
    .sg-seg span:hover { border-color: #c3c6f5; }
    .sg-seg input:checked + span { border-color: var(--brand); background: var(--brand-light); color: var(--brand-deep); }
    .sg-seg input:focus-visible + span { outline: 2px solid var(--brand); outline-offset: 2px; }

    label.sg-principale {
        display: flex; gap: 11px; margin: 0 16px 16px;
        padding: 12px 14px; border: 1px solid var(--border); border-radius: 11px; cursor: pointer;
    }
    .sg-principale:has(input:checked) { border-color: var(--brand); background: #fafbff; }
    /* `label:has(input[type=checkbox])` du layout centre ses éléments et bat
       une simple classe : `align-self` sur la case tranche sans surenchère. */
    .sg-principale input {
        width: 15px; height: 15px; max-width: none; margin: 2px 0 0;
        align-self: flex-start; accent-color: var(--brand); flex-shrink: 0;
    }
    .sg-principale strong { display: block; font-size: 13px; margin-bottom: 2px; }
    .sg-principale span { font-size: 12px; color: var(--muted); line-height: 1.45; }

    .sg-image { display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-start; padding: 16px; }
    .sg-apercu {
        display: flex; align-items: center; justify-content: center; flex: 0 1 280px; min-width: 200px; height: 120px;
        padding: 10px; border: 1px dashed var(--border); border-radius: 12px; background: #fcfcfe;
    }
    .sg-apercu img { max-height: 100%; max-width: 100%; object-fit: contain; }
    .sg-apercu img[hidden] { display: none; }
    .sg-apercu-vide { display: flex; flex-direction: column; align-items: center; gap: 6px; font-size: 12px; color: var(--faint); }
    .sg-apercu-vide[hidden] { display: none; }

    .sg-image-actions { flex: 1 1 240px; min-width: 0; }
    .sg-fichier { display: inline-block; margin: 0; }
    .sg-fichier input { position: absolute; opacity: 0; width: 0; height: 0; }
    .sg-fichier span {
        display: inline-flex; align-items: center; padding: 9px 14px; border-radius: 9px;
        border: 1px dashed #c9cdd9; background: #fff; color: var(--brand);
        font-size: 13px; font-weight: 600; cursor: pointer;
    }
    .sg-fichier span:hover { border-color: #a5a8f0; background: #fafbff; }
    .sg-fichier input:focus-visible + span { outline: 2px solid var(--brand); outline-offset: 2px; }
    .sg-aide { margin: 8px 0 0; font-size: 11.5px; color: var(--muted); }
    .sg-retirer { display: flex; align-items: center; gap: 8px; margin: 12px 0 0; font-size: 12.5px; color: #585e72; cursor: pointer; }
    .sg-retirer input { width: 14px; height: 14px; max-width: none; margin: 0; accent-color: var(--danger); }

    .sg-pied { display: flex; align-items: center; gap: 10px; margin-top: 16px; }
    .sg-pied .btn:last-child { margin-left: auto; }
</style>

<script>
    (function () {
        // Aperçu immédiat : on ne devine pas au nom du fichier si c'est la
        // bonne signature que l'on vient de choisir.
        var champ = document.querySelector('[data-fichier]');
        if (!champ) return;

        var image = document.querySelector('[data-image]');
        var vide = document.querySelector('[data-vide]');
        var nom = document.querySelector('[data-nom-fichier]');

        champ.addEventListener('change', function () {
            var fichier = champ.files && champ.files[0];
            if (!fichier) return;

            if (nom) nom.textContent = fichier.name;
            if (image) {
                image.src = URL.createObjectURL(fichier);
                image.hidden = false;
            }
            if (vide) vide.hidden = true;
        });
    })();
</script>
