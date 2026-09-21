@extends('layouts.app')

@section('title', 'Fiche élève')

@section('content')
@php
    $contact = $eleve->contact;
    $nom = $contact?->nom_complet ?? 'Élève #'.$eleve->id_eleve;
    $initiales = mb_strtoupper(mb_substr($contact?->prenom ?? '?', 0, 1).mb_substr($contact?->nom ?? '', 0, 1));


    $profilTint = match ($eleve->profil) {
        'eleve' => ['#eef0fe', '#3730a3'],
        'candidat' => ['#fdf3e3', '#92400e'],
        'alumni' => ['#f4ecfd', '#6d28d9'],
        'reinscrit' => ['#e7f6f2', '#0f766e'],
        'abandon' => ['#fdecef', '#be123c'],
        default => ['#eef1f6', '#475569'],
    };

    // Onglets secondaires : affichés seulement si le contrôleur fournit les données.
    $paiements = $paiements ?? null;
    $notesParMatiere = $notesParMatiere ?? null;
    $assiduite = $assiduite ?? collect();
    $documents = $documents ?? null;

    // Le montant fait foi côté échéancier : `montant_formation` est une
    // colonne héritée, vide depuis que la facturation passe par les
    // échéances. On ne retombe dessus que faute d'échéancier.
    $echeancier = $paiements ? collect($paiements) : collect();
    $montant = $echeancier->isNotEmpty()
        ? (float) $echeancier->sum('montant')
        : (float) ($eleve->montant_formation ?: 0);
    $encaisse = $echeancier->isNotEmpty() ? (float) $echeancier->where('encaisse', true)->sum('montant') : null;
    $reste = $encaisse === null ? null : max(0, $montant - $encaisse);
    $progression = $montant > 0 && $encaisse !== null ? min(100, round($encaisse / $montant * 100)) : null;
    $enRetard = $echeancier->filter(fn ($e) => ! $e->encaisse && $e->date_echeance && $e->date_echeance->isPast())->count();
    $dh = fn ($v) => number_format((float) $v, 0, ',', ' ').' DH';

    // Statut de règlement déduit de l'échéancier plutôt que de la colonne
    // `paiement_formation`, qui n'est plus tenue à jour.
    [$statutPaiement, $paiementTint] = match (true) {
        $echeancier->isEmpty() => ['Aucun échéancier', ['#eef1f6', '#475569']],
        $reste !== null && $reste <= 0 => ['Soldé', ['#e7f6f2', '#0f766e']],
        $enRetard > 0 => [$enRetard.' échéance(s) en retard', ['#fdecef', '#be123c']],
        default => ['Règlement en cours', ['#fdf3e3', '#92400e']],
    };

    $profilLibelle = match ($eleve->profil) {
        'eleve' => 'Élève',
        'candidat' => 'Candidat',
        'alumni' => 'Ancien élève',
        'reinscrit' => 'Réinscrit',
        'abandon' => 'Abandon',
        default => ucfirst($eleve->profil ?: '—'),
    };

    $scolarite = [
        'Profil' => $profilLibelle,
        'Niveau' => $eleve->niveau?->nom_niveau ?? '—',
        'Niveau futur' => $eleve->niveauFuture?->nom_niveau ?? '—',
        'Classe' => $eleve->classe?->classe ?? '—',
        'Établissement' => $eleve->etablissement?->nom_etablissement ?? '—',
        'Année de formation' => $eleve->annee_formation ?? '—',
        "Date d'inscription" => $eleve->date_inscription?->format('d/m/Y') ?? '—',
        'Frais de scolarité' => $montant > 0 ? $dh($montant) : '—',
    ];

    // État civil : les champs facultatifs n'apparaissent que remplis, pour
    // ne pas aligner une colonne de tirets sur une école qui ne les saisit pas.
    $etatCivil = collect([
        'Civilité' => $contact?->civilite,
        'Sexe' => match (mb_strtolower((string) $contact?->sexe)) {
            'm' => 'Masculin',
            'f' => 'Féminin',
            default => null,
        },
        'Date de naissance' => $contact?->date_naissance
            ? \Illuminate\Support\Carbon::parse($contact->date_naissance)->format('d/m/Y')
                .' ('.\Illuminate\Support\Carbon::parse($contact->date_naissance)->age.' ans)'
            : null,
        'Lieu de naissance' => $contact?->lieu_naissance,
        'Pays de naissance' => $contact?->pays_naissance,
        'Nationalité' => $contact?->nationalite,
        'Langue maternelle' => $eleve->lang_maternelle,
        'Numéro Massar' => $eleve->numero_massar,
        'Numéro social' => $eleve->numero_social,
    ])->filter(fn ($v) => filled($v));

    $coordonnees = [
        'Email' => $contact?->email ?: '—',
        'Téléphone' => $contact?->telephone
            ?: ($eleve->tuteurs->sortByDesc(fn ($t) => (int) ($t->pivot->responsable_legal ?? 0))->first()?->telephone
                ? $eleve->tuteurs->sortByDesc(fn ($t) => (int) ($t->pivot->responsable_legal ?? 0))->first()->telephone.' (famille)'
                : '—'),
        'Adresse' => $contact?->adresse ?: '—',
        'Ville' => $contact?->ville ?: '—',
        'Code postal' => $contact?->code_postal ?: '—',
        'Pays' => $contact?->pays ?: '—',
    ];
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <a href="{{ route('eleves.index') }}">Élèves</a>
    <span class="sep">/</span>
    <span class="current">{{ $nom }}</span>
