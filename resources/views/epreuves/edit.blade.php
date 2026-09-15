@extends('layouts.app')

@section('title', "Modifier l'épreuve")

@section('content')
@php
    $decisions = [
        'en_attente' => ['En attente', '#eef1f6', '#475569', '#9aa0b0'],
        'accepte' => ['Admis', '#e7f6f2', '#0f766e', '#0f766e'],
        'accepter_niveau_inferieur' => ['Admis (niveau inférieur)', '#eef0fe', '#3730a3', '#4f46e5'],
        'accepter_avec_entreprise' => ['Admis (avec entreprise)', '#f4ecfd', '#6d28d9', '#7c3aed'],
        'refuse' => ['Refusé', '#fdecef', '#be123c', '#be123c'],
    ];

    $champsNotes = [
        'anglais' => 'Anglais',
        'culture_generale' => 'Culture G.',
        'epreuve_redaction' => 'Rédaction',
        'entretien' => 'Entretien',
    ];

    $inscriptions = $epreuve->inscriptions;
    $presents = $inscriptions->where('presence', true)->count();
    $inscrits = $inscriptions->count();
    $effectif = (int) $epreuve->effectif;
    $taux = $effectif > 0 ? min(100, (int) round($inscrits / $effectif * 100)) : 0;
    $restant = max(0, $effectif - $inscrits);

    $formationsSelectionnees = collect(old('id_formation', $epreuve->formations->pluck('id_formation')->all()));

    $repartition = collect($decisions)->map(function ($info, $cle) use ($resultatsParEleve) {
        return ['libelle' => $info[0], 'dot' => $info[3], 'nombre' => $resultatsParEleve->where('decision', $cle)->count()];
    });
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <a href="{{ route('epreuves.index') }}">Épreuves d'admission</a>
    <span class="sep">/</span>
    <span class="current">Session du {{ $epreuve->date_epreuve->format('d/m/Y') }}</span>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

{{-- ---------- En-tête de la session ---------- --}}
<div class="ep-card">
    <div class="ep-row">
        <span class="ep-date">
            <span class="ep-month">{{ ucfirst($epreuve->date_epreuve->locale('fr_FR')->isoFormat('MMM')) }}</span>
            <span class="ep-day">{{ $epreuve->date_epreuve->format('d') }}</span>
        </span>

        <div class="ep-main">
            <div class="ep-title">
                <h1>Épreuve du {{ $epreuve->date_epreuve->format('d/m/Y') }} — {{ $epreuve->date_epreuve->format('H:i') }}</h1>
                <span class="pill dot-pill" style="background:{{ $epreuve->distanciel ? '#eef0fe' : '#e7f6f2' }};color:{{ $epreuve->distanciel ? '#3730a3' : '#0f766e' }}">
                    <span class="dot"></span>{{ $epreuve->distanciel ? 'Distanciel' : 'Présentiel' }}
                </span>
            </div>
            <p class="ep-sub">
                {{ $epreuve->lieu }}
                @if ($epreuve->formations->isNotEmpty()) · {{ $epreuve->formations->pluck('niveau')->join(', ') }} @endif
            </p>
        </div>

        <div class="ep-actions">
            <button type="button" class="btn btn-ghost" onclick="window.print()">
                @include('partials.icon', ['n' => 'printer', 's' => 15, 'w' => 2])Liste d'émargement
            </button>
            <button type="submit" form="epreuve-form" class="btn">
                @include('partials.icon', ['n' => 'check-simple', 's' => 15, 'w' => 2])Enregistrer
            </button>
        </div>
    </div>

    <div class="tabs-bar" role="tablist">
        <button type="button" class="tab is-active" data-tab="candidats" role="tab">
            Candidats<span class="tab-badge">{{ $inscrits }}</span>
        </button>
        <button type="button" class="tab" data-tab="parametres" role="tab">
            Paramètres<span class="tab-badge">{{ $formationsSelectionnees->count() }}</span>
        </button>
    </div>
</div>

