@extends('layouts.app')

@section('title', "Modifier l'épreuve")

@section('content')
    <a href="{{ route('epreuves.index') }}">&larr; Retour</a>
    <h1>Modifier l'épreuve du {{ $epreuve->date_epreuve->format('d/m/Y') }}</h1>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('epreuves.update', $epreuve) }}">
        @csrf
        @method('PUT')

        <label for="date">Date</label>
        <input type="date" name="date" id="date" value="{{ old('date', $epreuve->date_epreuve->format('Y-m-d')) }}" required>

        <label for="heure">Heure</label>
        <input type="time" name="heure" id="heure" value="{{ old('heure', $epreuve->date_epreuve->format('H:i')) }}" required>

        <label for="lieu">Lieu</label>
        <input type="text" name="lieu" id="lieu" maxlength="16" value="{{ old('lieu', $epreuve->lieu) }}" required>

        <label for="effectif">Effectif max</label>
        <input type="number" name="effectif" id="effectif" value="{{ old('effectif', $epreuve->effectif) }}" required>

        <label><input type="checkbox" name="distanciel" value="1" @checked(old('distanciel', $epreuve->distanciel))> Distanciel</label>

        <label for="url_distanciel">URL distanciel</label>
        <input type="url" name="url_distanciel" id="url_distanciel" value="{{ old('url_distanciel', $epreuve->url_distanciel) }}">

        @php $formationsSelectionnees = old('id_formation', $epreuve->formations->pluck('id_formation')->all()); @endphp
        <label for="id_formation">Formations concernées</label>
        <select name="id_formation[]" id="id_formation" multiple size="6">
            @foreach ($formations as $formation)
                <option value="{{ $formation->id_formation }}" @selected(collect($formationsSelectionnees)->contains($formation->id_formation))>{{ $formation->niveau }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>

    <h2 style="margin-top:2rem">Candidats inscrits</h2>

    @forelse ($epreuve->inscriptions as $inscription)
        @php $resultat = $resultatsParEleve->get($inscription->id_eleve); @endphp
        <div style="border:1px solid #e5e5e5;border-radius:8px;padding:0.75rem;margin-bottom:0.75rem">
            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap">
                <strong style="flex:1">{{ $inscription->eleve?->contact?->nom_complet ?? 'Candidat #'.$inscription->id_eleve }}</strong>

                <form method="post" action="{{ route('epreuves.toggle-presence', $inscription) }}">
                    @csrf
                    <label class="check" style="margin:0">
                        <input type="checkbox" onchange="this.form.requestSubmit()" @checked($inscription->presence)>
                        Présent
                    </label>
                </form>

                @if ($resultat)
                    <span class="badge {{ $resultat->decision === 'accepte' ? 'badge-success' : ($resultat->decision === 'refuse' ? 'badge-danger' : 'badge-brand') }}">
                        {{ match ($resultat->decision) {
                            'accepte' => 'Admis',
                            'accepter_niveau_inferieur' => 'Admis (niveau inférieur)',
                            'accepter_avec_entreprise' => 'Admis (avec entreprise)',
                            'refuse' => 'Refusé',
                            default => 'En attente',
                        } }}
                    </span>
                @endif

                <form method="post" action="{{ route('epreuves.suppression-candidat', [$epreuve, $inscription->id_eleve]) }}" onsubmit="return confirm('Retirer ce candidat de l\'épreuve ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost">Retirer</button>
                </form>
            </div>

            <details style="margin-top:0.5rem" @if($resultat) open @endif>
                <summary>{{ $resultat ? 'Modifier les notes' : 'Saisir les notes' }}</summary>

                <form method="post" action="{{ route('epreuves.resultats.store', $epreuve) }}" style="margin-top:0.5rem;display:flex;gap:0.5rem;flex-wrap:wrap;align-items:end">
                    @csrf
                    <input type="hidden" name="id_eleve" value="{{ $inscription->id_eleve }}">

                    <span>
                        <label>Anglais</label><br>
                        <input type="number" name="anglais" min="0" max="20" style="width:4.5rem" value="{{ $resultat->anglais ?? '' }}" required>
                    </span>
                    <span>
                        <label>Culture G.</label><br>
                        <input type="number" name="culture_generale" min="0" max="20" style="width:4.5rem" value="{{ $resultat->culture_generale ?? '' }}" required>
                    </span>
                    <span>
                        <label>Rédaction</label><br>
                        <input type="number" name="epreuve_redaction" min="0" max="20" style="width:4.5rem" value="{{ $resultat->epreuve_redaction ?? '' }}" required>
                    </span>
                    <span>
                        <label>Entretien</label><br>
                        <input type="number" name="entretien" min="0" max="20" style="width:4.5rem" value="{{ $resultat->entretien ?? '' }}" required>
                    </span>
                    <span>
                        <label>Décision</label><br>
                        <select name="decision" required>
                            <option value="en_attente" @selected(($resultat->decision ?? 'en_attente') === 'en_attente')>En attente</option>
                            <option value="accepte" @selected(($resultat->decision ?? '') === 'accepte')>Admis</option>
                            <option value="accepter_niveau_inferieur" @selected(($resultat->decision ?? '') === 'accepter_niveau_inferieur')>Admis (niveau inférieur)</option>
                            <option value="accepter_avec_entreprise" @selected(($resultat->decision ?? '') === 'accepter_avec_entreprise')>Admis (avec entreprise)</option>
                            <option value="refuse" @selected(($resultat->decision ?? '') === 'refuse')>Refusé</option>
                        </select>
                    </span>
                    <span>
                        <label>Motif de refus</label><br>
                        <select name="id_motif_refus">
                            <option value="">--</option>
                            @foreach ($motifsRefus as $motif)
                                <option value="{{ $motif->id_motif_refus }}" @selected(($resultat->id_motif_refus ?? null) == $motif->id_motif_refus)>{{ $motif->libelle }}</option>
                            @endforeach
                        </select>
                    </span>

                    <button type="submit" class="btn">Enregistrer</button>
                </form>

                @if ($resultat)
                    <form method="post" action="{{ route('epreuves.resultats.destroy', $resultat) }}" style="margin-top:0.5rem" onsubmit="return confirm('Supprimer ce résultat ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Supprimer le résultat</button>
                    </form>
                @endif
            </details>
        </div>
    @empty
        <p>Aucun candidat inscrit à cette épreuve.</p>
    @endforelse

    <h3 style="margin-top:1rem">Inscrire un candidat</h3>
    @if ($candidatsDisponibles->isEmpty())
        <p style="color:#666">Tous les candidats sont déjà inscrits, ou aucun candidat n'existe pour l'instant.</p>
    @else
        <form method="post" action="{{ route('epreuves.inscrire', $epreuve) }}" style="display:flex;gap:0.5rem;align-items:end">
            @csrf
            <span style="flex:1">
                <label for="id_eleve_inscription">Candidat</label>
                <select name="id_eleve" id="id_eleve_inscription" required>
                    @foreach ($candidatsDisponibles as $candidat)
                        <option value="{{ $candidat->id_eleve }}">{{ $candidat->contact->nom_complet }}</option>
                    @endforeach
                </select>
            </span>
            <button type="submit" class="btn">Inscrire</button>
        </form>
    @endif
@endsection