</div>

{{-- ---------- En-tête d'identité ---------- --}}
<div class="id-card">
    <div class="id-row">
        <span class="id-avatar">{{ $initiales }}</span>

        <div class="id-main">
            <div class="id-title">
                <h1>{{ $nom }}</h1>
                <span class="pill" style="background:{{ $profilTint[0] }};color:{{ $profilTint[1] }}">{{ $profilLibelle }}</span>
                <span class="pill dot-pill" style="background:{{ $eleve->visible ? '#e7f6f2' : '#fdecef' }};color:{{ $eleve->visible ? '#0f766e' : '#be123c' }}">
                    <span class="dot"></span>{{ $eleve->visible ? 'Visible' : 'Masqué' }}
                </span>
            </div>
            <p class="id-sub">
                {{ $eleve->niveau?->nom_niveau ?? '—' }}@if ($eleve->classe) — {{ $eleve->classe->classe }} @endif
                @if ($eleve->etablissement) · {{ $eleve->etablissement->nom_etablissement }} @endif
            </p>
            <div class="id-contacts">
                @if ($contact?->email)
                    <a href="mailto:{{ $contact->email }}">
                        @include('partials.icon', ['n' => 'mail', 's' => 15, 'c' => '#9aa0b0']){{ $contact->email }}
                    </a>
                @endif
                @if ($contact?->telephone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $contact->telephone) }}">
                        @include('partials.icon', ['n' => 'phone', 's' => 15, 'c' => '#9aa0b0']){{ $contact->telephone }}
                    </a>
                @endif
                <span class="id-meta">
                    @include('partials.icon', ['n' => 'tag', 's' => 15, 'c' => '#9aa0b0'])ID {{ $eleve->id_eleve }}
                </span>
            </div>
        </div>

        <div class="id-actions">
            @if (Route::has('bulletin-v2.create'))
                <a href="{{ route('bulletin-v2.create') }}" class="btn btn-ghost">
                    @include('partials.icon', ['n' => 'printer', 's' => 15, 'w' => 2])Bulletin PDF
                </a>
            @endif
            <a href="{{ route('eleves.edit', $eleve) }}" class="btn">
                @include('partials.icon', ['n' => 'pencil', 's' => 15, 'w' => 2])Modifier
            </a>
        </div>
    </div>

    <div class="tabs-bar" role="tablist">
        <button type="button" class="tab is-active" data-tab="scolarite" role="tab">Scolarité</button>
        <button type="button" class="tab" data-tab="famille" role="tab">
            Famille<span class="tab-badge">{{ $eleve->tuteurs->count() }}</span>
        </button>
        @if ($paiements !== null)
            <button type="button" class="tab" data-tab="reglements" role="tab">
                Règlements<span class="tab-badge">{{ count($paiements) }}</span>
            </button>
        @endif
        @if ($notesParMatiere !== null)
            <button type="button" class="tab" data-tab="notes" role="tab">
                Notes<span class="tab-badge">{{ collect($notesParMatiere)->sum(fn ($n) => count($n)) }}</span>
            </button>
        @endif
        @if ($assiduite->isNotEmpty())
            <button type="button" class="tab" data-tab="assiduite" role="tab">
                Assiduité<span class="tab-badge">{{ $assiduite->count() }}</span>
            </button>
        @endif
        @if ($documents !== null)
            <button type="button" class="tab" data-tab="documents" role="tab">
                Documents<span class="tab-badge">{{ count($documents) }}</span>
            </button>
        @endif
    </div>
