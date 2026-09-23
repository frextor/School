{{--
    Formulaire d'un créneau d'emploi du temps, partagé par la création et la
    modification. Attend $creneau (éventuellement vide) et les listes.
--}}
@php
    $format = fn ($valeur) => $valeur
        ? \Illuminate\Support\Carbon::parse($valeur)->format('Y-m-d\TH:i')
        : '';
    $semestre = (int) old('semestre', $creneau->semestre ?: 1);
@endphp

<section class="form-card">
    <div class="form-head">
        <h2>Le cours</h2>
        <span class="form-sub">Qui enseigne quoi, et à quelle classe.</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span>Enseignant</span>
            <select name="id_intervenant" required>
                @foreach ($intervenants as $intervenant)
                    <option value="{{ $intervenant->id_intervenant }}" @selected(old('id_intervenant', $creneau->id_intervenant) == $intervenant->id_intervenant)>{{ $intervenant->nom }} {{ $intervenant->prenom }}</option>
                @endforeach
            </select>
        </label>

        <label class="field">
            <span>Matière</span>
            <select name="id_cours" required>
                @foreach ($cours as $matiere)
                    <option value="{{ $matiere->id_cours }}" @selected(old('id_cours', $creneau->id_cours) == $matiere->id_cours)>{{ $matiere->nom_cours }}</option>
                @endforeach
            </select>
        </label>

        <label class="field">
            <span>Classe</span>
            <select name="id_classe">
                <option value="">Aucune classe précise</option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe->id_classe }}" @selected(old('id_classe', $creneau->id_classe) == $classe->id_classe)>{{ $classe->classe }}</option>
                @endforeach
            </select>
        </label>

        <label class="field">
            <span>Établissement</span>
            <select name="id_etablissement" required>
                @foreach ($etablissements as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected(old('id_etablissement', $creneau->id_etablissement) == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
        </label>

        <label class="field">
            <span>Salle</span>
            {{-- `id_salle` est une colonne texte portant l'identifiant : le
                 champ libre laissait écrire n'importe quoi, et l'affichage
                 montrait alors « Salle 43 » au lieu du nom de la salle. --}}
            <select name="id_salle">
                <option value="">Aucune salle</option>
                @foreach ($salles as $salle)
                    <option value="{{ $salle->id_salle }}" @selected((string) old('id_salle', $creneau->id_salle) === (string) $salle->id_salle)>{{ $salle->nom_salle }} ({{ $salle->code_salle }})</option>
                @endforeach
            </select>
        </label>
    </div>
</section>

<section class="form-card">
    <div class="form-head"><h2>Quand</h2></div>

    <div class="form-grid">
        <label class="field">
            <span>Début</span>
            <input type="datetime-local" name="date_debut" required
                   value="{{ old('date_debut', $format($creneau->date_debut)) }}">
        </label>

        <label class="field">
            <span>Fin</span>
            <input type="datetime-local" name="date_fin" required
                   value="{{ old('date_fin', $format($creneau->date_fin)) }}">
        </label>

        <label class="field">
            <span>Année scolaire</span>
            <input type="number" name="annee" required value="{{ old('annee', $creneau->annee ?: date('Y')) }}">
        </label>

        <div class="field">
            <span class="field-label">Semestre</span>
            <div class="seg-group">
                @foreach ([1, 2] as $s)
                    <label class="seg-opt">
                        <input type="radio" name="semestre" value="{{ $s }}" @checked($semestre === $s) required>
                        <span>S{{ $s }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <label class="field field-full">
            <span>Annotation</span>
            <textarea name="annotation" rows="3" placeholder="Salle changée, cours dédoublé…">{{ old('annotation', $creneau->annotation) }}</textarea>
        </label>
    </div>
</section>