<div class="ep-grid">
    <div class="ep-col">

        {{-- ---------- Onglet candidats ---------- --}}
        <div data-panel="candidats">
            <section class="panel">
                <div class="panel-head">
                    <h2>Candidats inscrits</h2>
                    <span class="panel-sub">{{ $presents }} / {{ $inscrits }} {{ $presents > 1 ? 'présents' : 'présent' }}</span>
                </div>

                @forelse ($inscriptions as $inscription)
                    @php
                        $resultat = $resultatsParEleve->get($inscription->id_eleve);
                        $cle = $resultat->decision ?? 'en_attente';
                        [$libelleDecision, $bg, $ink] = $decisions[$cle] ?? $decisions['en_attente'];
                        $nomCandidat = $inscription->eleve?->contact?->nom_complet ?? 'Candidat #'.$inscription->id_eleve;
                        $initiales = mb_strtoupper(mb_substr($inscription->eleve?->contact?->prenom ?? '?', 0, 1).mb_substr($inscription->eleve?->contact?->nom ?? '', 0, 1));
                        $notes = collect($champsNotes)->keys()->map(fn ($c) => $resultat->{$c} ?? null);
                        $moyenne = $notes->filter(fn ($n) => $n !== null)->count() === $notes->count()
                            ? $notes->sum() / $notes->count()
                            : null;
                    @endphp

                    <div class="cand {{ $resultat ? 'is-open' : '' }}">
                        <div class="cand-row">
                            <span class="cand-avatar">{{ $initiales }}</span>
                            <span class="cand-text">
                                @if ($inscription->eleve)
                                    <a href="{{ route('eleves.show', $inscription->eleve) }}" class="cand-name">{{ $nomCandidat }}</a>
                                @else
                                    <span class="cand-name">{{ $nomCandidat }}</span>
                                @endif
                                <span class="cand-sub">{{ $inscription->eleve?->niveau?->nom_niveau ?? '—' }}</span>
                            </span>

                            <form method="post" action="{{ route('epreuves.toggle-presence', $inscription) }}" class="inline-form">
                                @csrf
                                <label class="presence {{ $inscription->presence ? 'is-on' : '' }}">
                                    <input type="checkbox" onchange="this.form.requestSubmit()" @checked($inscription->presence)>
                                    {{ $inscription->presence ? 'Présent' : 'Absent' }}
                                </label>
                            </form>

                            <span class="pill" style="background:{{ $bg }};color:{{ $ink }}">
                                @if ($moyenne !== null)
                                    <span class="num">{{ number_format($moyenne, 2, ',', ' ') }}</span>
                                    <span class="pill-sep">·</span>
                                @endif
                                {{ $libelleDecision }}
                            </span>

                            <button type="button" class="caret" data-cand-toggle aria-label="Saisir les notes">
                                @include('partials.icon', ['n' => 'chevron-down', 's' => 15, 'c' => '#585e72', 'w' => 2.2])
                            </button>
                        </div>

                        <div class="cand-body" @unless($resultat) hidden @endunless>
                            <form method="post" action="{{ route('epreuves.resultats.store', $epreuve) }}" class="notes-form">
                                @csrf
                                <input type="hidden" name="id_eleve" value="{{ $inscription->id_eleve }}">

                                <div class="notes-row">
                                    @foreach ($champsNotes as $champ => $libelle)
                                        @php $valeur = $resultat->{$champ} ?? null; @endphp
                                        <label class="note-field">
                                            <span>{{ $libelle }}</span>
                                            <input type="number" name="{{ $champ }}" min="0" max="20" step="0.5"
                                                   value="{{ $valeur }}" data-note required
                                                   class="{{ $valeur !== null && $valeur < 10 ? 'is-low' : '' }}">
                                        </label>
                                    @endforeach
                                    <div class="note-field">
                                        <span>Moyenne</span>
                                        <div class="avg {{ $moyenne === null ? 'is-empty' : ($moyenne < 10 ? 'is-low' : '') }}" data-avg>
                                            {{ $moyenne !== null ? number_format($moyenne, 2, ',', ' ') : '—' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="notes-foot">
                                    <label class="note-select">
                                        <span>Décision</span>
                                        <select name="decision" data-decision required>
                                            @foreach ($decisions as $valeur => [$libelle])
                                                <option value="{{ $valeur }}" @selected($cle === $valeur)>{{ $libelle }}</option>
                                            @endforeach
                                        </select>
                                    </label>

                                    <label class="note-select" data-motif @unless($cle === 'refuse') hidden @endunless>
                                        <span>Motif de refus</span>
                                        <select name="id_motif_refus">
                                            <option value="">—</option>
                                            @foreach ($motifsRefus as $motif)
                                                <option value="{{ $motif->id_motif_refus }}" @selected(($resultat->id_motif_refus ?? null) == $motif->id_motif_refus)>{{ $motif->libelle }}</option>
                                            @endforeach
                                        </select>
                                    </label>

                                    <div class="notes-actions">
                                        <button type="submit" class="btn">Enregistrer</button>
                                    </div>
                                </div>
                            </form>

                            <div class="cand-danger">
                                @if ($resultat)
                                    <form method="post" action="{{ route('epreuves.resultats.destroy', $resultat) }}" class="inline-form"
                                          onsubmit="return confirm('Supprimer ce résultat ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Supprimer le résultat</button>
                                    </form>
                                @endif
                                <form method="post" action="{{ route('epreuves.suppression-candidat', [$epreuve, $inscription->id_eleve]) }}" class="inline-form"
                                      onsubmit="return confirm('Retirer ce candidat de l\'épreuve ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Retirer le candidat</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-cell">
                        @include('partials.icon', ['n' => 'users', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                        <span>Aucun candidat inscrit à cette épreuve.</span>
                    </div>
                @endforelse

                {{-- Inscription d'un candidat --}}
                <div class="add-foot">
                    @if ($candidatsDisponibles->isEmpty())
                        <p class="add-empty">Tous les candidats sont déjà inscrits, ou aucun candidat n'existe pour l'instant.</p>
                    @else
                        <form method="post" action="{{ route('epreuves.inscrire', $epreuve) }}" class="add-form">
                            @csrf
                            <label class="note-select" style="flex:1 1 220px">
                                <span>Inscrire un candidat</span>
                                <select name="id_eleve" required>
                                    @foreach ($candidatsDisponibles as $candidat)
                                        <option value="{{ $candidat->id_eleve }}">{{ $candidat->contact->nom_complet }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <button type="submit" class="btn">
                                @include('partials.icon', ['n' => 'plus', 's' => 14, 'w' => 2.4])Inscrire
                            </button>
                        </form>
                    @endif
                </div>
            </section>
        </div>

        {{-- ---------- Onglet paramètres ---------- --}}
        <div data-panel="parametres" hidden>
            <section class="panel panel-pad">
                <h2 class="panel-title">Paramètres de la session</h2>

                <form method="post" action="{{ route('epreuves.update', $epreuve) }}" id="epreuve-form" class="ep-form">
                    @csrf
                    @method('PUT')

                    <div class="quad">
                        <label class="stack">
                            <span>Date</span>
                            <input type="date" name="date" value="{{ old('date', $epreuve->date_epreuve->format('Y-m-d')) }}" required>
                        </label>
                        <label class="stack">
                            <span>Heure</span>
                            <input type="time" name="heure" value="{{ old('heure', $epreuve->date_epreuve->format('H:i')) }}" required>
                        </label>
                        <label class="stack">
                            <span>Lieu</span>
                            <input type="text" name="lieu" maxlength="16" value="{{ old('lieu', $epreuve->lieu) }}" required>
                        </label>
                        <label class="stack">
                            <span>Effectif max</span>
                            <input type="number" name="effectif" value="{{ old('effectif', $epreuve->effectif) }}" required>
                        </label>
                    </div>

                    <div class="divider">
                        <label class="choice {{ old('distanciel', $epreuve->distanciel) ? 'is-on' : '' }}" data-distanciel-toggle>
                            <input type="checkbox" name="distanciel" value="1" @checked(old('distanciel', $epreuve->distanciel))>
                            <span>
                                <span class="choice-label">Épreuve à distance</span>
                                <span class="choice-hint">Les candidats reçoivent un lien de connexion au lieu d'une convocation sur site.</span>
                            </span>
                        </label>

                        <label class="stack" data-distanciel-url @unless(old('distanciel', $epreuve->distanciel)) hidden @endunless>
                            <span>URL de connexion</span>
                            <input type="url" name="url_distanciel" value="{{ old('url_distanciel', $epreuve->url_distanciel) }}"
                                   placeholder="https://meet.example.com/admission">
                        </label>
                    </div>

                    <div class="divider">
                        <span class="stack-label">Formations concernées</span>
                        <div class="chips-row">
                            @foreach ($formations as $formation)
                                @php $on = $formationsSelectionnees->contains($formation->id_formation); @endphp
                                <label class="form-chip {{ $on ? 'is-on' : '' }}">
                                    <input type="checkbox" name="id_formation[]" value="{{ $formation->id_formation }}" @checked($on)>
                                    {{ $formation->niveau }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>

    {{-- ---------- Colonne latérale ---------- --}}
    <div class="ep-col">
        <section class="panel panel-pad">
            <div class="field-label">Remplissage</div>
            <div class="fill-total">
                <span class="fill-value">{{ $inscrits }}</span>
                <span class="fill-hint">/ {{ $effectif }} places</span>
            </div>
            <div class="bar"><span style="width:{{ $taux }}%;{{ $taux >= 100 ? 'background:#b45309' : '' }}"></span></div>
            <div class="bar-legend">
                <span class="is-strong">{{ $taux }}% rempli</span>
                <span>{{ $restant > 0 ? $restant.' place(s) restante(s)' : 'complet' }}</span>
            </div>
        </section>

        <section class="panel">
            <div class="panel-head"><h2>Décisions</h2></div>
            <div class="rep-list">
                @foreach ($repartition as $ligne)
                    <div class="rep-row">
                        <span class="rep-dot" style="background:{{ $ligne['dot'] }}"></span>
                        <span class="rep-label">{{ $ligne['libelle'] }}</span>
                        <span class="rep-count">{{ $ligne['nombre'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="panel panel-pad">
            <h2 class="side-title">Actions</h2>
            <div class="side-actions">
                <button type="button" class="side-action" onclick="window.print()">
                    @include('partials.icon', ['n' => 'printer', 's' => 15])Imprimer la liste
                </button>
                <a href="{{ route('epreuves.create') }}" class="side-action">
                    @include('partials.icon', ['n' => 'plus', 's' => 15, 'w' => 2.2])Nouvelle session
                </a>

                <form method="post" action="{{ route('epreuves.destroy', $epreuve) }}"
                      onsubmit="return confirm('Supprimer cette épreuve ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="side-action is-danger">
                        @include('partials.icon', ['n' => 'trash', 's' => 15, 'c' => '#b91c1c'])Supprimer l'épreuve
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>

<style>
    /* Épreuve d'admission : styles spécifiques (le reste vient de layouts/app.blade.php) */
    .ep-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px 20px 0; margin-bottom: 14px; }
    .ep-row { display: flex; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
    .ep-date {
        width: 58px; height: 58px; border-radius: 14px; flex-shrink: 0; background: var(--brand-light);
        display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1;
    }
    .ep-month { font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .ep-day { font-size: 22px; font-weight: 700; letter-spacing: -.02em; color: var(--brand-deep); margin-top: 2px; }
    .ep-main { min-width: 0; flex: 1 1 280px; }
    .ep-title { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
    .ep-title h1 { margin: 0; font-size: 22px; letter-spacing: -.025em; }
    .ep-sub { margin: 7px 0 0; font-size: 13.5px; color: #585e72; }
    .ep-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto; }

    .pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
    .pill .dot, .rep-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .pill .num { font-variant-numeric: tabular-nums; }
    .pill-sep { opacity: .5; }

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

    .ep-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 14px; align-items: start; }
    .ep-col { display: flex; flex-direction: column; gap: 14px; min-width: 0; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-pad { padding: 18px; }
    .panel-title { margin: 0 0 15px; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 13px 16px; border-bottom: 1px solid var(--border-soft); }
    .panel-head h2 { margin: 0; font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .panel-head .panel-sub { font-size: 12.5px; color: var(--muted); }

    /* Candidats */
    .cand { border-bottom: 1px solid #f6f7fa; transition: background .14s ease; }
    .cand.is-open { background: #fafbff; }
    .cand-row { display: flex; align-items: center; gap: 12px; padding: 13px 16px; flex-wrap: wrap; }
    .cand-avatar {
        width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0; background: var(--brand-light);
        color: var(--brand-deep); font-size: 11.5px; font-weight: 700; display: flex; align-items: center; justify-content: center;
    }
    .cand-text { min-width: 0; flex: 1 1 180px; }
    .cand-name { display: block; font-size: 13.5px; font-weight: 600; color: var(--ink); }
    a.cand-name:hover { color: var(--brand); }
    .cand-sub { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
    .inline-form { display: inline-block; margin: 0; max-width: none; }

    .presence {
        display: inline-flex; align-items: center; gap: 7px; margin: 0; padding: 6px 11px;
        border: 1px solid var(--border); border-radius: 999px; background: #fff;
        color: var(--muted); font-size: 12.5px; font-weight: 600; cursor: pointer; flex-shrink: 0;
        transition: border-color .14s ease, background .14s ease, color .14s ease;
    }
    .presence.is-on { border-color: #c3e6da; background: #e7f6f2; color: #0f766e; }
    .presence input { width: 14px; height: 14px; margin: 0; flex-shrink: 0; }

    .caret {
        width: 30px; height: 30px; border-radius: 8px; padding: 0; flex-shrink: 0;
        border: 1px solid var(--border); background: #fff; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .caret:hover { background: #f4f5fa; }
    .caret svg { transition: transform .16s cubic-bezier(.22,1,.36,1); transform: rotate(-90deg); }
    .cand.is-open .caret svg { transform: none; }

    .cand-body { padding: 0 16px 15px; }
    .notes-form { max-width: none; background: #fbfbff; border: 1px solid #eef0f5; border-radius: 12px; padding: 14px; }
    .notes-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
    .note-field { flex: 0 1 92px; min-width: 82px; margin: 0; }
    .note-field > span { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .note-field input {
        width: 100%; max-width: none; text-align: right; font-variant-numeric: tabular-nums;
        border-radius: 9px; padding: 9px 11px; font-size: 13px;
    }
    .note-field input.is-low { color: var(--danger-dark); }
    .avg {
        border: 1px solid var(--border); background: #fff; border-radius: 9px; padding: 9px 11px;
        font-size: 14px; font-weight: 700; font-variant-numeric: tabular-nums; text-align: right;
    }
    .avg.is-empty { border-color: var(--border-soft); background: #fafbfd; color: var(--faint); }
    .avg.is-low { color: var(--danger-dark); }

    .notes-foot { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; margin-top: 12px; padding-top: 12px; border-top: 1px solid #eef0f5; }
    .note-select { flex: 1 1 220px; min-width: 180px; margin: 0; }
    .note-select > span { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .note-select select { width: 100%; max-width: none; border-radius: 9px; padding: 9px 11px; font-size: 13px; cursor: pointer; }
    .notes-actions { display: flex; gap: 8px; margin-left: auto; }
    .notes-actions .btn { white-space: nowrap; }

    .cand-danger { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
    .cand-danger .btn-danger { padding: 7px 12px; font-size: 12.5px; }

    .add-foot { padding: 14px 16px; background: #fbfbff; border-top: 1px solid var(--border-soft); }
    .add-form { display: flex; gap: 9px; align-items: flex-end; flex-wrap: wrap; max-width: none; margin: 0; }
    .add-form .btn { white-space: nowrap; }
    .add-empty { margin: 0; font-size: 13px; color: var(--muted); }
    .empty-cell { text-align: center; padding: 42px 16px; }
    .empty-cell span { display: block; margin-top: 10px; font-size: 13px; color: var(--muted); }

    /* Paramètres */
    .ep-form { max-width: none; }
    .quad { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }
    .stack { display: block; margin: 0; }
    .stack > span:first-child, .stack-label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
    .stack input { width: 100%; max-width: none; background: #fafbfd; }
    .divider { margin-top: 16px; padding-top: 15px; border-top: 1px solid var(--border-soft); }
    .divider .stack { margin-top: 12px; }

    .choice {
        display: flex; align-items: flex-start; gap: 10px; margin: 0; padding: 12px 13px;
        border: 1px solid var(--border); border-radius: 11px; background: #fff; cursor: pointer; font-weight: 400;
        transition: border-color .14s ease, background .14s ease;
    }
    .choice:hover, .choice.is-on { border-color: #c3c6f5; background: #fafbff; }
    .choice input { width: 15px; height: 15px; margin: 2px 0 0; flex-shrink: 0; }
    .choice-label { display: block; font-size: 13.5px; font-weight: 600; }
    .choice-hint { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; }

    .chips-row { display: flex; gap: 8px; flex-wrap: wrap; }
    .form-chip {
        display: inline-flex; align-items: center; gap: 7px; margin: 0; padding: 8px 13px;
        border: 1px solid var(--border); border-radius: 999px; background: #fff;
        color: #585e72; font-size: 12.5px; font-weight: 600; cursor: pointer;
        transition: border-color .14s ease, background .14s ease, color .14s ease;
    }
    .form-chip:hover { border-color: #c3c6f5; }
    .form-chip.is-on { border-color: #c3c6f5; background: var(--brand-light); color: var(--brand-deep); }
    .form-chip input { width: 14px; height: 14px; margin: 0; flex-shrink: 0; }

    /* Colonne latérale */
    .field-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .fill-total { display: flex; align-items: baseline; gap: 8px; margin-top: 7px; }
    .fill-value { font-size: 28px; font-weight: 700; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
    .fill-hint { font-size: 13px; color: var(--muted); }
    .bar { height: 7px; border-radius: 999px; background: #f0f1f6; overflow: hidden; margin-top: 13px; }
    .bar span { display: block; height: 100%; border-radius: 999px; background: var(--brand); transition: width .3s cubic-bezier(.22,1,.36,1); }
    .bar-legend { display: flex; justify-content: space-between; gap: 10px; margin-top: 7px; font-size: 12px; color: var(--muted); }
    .bar-legend .is-strong { color: #585e72; font-weight: 600; }

    .rep-list { display: flex; flex-direction: column; }
    .rep-row { display: flex; align-items: center; gap: 10px; padding: 11px 16px; border-bottom: 1px solid #f6f7fa; }
    .rep-dot { width: 8px; height: 8px; flex-shrink: 0; }
    .rep-label { font-size: 13px; color: #585e72; min-width: 0; }
    .rep-count { margin-left: auto; font-size: 13px; font-weight: 700; font-variant-numeric: tabular-nums; }

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

    @media print {
        .ep-actions, .tabs-bar, .ep-col:last-child, .add-foot, .cand-danger, .notes-foot .notes-actions, .caret { display: none !important; }
        .ep-grid { display: block !important; }
        .cand-body { display: none !important; }
    }
</style>

<script>
    (function () {
        // Onglets
        var tabs = Array.prototype.slice.call(document.querySelectorAll('.tab[data-tab]'));
        var panels = Array.prototype.slice.call(document.querySelectorAll('[data-panel]'));
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.toggle('is-active', t === tab); });
                panels.forEach(function (p) { p.hidden = p.dataset.panel !== tab.dataset.tab; });
            });
        });

        // Tiroir de saisie des notes
        document.querySelectorAll('[data-cand-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var cand = btn.closest('.cand');
                var body = cand.querySelector('.cand-body');
                body.hidden = !body.hidden;
                cand.classList.toggle('is-open', !body.hidden);
            });
        });

        // Moyenne calculée en direct + motif de refus conditionnel
        document.querySelectorAll('.notes-form').forEach(function (form) {
            var notes = Array.prototype.slice.call(form.querySelectorAll('[data-note]'));
            var avg = form.querySelector('[data-avg]');
            var decision = form.querySelector('[data-decision]');
            var motif = form.querySelector('[data-motif]');

            function refresh() {
                var valeurs = notes.map(function (n) { return n.value === '' ? null : parseFloat(n.value); });
                var completes = valeurs.every(function (v) { return v !== null && !isNaN(v); });
                notes.forEach(function (n) {
                    n.classList.toggle('is-low', n.value !== '' && parseFloat(n.value) < 10);
                });
                if (!avg) return;
                if (!completes) {
                    avg.textContent = '—';
                    avg.className = 'avg is-empty';
                    return;
                }
                var moyenne = valeurs.reduce(function (a, b) { return a + b; }, 0) / valeurs.length;
                avg.textContent = moyenne.toFixed(2).replace('.', ',');
                avg.className = 'avg' + (moyenne < 10 ? ' is-low' : '');
            }

            notes.forEach(function (n) { n.addEventListener('input', refresh); });
            if (decision && motif) {
                decision.addEventListener('change', function () {
                    motif.hidden = decision.value !== 'refuse';
                });
            }
            refresh();
        });
    })();
</script>
@endsection