</div>

{{-- ---------- Corps : contenu + colonne latérale ---------- --}}
<div class="detail-grid">
    <div class="detail-main">

        {{-- Scolarité --}}
        <div data-panel="scolarite">
            <section class="panel">
                <div class="panel-head"><h2>Scolarité</h2></div>
                <div class="field-grid">
                    @foreach ($scolarite as $label => $valeur)
                        <div class="field">
                            <div class="field-label">{{ $label }}</div>
                            <div class="field-value">{{ $valeur }}</div>
                        </div>
                    @endforeach
                </div>
            </section>

            @if ($etatCivil->isNotEmpty())
                <section class="panel">
                    <div class="panel-head">
                        <h2>État civil</h2>
                        @if ($contact && Route::has('contacts.edit'))
                            <a href="{{ route('contacts.edit', $contact) }}">Modifier</a>
                        @endif
                    </div>
                    <div class="field-grid">
                        @foreach ($etatCivil as $label => $valeur)
                            <div class="field">
                                <div class="field-label">{{ $label }}</div>
                                <div class="field-value is-regular">{{ $valeur }}</div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="panel">
                <div class="panel-head">
                    <h2>Coordonnées</h2>
                    @if ($contact && Route::has('contacts.edit'))
                        <a href="{{ route('contacts.edit', $contact) }}">Modifier le contact</a>
                    @endif
                </div>
                <div class="field-grid">
                    @foreach ($coordonnees as $label => $valeur)
                        <div class="field">
                            <div class="field-label">{{ $label }}</div>
                            <div class="field-value is-regular">{{ $valeur }}</div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- Famille : parents / tuteurs légaux (K-12) --}}
        <div data-panel="famille" hidden>
            <section class="panel">
                <div class="panel-head">
                    <h2>Parents / tuteurs</h2>
                    <span class="panel-sub">{{ $eleve->tuteurs->count() }} rattaché(s) à cet élève</span>
                </div>

                @forelse ($eleve->tuteurs as $tuteur)
                    <div class="tut">
                        <div class="tut-row">
                            <span class="tut-avatar">{{ mb_strtoupper(mb_substr($tuteur->prenom, 0, 1).mb_substr($tuteur->nom, 0, 1)) }}</span>
                            <span class="tut-text">
                                <span class="tut-name">{{ $tuteur->nom_complet }}</span>
                                <span class="tut-sub">
                                    {{ $tuteur->lien_libelle }}
                                    @if ($tuteur->profession) · {{ $tuteur->profession }} @endif
                                    @if ($tuteur->eleves_count > 1) · {{ $tuteur->eleves_count }} enfants dans l'école @endif
                                </span>
                            </span>

                            @if ($tuteur->pivot->responsable_legal)
                                <span class="pill" style="background:#eef0fe;color:#3730a3">Responsable légal</span>
                            @endif
                            @if ($tuteur->pivot->contact_urgence)
                                <span class="pill" style="background:#fdf3e3;color:#92400e">Urgence</span>
                            @endif

                            <button type="button" class="caret" data-tut-toggle aria-label="Modifier ce parent">
                                @include('partials.icon', ['n' => 'chevron-down', 's' => 15, 'c' => '#585e72', 'w' => 2.2])
                            </button>
                        </div>

                        <div class="tut-contacts">
                            @if ($tuteur->telephone)
                                <a href="tel:{{ preg_replace('/\s+/', '', $tuteur->telephone) }}">
                                    @include('partials.icon', ['n' => 'phone', 's' => 14, 'c' => '#9aa0b0']){{ $tuteur->telephone }}
                                </a>
                            @endif
                            @if ($tuteur->email)
                                <a href="mailto:{{ $tuteur->email }}">
                                    @include('partials.icon', ['n' => 'mail', 's' => 14, 'c' => '#9aa0b0']){{ $tuteur->email }}
                                </a>
                            @endif
                            @if ($tuteur->cin)
                                <span class="tut-meta">@include('partials.icon', ['n' => 'tag', 's' => 14, 'c' => '#9aa0b0'])CIN {{ $tuteur->cin }}</span>
                            @endif
                        </div>

                        <div class="tut-body" hidden>
                            <form method="post" action="{{ route('tuteurs.update', [$eleve, $tuteur]) }}" class="tut-form">
                                @csrf
                                @method('PUT')
                                @include('eleves._tuteur-champs', ['tuteur' => $tuteur, 'lien' => $tuteur->pivot->lien_parente, 'pivot' => $tuteur->pivot])

                                <div class="tut-actions">
                                    <button type="submit" class="btn">Enregistrer</button>
                                </div>
                            </form>

                            <form method="post" action="{{ route('tuteurs.destroy', [$eleve, $tuteur]) }}" class="inline-form"
                                  onsubmit="return confirm('Retirer ce parent de la fiche de cet élève ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Retirer de cet élève</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-cell">
                        @include('partials.icon', ['n' => 'contact', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                        <span>Aucun parent renseigné pour cet élève.</span>
                    </div>
                @endforelse

                <div class="tut-foot">
                    <details>
                        <summary>Ajouter un parent / tuteur</summary>

                        <form method="post" action="{{ route('tuteurs.store', $eleve) }}" class="tut-form" style="margin-top:12px">
                            @csrf
                            @include('eleves._tuteur-champs', ['tuteur' => null, 'lien' => 'pere', 'pivot' => null])
                            <div class="tut-actions">
                                <button type="submit" class="btn">Ajouter</button>
                            </div>
                        </form>
                    </details>

                    <details style="margin-top:8px">
                        <summary>Rattacher un parent déjà enregistré (fratrie)</summary>

                        <form method="post" action="{{ route('tuteurs.attacher', $eleve) }}" class="tut-form" style="margin-top:12px" id="form-attacher">
                            @csrf
                            <div class="tut-grid">
                                <label class="stack" style="flex:1 1 260px">
                                    <span>Rechercher un parent existant</span>
                                    <input type="text" id="tut-recherche" autocomplete="off" placeholder="Nom, téléphone ou email…">
                                    <input type="hidden" name="id_tuteur" id="tut-id" required>
                                    <div class="tut-results" id="tut-results" hidden></div>
                                </label>
                                <label class="stack">
                                    <span>Lien de parenté</span>
                                    <select name="lien_parente" required>
                                        @foreach (\App\Models\Tuteur::LIENS as $valeur => $libelle)
                                            <option value="{{ $valeur }}">{{ $libelle }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                            <label class="check"><input type="checkbox" name="responsable_legal" value="1" checked> Responsable légal</label>
                            <label class="check"><input type="checkbox" name="contact_urgence" value="1"> Contact d'urgence</label>
                            <div class="tut-actions">
                                <button type="submit" class="btn">Rattacher</button>
                            </div>
                        </form>
                    </details>
                </div>
            </section>
        </div>

        {{-- Règlements --}}
        @if ($paiements !== null)
            <div data-panel="reglements" hidden>
                <section class="panel">
                    <div class="panel-head">
                        <h2>Échéancier</h2>
                        <span class="panel-sub">{{ count($paiements) }} échéances @if ($montant > 0) · {{ $dh($montant) }} au total @endif</span>
                        @if (Route::has('paiements.index'))
                            <a href="{{ route('paiements.index', $eleve) }}">Tous les règlements</a>
                        @endif
                    </div>
                    <div class="table-scroll">
                        <table class="data-table" style="min-width:440px">
                            <thead>
                                <tr>
                                    <th>Échéance</th>
                                    <th>Moyen</th>
                                    <th class="num">Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paiements as $paiement)
                                    <tr>
                                        <td class="strong">{{ $paiement->date_echeance?->format('d/m/Y') ?? '—' }}</td>
                                        <td class="muted">{{ $paiement->moyen ?? '—' }}</td>
                                        <td class="num strong">{{ $dh($paiement->montant) }}</td>
                                        <td>
                                            <span class="dot-status" style="color:{{ $paiement->encaisse ? '#0f766e' : '#b45309' }}">
                                                <span class="dot"></span>{{ $paiement->encaisse ? 'Encaissé' : 'À échoir' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @endif

        {{-- Notes --}}
        @if ($notesParMatiere !== null)
            <div data-panel="notes" hidden>
                <section class="panel">
                    <div class="panel-head">
                        <h2>Notes</h2>
                        @isset($moyenneGenerale)
                            <span class="panel-sub">moyenne générale {{ number_format((float) $moyenneGenerale, 2, ',', ' ') }}</span>
                        @endisset
                        @if (Route::has('bulletin-v2.create'))
                            <a href="{{ route('bulletin-v2.create') }}">Bulletin PDF</a>
                        @endif
                    </div>
                    <div class="table-scroll">
                        <table class="data-table" style="min-width:400px">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Évaluation</th>
                                    <th>Date</th>
                                    <th class="num">Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notesParMatiere as $matiere => $notesMatiere)
                                    @foreach ($notesMatiere as $ligne)
                                        @php $valeur = is_numeric($ligne->note) ? (float) $ligne->note : null; @endphp
                                        <tr>
                                            <td class="strong">{{ $loop->first ? $matiere : '' }}</td>
                                            <td class="muted">{{ $ligne->evaluation?->typeEvaluation?->type?->type ?: 'Évaluation' }}</td>
                                            <td class="muted">{{ $ligne->date_saisie?->format('d/m/Y') ?: '—' }}</td>
                                            <td class="num note" @if ($valeur !== null && $valeur < 10) style="color:var(--danger)" @endif>
                                                {{ $valeur !== null ? number_format($valeur, 2, ',', ' ') : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @endif

        {{-- Documents --}}
        @if ($assiduite->isNotEmpty())
            <div data-panel="assiduite" hidden>
                <section class="panel">
                    <div class="panel-head">
                        <h2>Assiduité</h2>
                        <span class="panel-sub">
                            {{ $assiduite->filter(fn ($a) => $a->nature === \App\Models\AbsenceEleve::NATURE_ABSENCE)->count() }} absence(s)
                            · {{ $assiduite->filter(fn ($a) => $a->nature === \App\Models\AbsenceEleve::NATURE_RETARD)->count() }} retard(s)
                            depuis le {{ $depuis->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="table-scroll">
                        <table class="data-table" style="min-width:520px">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure</th>
                                    <th>Nature</th>
                                    <th>Cours</th>
                                    <th>Justification</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assiduite as $ligne)
                                    @php $estRetard = $ligne->nature === \App\Models\AbsenceEleve::NATURE_RETARD; @endphp
                                    <tr>
                                        <td class="strong">{{ $ligne->date_absence?->format('d/m/Y') ?: '—' }}</td>
                                        <td class="muted">{{ substr((string) $ligne->heure_absence, 0, 5) ?: '—' }}</td>
                                        <td>
                                            <span class="pill" style="background:{{ $estRetard ? '#fdf3e3' : '#fdecef' }};color:{{ $estRetard ? '#92400e' : '#be123c' }}">
                                                {{ $ligne->nature_libelle }}
                                            </span>
                                        </td>
                                        <td class="muted">{{ $ligne->cours?->nom_cours ?: '—' }}</td>
                                        <td class="muted">
                                            {{ $ligne->justifie ? 'Justifiée' : 'Non justifiée' }}
                                            @if ($ligne->annotation) · {{ $ligne->annotation }} @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @endif

        @if ($documents !== null)
            <div data-panel="documents" hidden>
                <section class="panel">
                    <div class="panel-head"><h2>Documents</h2></div>
                    <div class="doc-list">
                        @foreach ($documents as $document)
                            <a href="{{ $document->url ?? '#' }}" class="doc" target="_blank" rel="noopener">
                                <span class="doc-icon">@include('partials.icon', ['n' => 'file', 's' => 16, 'c' => '#585e72'])</span>
                                <span class="doc-text">
                                    <span class="doc-name">{{ $document->nom }}</span>
                                    <span class="doc-meta">{{ $document->meta ?? '' }}</span>
                                </span>
                                @include('partials.icon', ['n' => 'download', 's' => 15, 'c' => '#c9cdd9', 'w' => 2, 'style' => 'margin-left:auto'])
                            </a>
                        @endforeach
                    </div>
                </section>
            </div>
        @endif
    </div>

    {{-- Colonne latérale --}}
    <div class="detail-side">
        <section class="panel panel-pad">
            <div class="field-label">Règlement de la formation</div>
            <div class="money">
                <span class="money-value">{{ $montant > 0 ? $dh($montant) : '—' }}</span>
                <span class="money-hint">{{ $echeancier->count() ? $echeancier->count().' échéance'.($echeancier->count() > 1 ? 's' : '') : 'montant total' }}</span>
            </div>
            @if ($progression !== null)
                <div class="bar"><span style="width:{{ $progression }}%"></span></div>
                <div class="bar-legend">
                    <span class="is-ok">{{ $dh($encaisse) }} encaissés</span>
                    <span>{{ $dh($reste) }} restants</span>
                </div>
            @endif
            <div class="money-foot">
                <span class="pill" style="background:{{ $paiementTint[0] }};color:{{ $paiementTint[1] }}">{{ $statutPaiement }}</span>
                @if (Route::has('paiements.index'))
                    <a href="{{ route('paiements.index', $eleve) }}">Détail</a>
                @endif
            </div>
        </section>

        @isset($parcours)
            <section class="panel">
                <div class="panel-head"><h2>Parcours</h2></div>
                <div class="timeline">
                    @foreach ($parcours as $etape)
                        <div class="step">
                            <span class="step-dot"></span>
                            <span>
                                <span class="step-title">{{ $etape['titre'] }}</span>
                                <span class="step-date">{{ $etape['date'] }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endisset

        <section class="panel panel-pad">
            <h2 class="side-title">Actions</h2>
            <div class="side-actions">
                @if (Route::has('paiements.index'))
                    <a href="{{ route('paiements.index', $eleve) }}" class="side-action">
                        @include('partials.icon', ['n' => 'card', 's' => 15])Voir les règlements
                    </a>
                @endif
                @if (Route::has('bulletin-v2.create'))
                    <a href="{{ route('bulletin-v2.create') }}" class="side-action">
                        @include('partials.icon', ['n' => 'printer', 's' => 15])Générer un bulletin
                    </a>
                @endif
                <a href="{{ route('eleves.certificat-scolarite', $eleve) }}" class="side-action" target="_blank" rel="noopener">
                    @include('partials.icon', ['n' => 'file', 's' => 15])Certificat de scolarité
                </a>
                <a href="{{ route('eleves.certificat-radiation', $eleve) }}" class="side-action" target="_blank" rel="noopener">
                    @include('partials.icon', ['n' => 'file', 's' => 15])Certificat de radiation
                </a>
                @if ($contact?->email)
                    <a href="mailto:{{ $contact->email }}" class="side-action">
                        @include('partials.icon', ['n' => 'mail', 's' => 15])Envoyer un email
                    </a>
                @endif
                @if ($contact && Route::has('contacts.show'))
                    <a href="{{ route('contacts.show', $contact) }}" class="side-action">
                        @include('partials.icon', ['n' => 'contact', 's' => 15])Ouvrir la fiche contact
                    </a>
                @endif

                <form method="post" action="{{ route('eleves.destroy', $eleve) }}" onsubmit="return confirm('Masquer cet élève ?')">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="side-action is-danger" onclick="this.form.requestSubmit()">
                        @include('partials.icon', ['n' => 'eye-off', 's' => 15, 'c' => '#b91c1c'])Masquer l'élève
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>

<style>
    /* Fiche élève : styles spécifiques (le reste vient de layouts/app.blade.php) */
    .id-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px 20px 0; margin-bottom: 14px; }
    .id-row { display: flex; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
    .id-avatar {
        width: 62px; height: 62px; border-radius: 16px; flex-shrink: 0;
        background: linear-gradient(140deg, #6366f1, var(--brand-dark));
        color: #fff; font-size: 21px; font-weight: 700; letter-spacing: -.02em;
        display: flex; align-items: center; justify-content: center;
    }
    .id-main { min-width: 0; flex: 1 1 280px; }
    .id-title { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
    .id-title h1 { margin: 0; font-size: 23px; letter-spacing: -.025em; }
    .id-sub { margin: 7px 0 0; font-size: 13.5px; color: #585e72; }
    .id-contacts { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 11px; }
    .id-contacts a, .id-meta { display: inline-flex; align-items: center; gap: 7px; font-size: 13px; font-weight: 500; color: #585e72; }
    .id-contacts a:hover { color: var(--brand); }
    .id-meta { color: var(--muted); font-weight: 400; }
    .id-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto; }

    .pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 700; }
    .pill .dot, .dot-status .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .tabs-bar { display: flex; gap: 2px; margin-top: 18px; overflow-x: auto; }
    .tab {
        display: inline-flex; align-items: center; gap: 7px; padding: 10px 15px;
        border: 0; border-bottom: 2px solid transparent; background: none; cursor: pointer;
        font: inherit; font-size: 13.5px; font-weight: 600; color: var(--muted); white-space: nowrap;
        transition: color .14s ease, border-color .14s ease;
    }
    .tab:hover { color: var(--ink); background: none; }
    .tab.is-active { color: var(--brand-deep); border-bottom-color: var(--brand); }
    .tab-badge { padding: 1px 7px; border-radius: 999px; font-size: 11px; font-weight: 700; background: #f0f1f6; color: var(--muted); }
    .tab.is-active .tab-badge { background: var(--brand-light); color: var(--brand-deep); }

    .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 14px; align-items: start; }
    .detail-main, .detail-side { display: flex; flex-direction: column; gap: 14px; min-width: 0; }
    .detail-main > [data-panel] { display: flex; flex-direction: column; gap: 14px; }
    /* `display` en règle d'auteur l'emporte sur l'attribut [hidden] : sans
       cette ligne, tous les panneaux s'affichent en même temps et les
       onglets semblent inertes. */
    .detail-main > [data-panel][hidden] { display: none; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-pad { padding: 16px; }
    .panel-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 14px 16px 12px; border-bottom: 1px solid var(--border-soft); }
    .panel-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head .panel-sub { font-size: 12.5px; color: var(--muted); }
    .panel-head a { margin-left: auto; font-size: 12.5px; font-weight: 600; }

    .field-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 1px; background: var(--border-soft); }
    .field { background: var(--surface); padding: 12px 16px; }
    .field-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .field-value { font-size: 13.5px; font-weight: 600; margin-top: 3px; min-width: 0; overflow-wrap: anywhere; }
    .field-value.is-regular { font-weight: 500; }

    .table-scroll { overflow-x: auto; }
    .data-table { margin: 0; border: 0; border-radius: 0; box-shadow: none; }
    .data-table th.num, .data-table td.num { text-align: right; }
    .data-table td { vertical-align: middle; font-size: 13px; }
    .data-table td.num { font-variant-numeric: tabular-nums; }
    .data-table td.strong { font-weight: 600; }
    .data-table td.muted { color: var(--muted); }
    .data-table td.note { font-weight: 700; }
    .dot-status { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; }

    .doc-list { display: flex; flex-direction: column; }
    .doc { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid #f6f7fa; color: inherit; transition: background .14s ease; }
    .doc:hover { background: #fafbff; color: inherit; }
    .doc-icon { width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0; background: #f5f6fa; display: flex; align-items: center; justify-content: center; }
    .doc-text { min-width: 0; }
    .doc-name { display: block; font-size: 13.5px; font-weight: 600; }
    .doc-meta { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }

    .money { display: flex; align-items: baseline; gap: 8px; margin-top: 8px; }
    .money-value { font-size: 26px; font-weight: 700; letter-spacing: -.025em; font-variant-numeric: tabular-nums; }
    .money-hint { font-size: 12.5px; color: var(--muted); }
    .bar { height: 7px; border-radius: 999px; background: #f0f1f6; overflow: hidden; margin-top: 13px; }
    .bar span { display: block; height: 100%; border-radius: 999px; background: var(--brand); }
    .bar-legend { display: flex; justify-content: space-between; gap: 10px; margin-top: 7px; font-size: 12px; color: var(--muted); }
    .bar-legend .is-ok { color: #0f766e; font-weight: 600; }
    .money-foot { display: flex; align-items: center; gap: 9px; margin-top: 13px; padding-top: 12px; border-top: 1px solid var(--border-soft); }
    .money-foot a { margin-left: auto; font-size: 12.5px; font-weight: 600; }

    .timeline { display: flex; flex-direction: column; }
    .step { display: flex; gap: 11px; padding: 11px 16px; border-bottom: 1px solid #f6f7fa; }
    .step-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; background: var(--brand); }
    .step-title { display: block; font-size: 13px; font-weight: 600; }
    .step-date { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }

    .side-title { margin: 0 0 11px; font-size: 13.5px; font-weight: 700; }
    .side-actions { display: flex; flex-direction: column; gap: 7px; }
    .side-actions form { max-width: none; margin: 0; }
    .side-action {
        display: flex; align-items: center; gap: 9px; width: 100%; padding: 9px 11px;
        border-radius: 9px; border: 1px solid var(--border); background: #fff;
        color: #585e72; font: inherit; font-size: 13px; font-weight: 600; cursor: pointer;
        transition: border-color .14s ease, background .14s ease, color .14s ease;
    }
    .side-action:hover { border-color: #c3c6f5; background: #fafbff; color: var(--brand-deep); }
    .side-action svg { stroke: currentColor; flex-shrink: 0; }
    .side-action.is-danger { color: var(--danger-dark); border-color: #fecaca; }
    .side-action.is-danger:hover { background: var(--danger-bg); color: var(--danger-dark); border-color: #fecaca; }
    .side-action.is-danger svg { stroke: var(--danger-dark); }

    /* Onglet Famille : parents / tuteurs */
    .tut { border-bottom: 1px solid #f6f7fa; }
    .tut-row { display: flex; align-items: center; gap: 12px; padding: 13px 16px 4px; flex-wrap: wrap; }
    .tut-avatar {
        width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0; background: var(--brand-light);
        color: var(--brand-deep); font-size: 11.5px; font-weight: 700; display: flex; align-items: center; justify-content: center;
    }
    .tut-text { min-width: 0; flex: 1 1 180px; }
    .tut-name { display: block; font-size: 13.5px; font-weight: 600; }
    .tut-sub { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
    .tut-contacts { display: flex; gap: 16px; flex-wrap: wrap; padding: 0 16px 13px 62px; }
    .tut-contacts a, .tut-meta { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 500; color: #585e72; }
    .tut-contacts a:hover { color: var(--brand); }
    .tut-meta { color: var(--muted); font-weight: 400; }
    .tut-body { padding: 0 16px 15px 62px; }
    .tut-form { max-width: none; background: #fbfbff; border: 1px solid #eef0f5; border-radius: 12px; padding: 14px; margin: 0; }
    .tut-grid { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px; }
    .tut-grid .stack { flex: 1 1 150px; margin: 0; }
    .tut-form .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .tut-form input, .tut-form select { width: 100%; max-width: none; border-radius: 9px; padding: 9px 11px; font-size: 13px; background: #fff; }
    .tut-form .check { display: inline-flex; align-items: center; gap: 7px; margin: 4px 14px 0 0; font-size: 12.5px; font-weight: 500; }
    .tut-form .check input { width: 15px; height: 15px; margin: 0; }
    .tut-actions { display: flex; gap: 8px; margin-top: 12px; }
    .tut-foot { padding: 14px 16px; background: #fbfbff; border-top: 1px solid var(--border-soft); }
    .tut-foot summary { font-size: 13px; font-weight: 600; color: var(--brand-deep); cursor: pointer; }
    .tut-results { position: relative; margin-top: 4px; }
    .tut-results button {
        display: block; width: 100%; text-align: left; padding: 9px 11px; border: 1px solid var(--border);
        border-radius: 9px; background: #fff; cursor: pointer; font: inherit; font-size: 12.5px; margin-top: 4px;
    }
    .tut-results button:hover { border-color: #c3c6f5; background: #fafbff; }
    .tut-results .tut-result-detail { display: block; font-size: 11px; color: var(--muted); margin-top: 1px; }
</style>

<script>
    // Onglets de la fiche : bascule locale, sans rechargement.
    (function () {
        var tabs = Array.prototype.slice.call(document.querySelectorAll('.tab[data-tab]'));
        var panels = Array.prototype.slice.call(document.querySelectorAll('[data-panel]'));
        if (!tabs.length) return;

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.toggle('is-active', t === tab); });
                panels.forEach(function (p) { p.hidden = p.dataset.panel !== tab.dataset.tab; });
            });
        });

        // Onglet Famille : dépliage du formulaire de chaque parent
        document.querySelectorAll('[data-tut-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var body = btn.closest('.tut').querySelector('.tut-body');
                body.hidden = !body.hidden;
            });
        });

        // Recherche d'un parent déjà enregistré (fratrie)
        var rech = document.getElementById('tut-recherche');
        var results = document.getElementById('tut-results');
        var hidden = document.getElementById('tut-id');

        if (rech && results && hidden) {
            var timer = null;

            rech.addEventListener('input', function () {
                hidden.value = '';
                clearTimeout(timer);
                var q = rech.value.trim();

                if (q.length < 2) {
                    results.hidden = true;
                    results.innerHTML = '';
                    return;
                }

                timer = setTimeout(function () {
                    fetch('{{ route('tuteurs.recherche') }}?q=' + encodeURIComponent(q))
                        .then(function (r) { return r.json(); })
                        .then(function (liste) {
                            results.innerHTML = '';
                            if (!liste.length) {
                                results.hidden = true;
                                return;
                            }
                            liste.forEach(function (t) {
                                var b = document.createElement('button');
                                b.type = 'button';
                                b.textContent = t.libelle;
                                var d = document.createElement('span');
                                d.className = 'tut-result-detail';
                                d.textContent = t.detail;
                                b.appendChild(d);
                                b.addEventListener('click', function () {
                                    hidden.value = t.id_tuteur;
                                    rech.value = t.libelle;
                                    results.hidden = true;
                                });
                                results.appendChild(b);
                            });
                            results.hidden = false;
                        });
                }, 250);
            });

            // Empêche l'envoi si aucun parent n'a été choisi dans la liste.
            document.getElementById('form-attacher').addEventListener('submit', function (e) {
                if (!hidden.value) {
                    e.preventDefault();
                    alert('Choisissez un parent dans la liste des résultats.');
                }
            });
        }
    })();
</script>
@endsection
