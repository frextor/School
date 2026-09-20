@extends('layouts.app')

@section('title', 'Emploi du temps')

@section('content')
@php
    // Navigation et bascule de vue conservent les filtres en cours.
    $avec = fn (array $params) => route('planning.index', array_filter(
        array_merge(request()->query(), $params),
        fn ($v) => $v !== null && $v !== ''
    ));
    $urlSemaine = fn ($lundi) => $avec(['semaine' => $lundi->format('Y-m-d'), 'vue' => 'calendrier']);
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Emploi du temps</span>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

<div class="page-head">
    <div>
        <h1>Emploi du temps</h1>
        <p class="page-sub">La semaine de cours, classe par classe. Cliquez sur un créneau pour le modifier.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('planning.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouveau créneau
        </a>
    </div>
</div>

<form method="get" action="{{ route('planning.index') }}" class="filters filter-card">
    <input type="hidden" name="vue" value="{{ $vue }}">
    <input type="hidden" name="semaine" value="{{ $debutSemaine->format('Y-m-d') }}">

    <div class="pl-filtres">
        <label class="stack">
            <span>Établissement</span>
            <select name="campus">
                <option value="">Tous les établissements</option>
                @foreach ($etablissements as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected($filtres['campus'] === $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
        </label>

        <label class="stack">
            <span>Classe</span>
            <select name="classe">
                <option value="">Toutes les classes</option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe->id_classe }}" @selected($filtres['classe'] === (int) $classe->id_classe)>{{ $classe->classe }}</option>
                @endforeach
            </select>
        </label>

        <label class="stack">
            <span>Enseignant</span>
            <select name="intervenant">
                <option value="">Tous les enseignants</option>
                @foreach ($intervenants as $intervenant)
                    <option value="{{ $intervenant->id_intervenant }}" @selected($filtres['intervenant'] === $intervenant->id_intervenant)>{{ $intervenant->nom }} {{ $intervenant->prenom }}</option>
                @endforeach
            </select>
        </label>

        <div class="pl-filtres-actions">
            <button type="submit" class="btn">Filtrer</button>
            @if ($filtres['campus'] || $filtres['classe'] || $filtres['intervenant'])
                {{-- Le bouton ne conserve que la semaine consultée : remettre
                     les filtres à zéro ne doit pas renvoyer à aujourd'hui. --}}
                <a href="{{ route('planning.index', ['vue' => $vue, 'semaine' => $debutSemaine->format('Y-m-d')]) }}" class="filter-reset">Réinitialiser</a>
            @endif
        </div>

        <span class="pl-vues">
            <a href="{{ $avec(['vue' => 'calendrier']) }}" class="pl-vue @if ($vue === 'calendrier') is-active @endif">
                @include('partials.icon', ['n' => 'cal', 's' => 14, 'w' => 2])Calendrier
            </a>
            <a href="{{ $avec(['vue' => 'liste']) }}" class="pl-vue @if ($vue === 'liste') is-active @endif">
                @include('partials.icon', ['n' => 'list', 's' => 14, 'w' => 2])Liste
            </a>
        </span>
    </div>
</form>

@if ($vue === 'calendrier' && ! $cible)
    {{-- Sans classe ni enseignant, toutes les classes se superposeraient sur
         les mêmes heures : on fait choisir plutôt que d'afficher une bouillie. --}}
    <div class="cal-card pl-choix">
        @include('partials.icon', ['n' => 'cal', 's' => 28, 'c' => '#c3c6d4', 'w' => 1.6])
        <h2>Choisissez une classe ou un enseignant</h2>
        <p>Une grille horaire se lit pour une classe à la fois. Les {{ $classes->count() }} classes affichées ensemble se chevaucheraient sur les mêmes créneaux.</p>

        <div class="pl-choix-liste">
            @foreach ($classes as $classe)
                <a href="{{ $avec(['classe' => $classe->id_classe, 'vue' => 'calendrier']) }}" class="pl-choix-chip">
                    <i style="background: {{ $classe->couleur ?: '#4f46e5' }}"></i>{{ $classe->classe }}
                </a>
            @endforeach
        </div>

        <a href="{{ $avec(['vue' => 'liste']) }}" class="pl-choix-lien">
            Voir tous les créneaux en liste @include('partials.icon', ['n' => 'arrow-right', 's' => 14, 'c' => 'var(--brand)', 'w' => 2])
        </a>
    </div>
@elseif ($vue === 'calendrier')
    @include('partials.calendrier-semaine', [
        'semaine' => $semaine,
        'debutSemaine' => $debutSemaine,
        'urlSemaine' => $urlSemaine,
        'vide' => 'Aucun cours planifié cette semaine avec ces filtres.',
    ])

    @if ($classes->isNotEmpty())
        <div class="pl-legende">
            <span class="pl-legende-label">Classes</span>
            @foreach ($classes->take(12) as $classe)
                <span class="pl-puce"><i style="background: {{ $classe->couleur ?: '#4f46e5' }}"></i>{{ $classe->classe }}</span>
            @endforeach
        </div>
    @endif
@else
    <div class="table-card">
        <div class="table-head">
            <span class="table-count">{{ $creneaux->total() }} créneau{{ $creneaux->total() > 1 ? 'x' : '' }}</span>
        </div>

        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Cours</th>
                        <th>Classe</th>
                        <th>Enseignant</th>
                        <th>Campus</th>
                        <th>Salle</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($creneaux as $creneau)
                        <tr>
                            <td>
                                <span class="cell-name">{{ ucfirst($creneau->date_debut->translatedFormat('D j M Y')) }}</span>
                                <span class="cell-sub">{{ $creneau->date_debut->format('H:i') }} – {{ $creneau->date_fin->format('H:i') }}</span>
                            </td>
                            <td>{{ $creneau->cours?->nom_cours ?? '—' }}</td>
                            <td>
                                <span class="pl-puce"><i style="background: {{ $creneau->classe?->couleur ?: '#cbd0dd' }}"></i>{{ $creneau->classe?->classe ?? '—' }}</span>
                            </td>
                            <td>{{ trim(($creneau->intervenant?->nom ?? '').' '.($creneau->intervenant?->prenom ?? '')) ?: '—' }}</td>
                            <td>{{ $creneau->etablissement?->nom_etablissement ?? '—' }}</td>
                            <td>{{ $creneau->salle?->nom_salle ?: '—' }}</td>
                            <td class="col-actions">
                                <a class="row-btn" href="{{ route('planning.edit', $creneau) }}" title="Modifier">
                                    @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                </a>
                                <form method="post" action="{{ route('planning.destroy', $creneau) }}" class="pl-suppr"
                                      onsubmit="return confirm('Supprimer ce créneau ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="row-btn" title="Supprimer">
                                        @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#585e72', 'w' => 2])
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-cell">
                                @include('partials.icon', ['n' => 'cal', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
                                <span>Aucun créneau avec ces filtres.</span>
                                <a href="{{ route('planning.create') }}">Créer un créneau</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($creneaux->hasPages())
            <div class="table-foot">{{ $creneaux->links() }}</div>
        @endif
    </div>
@endif

{{-- ---------- Fiche d'un créneau (ouverte au clic sur la grille) ---------- --}}
<div class="pl-modal" data-modal hidden>
    <div class="pl-modal-fond" data-fermer></div>

    <div class="pl-modal-boite" role="dialog" aria-modal="true" aria-labelledby="pl-modal-titre">
        <div class="pl-modal-tete">
            <div>
                <span class="pl-modal-eyebrow">Créneau</span>
                <h2 id="pl-modal-titre" data-champ-libelle>—</h2>
                <p class="pl-modal-quand" data-champ-quand>—</p>
            </div>
            <button type="button" class="pl-modal-x" data-fermer aria-label="Fermer">
                @include('partials.icon', ['n' => 'close', 's' => 16, 'c' => '#585e72', 'w' => 2.2])
            </button>
        </div>

        {{-- Lecture : ce que l'on vient vérifier en un coup d'œil. --}}
        <div class="pl-modal-infos">
            <div class="pl-info"><span>Classe</span><strong data-champ-classe-nom>—</strong></div>
            <div class="pl-info"><span>Enseignant</span><strong data-champ-intervenant-nom>—</strong></div>
            <div class="pl-info"><span>Établissement</span><strong data-champ-campus-nom>—</strong></div>
            <div class="pl-info"><span>Salle</span><strong data-champ-salle-nom>—</strong></div>
        </div>

        {{-- Modification : le même formulaire que l'écran plein écran, replié
             ici pour éviter l'aller-retour. --}}
        <form method="post" id="pl-form" class="pl-modal-form" data-form hidden>
            @csrf
            @method('PUT')

            <div class="pl-form-grid">
                <label class="stack">
                    <span>Matière</span>
                    <select name="id_cours" data-saisie-cours required>
                        @foreach ($cours as $matiere)
                            <option value="{{ $matiere->id_cours }}">{{ $matiere->nom_cours }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="stack">
                    <span>Enseignant</span>
                    <select name="id_intervenant" data-saisie-intervenant required>
                        @foreach ($intervenants as $intervenant)
                            <option value="{{ $intervenant->id_intervenant }}">{{ $intervenant->nom }} {{ $intervenant->prenom }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="stack">
                    <span>Établissement</span>
                    <select name="id_etablissement" data-saisie-campus required>
                        @foreach ($etablissements as $etablissement)
                            <option value="{{ $etablissement->id_etablissement }}">{{ $etablissement->nom_etablissement }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="stack">
                    <span>Classe</span>
                    <select name="id_classe" data-saisie-classe>
                        <option value="">— Aucune —</option>
                        @foreach ($classes as $classe)
                            <option value="{{ $classe->id_classe }}">{{ $classe->classe }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="stack">
                    <span>Salle</span>
                    <select name="id_salle" data-saisie-salle>
                        <option value="">— Aucune —</option>
                        @foreach ($salles as $salle)
                            <option value="{{ $salle->id_salle }}">{{ $salle->nom_salle }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="stack">
                    <span>Semestre</span>
                    <select name="semestre" data-saisie-semestre required>
                        <option value="1">Semestre 1</option>
                        <option value="2">Semestre 2</option>
                    </select>
                </label>

                <label class="stack">
                    <span>Début</span>
                    <input type="datetime-local" name="date_debut" data-saisie-debut required>
                </label>

                <label class="stack">
                    <span>Fin</span>
                    <input type="datetime-local" name="date_fin" data-saisie-fin required>
                </label>

                <label class="stack">
                    <span>Année scolaire</span>
                    <input type="number" name="annee" min="2000" max="2100" data-saisie-annee required>
                </label>
            </div>

            <label class="stack pl-form-large">
                <span>Annotation</span>
                <textarea name="annotation" rows="2" data-saisie-annotation></textarea>
            </label>
        </form>

        <div class="pl-modal-pied">
            <form method="post" data-suppr class="pl-modal-suppr" onsubmit="return confirm('Supprimer ce créneau ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-ghost pl-danger">
                    @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer
                </button>
            </form>

            <button type="button" class="btn btn-ghost" data-basculer>Modifier</button>
            <button type="submit" form="pl-form" class="btn" data-enregistrer hidden>Enregistrer</button>
        </div>
    </div>
</div>

<p class="pl-note">
    Créneaux ponctuels : ni détection de conflit de salle ou d'enseignant, ni récurrence —
    chaque séance est saisie pour sa date.
</p>

<style>
    /* Emploi du temps : bascule de vue, légende des classes. */
    .pl-vues { display: inline-flex; gap: 4px; margin-left: auto; padding: 3px; border-radius: 10px; background: #f3f4f9; }
    .pl-vue {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px;
        font-size: 12.5px; font-weight: 600; color: var(--muted);
    }
    .pl-vue svg { stroke: currentColor; }
    .pl-vue:hover { color: var(--brand-deep); }
    .pl-vue.is-active { background: #fff; color: var(--brand-deep); box-shadow: var(--shadow-sm); }

    .pl-legende { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; margin-top: 13px; }
    .pl-legende-label { font-size: 11.5px; font-weight: 600; color: var(--muted); }
    .pl-puce { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: #585e72; white-space: nowrap; }
    .pl-puce i { width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0; }

    .pl-choix { padding: 40px 24px; text-align: center; }
    .pl-choix h2 { margin: 12px 0 0; font-size: 15px; font-weight: 700; letter-spacing: -.015em; }
    .pl-choix p { margin: 7px auto 0; max-width: 56ch; font-size: 13px; color: var(--muted); text-wrap: pretty; }
    .pl-choix-liste { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-top: 18px; }
    .pl-choix-chip {
        display: inline-flex; align-items: center; gap: 7px; padding: 7px 13px; border-radius: 999px;
        border: 1px solid var(--border); background: #fff; font-size: 12.5px; font-weight: 600; color: #585e72;
    }
    .pl-choix-chip:hover { border-color: #c3c6f5; background: #fafbff; color: var(--brand-deep); }
    .pl-choix-chip i { width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0; }
    .pl-choix-lien { display: inline-flex; align-items: center; gap: 6px; margin-top: 18px; font-size: 12.5px; font-weight: 600; }

    .pl-suppr { display: inline-block; margin: 0; }
    .pl-note { margin: 14px 0 0; font-size: 12px; color: var(--muted); }

    /* Filtres : étiquetés, avec les actions et la bascule de vue au bout. */
    .pl-filtres { display: flex; align-items: flex-end; gap: 11px; flex-wrap: wrap; }
    .pl-filtres .stack { display: block; margin: 0; flex: 1 1 180px; min-width: 0; }
    .pl-filtres .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .pl-filtres select { width: 100%; max-width: none; cursor: pointer; }
    .pl-filtres-actions { display: flex; align-items: center; gap: 8px; }
    .pl-filtres-actions .filter-reset { margin-left: 0; }

    /* ---------- Fiche d'un créneau ---------- */
    .pl-modal { position: fixed; inset: 0; z-index: 60; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .pl-modal[hidden] { display: none; }
    .pl-modal-fond { position: absolute; inset: 0; background: rgba(15, 18, 34, .45); }
    .pl-modal-boite {
        position: relative; width: 100%; max-width: 620px; max-height: 88vh; overflow-y: auto;
        background: var(--surface); border-radius: 16px; box-shadow: var(--shadow-lg);
    }

    .pl-modal-tete { display: flex; align-items: flex-start; gap: 12px; padding: 18px 18px 14px; border-bottom: 1px solid var(--border-soft); }
    .pl-modal-eyebrow { font-size: 10.5px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--muted); }
    .pl-modal-tete h2 { margin: 4px 0 0; font-size: 17px; letter-spacing: -.02em; }
    .pl-modal-quand { margin: 4px 0 0; font-size: 12.5px; color: var(--muted); }
    .pl-modal-x {
        margin-left: auto; width: 30px; height: 30px; flex-shrink: 0; padding: 0; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); border-radius: 9px; background: #fff;
    }
    .pl-modal-x:hover { background: #f4f5fa; }

    .pl-modal-infos { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; padding: 16px 18px; }
    .pl-info span { display: block; font-size: 11.5px; color: var(--muted); }
    .pl-info strong { display: block; font-size: 13.5px; font-weight: 600; margin-top: 2px; }

    .pl-modal-form { max-width: none; margin: 0; padding: 4px 18px 16px; border-top: 1px solid var(--border-soft); }
    .pl-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-top: 14px; }
    .pl-modal-form .stack { display: block; margin: 0; min-width: 0; }
    .pl-modal-form .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .pl-modal-form input, .pl-modal-form select, .pl-modal-form textarea { width: 100%; max-width: none; }
    .pl-form-large { margin-top: 12px !important; }

    .pl-modal-pied { display: flex; align-items: center; gap: 8px; padding: 14px 18px; border-top: 1px solid var(--border-soft); background: #fafbfd; }
    .pl-modal-pied .btn:not(.pl-danger) { margin-left: 0; }
    .pl-modal-suppr { display: inline-block; margin: 0 auto 0 0; }
    .pl-danger { color: var(--danger); border-color: #fecaca; display: inline-flex; align-items: center; gap: 6px; }
    .pl-danger:hover { background: var(--danger-bg); }
    .pl-danger svg { stroke: var(--danger); }

    a.cal-event { cursor: pointer; }
</style>

<script>
    (function () {
        var modal = document.querySelector('[data-modal]');
        if (!modal) return;

        var form = modal.querySelector('[data-form]');
        var formSuppr = modal.querySelector('[data-suppr]');
        var basculer = modal.querySelector('[data-basculer]');
        var enregistrer = modal.querySelector('[data-enregistrer]');
        var dernierDeclencheur = null;

        // Chaque champ de la fiche est alimenté par un data-* posé sur le
        // créneau : pas d'aller-retour serveur pour ouvrir la fiche.
        function ecrire(cle, valeur) {
            modal.querySelectorAll('[data-champ-' + cle + ']').forEach(function (el) {
                el.textContent = valeur || '—';
            });
        }

        function saisir(cle, valeur) {
            var champ = modal.querySelector('[data-saisie-' + cle + ']');
            if (champ) champ.value = valeur || '';
        }

        function modeEdition(actif) {
            form.hidden = !actif;
            enregistrer.hidden = !actif;
            basculer.textContent = actif ? 'Annuler la modification' : 'Modifier';
        }

        function ouvrir(evenement) {
            var d = evenement.dataset;

            ['libelle', 'quand', 'classe-nom', 'intervenant-nom', 'campus-nom', 'salle-nom'].forEach(function (cle) {
                // dataset transforme « classe-nom » en « classeNom ».
                var propriete = cle.replace(/-(\w)/g, function (_, c) { return c.toUpperCase(); });
                ecrire(cle, d[propriete]);
            });

            saisir('cours', d.cours);
            saisir('intervenant', d.intervenant);
            saisir('campus', d.campus);
            saisir('classe', d.classe);
            saisir('salle', d.salle);
            saisir('semestre', d.semestre);
            saisir('debut', d.debut);
            saisir('fin', d.fin);
            saisir('annee', d.annee);
            saisir('annotation', d.annotation);

            form.action = d.maj;
            formSuppr.action = d.suppr;

            modeEdition(false);
            modal.hidden = false;
            document.body.style.overflow = 'hidden';
            basculer.focus();
        }

        function fermer() {
            modal.hidden = true;
            document.body.style.overflow = '';
            if (dernierDeclencheur) dernierDeclencheur.focus();
        }

        document.querySelectorAll('.cal-event[data-creneau]').forEach(function (evenement) {
            evenement.addEventListener('click', function (e) {
                // Le href reste en place comme repli : on ne l'annule que si
                // le script a bien pris la main.
                e.preventDefault();
                dernierDeclencheur = evenement;
                ouvrir(evenement);
            });
        });

        modal.querySelectorAll('[data-fermer]').forEach(function (el) {
            el.addEventListener('click', fermer);
        });

        basculer.addEventListener('click', function () { modeEdition(form.hidden); });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hidden) fermer();
        });
    })();
</script>
@endsection
