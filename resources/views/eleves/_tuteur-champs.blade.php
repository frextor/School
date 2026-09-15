{{-- Champs d'un parent / tuteur, partagés par le formulaire d'ajout et celui
     de modification. Paramètres : $tuteur (null à la création), $lien, $pivot. --}}
<div class="tut-grid">
    <label class="stack" style="flex:0 1 110px">
        <span>Civilité</span>
        <select name="civilite">
            @foreach (['M.', 'Mme'] as $civilite)
                <option value="{{ $civilite }}" @selected(($tuteur->civilite ?? '') === $civilite)>{{ $civilite }}</option>
            @endforeach
        </select>
    </label>
    <label class="stack">
        <span>Nom</span>
        <input type="text" name="nom" value="{{ $tuteur->nom ?? '' }}" required>
    </label>
    <label class="stack">
        <span>Prénom</span>
        <input type="text" name="prenom" value="{{ $tuteur->prenom ?? '' }}" required>
    </label>
    <label class="stack">
        <span>Lien de parenté</span>
        <select name="lien_parente" required>
            @foreach (\App\Models\Tuteur::LIENS as $valeur => $libelle)
                <option value="{{ $valeur }}" @selected($lien === $valeur)>{{ $libelle }}</option>
            @endforeach
        </select>
    </label>
</div>

<div class="tut-grid">
    <label class="stack">
        <span>Téléphone</span>
        <input type="tel" name="telephone" value="{{ $tuteur->telephone ?? '' }}" placeholder="06 00 00 00 00">
    </label>
    <label class="stack">
        <span>Téléphone pro</span>
        <input type="tel" name="telephone_pro" value="{{ $tuteur->telephone_pro ?? '' }}">
    </label>
    <label class="stack" style="flex:1 1 220px">
        <span>Email</span>
        <input type="email" name="email" value="{{ $tuteur->email ?? '' }}">
    </label>
</div>

<div class="tut-grid">
    <label class="stack">
        <span>Profession</span>
        <input type="text" name="profession" value="{{ $tuteur->profession ?? '' }}">
    </label>
    <label class="stack">
        <span>CIN</span>
        <input type="text" name="cin" value="{{ $tuteur->cin ?? '' }}" placeholder="Carte d'identité">
    </label>
    <label class="stack">
        <span>Ville</span>
        <input type="text" name="ville" value="{{ $tuteur->ville ?? '' }}">
    </label>
    <label class="stack">
        <span>Code postal</span>
        <input type="text" name="code_postal" value="{{ $tuteur->code_postal ?? '' }}">
    </label>
</div>

<label class="stack">
    <span>Adresse</span>
    <input type="text" name="adresse" value="{{ $tuteur->adresse ?? '' }}">
</label>

<label class="check"><input type="checkbox" name="responsable_legal" value="1" @checked($pivot?->responsable_legal ?? true)> Responsable légal</label>
<label class="check"><input type="checkbox" name="contact_urgence" value="1" @checked($pivot?->contact_urgence ?? false)> Contact d'urgence</label>
