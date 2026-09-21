@extends('layouts.app')

@section('title', 'Élèves · '.$classe->classe)

@section('content')
@php
    // Un référent joignable par élève : le responsable légal d'abord.
    $referent = function ($eleve) {
        return $eleve->tuteurs
            ->sortByDesc(fn ($t) => (int) ($t->pivot->responsable_legal ?? 0))
            ->first();
    };
@endphp

<div class="crumb">
    <a href="{{ route('espace-intervenant.classes') }}">Mes classes</a>
    <span class="sep">/</span>
    <span class="current">{{ $classe->classe }}</span>
</div>

<div class="page-head">
    <div>
        <h1>{{ $classe->classe }}</h1>
        <p class="page-sub">
            {{ $classe->niveau?->nom_niveau ?: 'Niveau non précisé' }}
            · {{ $eleves->count() }} élève{{ $eleves->count() > 1 ? 's' : '' }}
            · assiduité depuis le {{ $depuis->format('d/m/Y') }}
        </p>
    </div>
    <div class="page-actions">
        <a class="btn btn-ghost" href="{{ route('espace-intervenant.appel-jour') }}">Faire l'appel</a>
    </div>
</div>

@if ($eleves->isNotEmpty())
    <div class="ro-recherche">
        @include('partials.icon', ['n' => 'search', 's' => 15, 'c' => '#9aa0b0', 'w' => 2])
        <input type="search" id="ro-filtre" placeholder="Filtrer par nom…" autocomplete="off" aria-label="Filtrer les élèves">
        <span class="ro-compte" data-compte></span>
    </div>
@endif

<div class="ro-grid">
    @forelse ($eleves as $eleve)
        @php
            $contact = $eleve->contact;
            $nom = $contact?->nom_complet ?: 'Élève n°'.$eleve->id_eleve;
            $stats = $assiduite[$eleve->id_eleve] ?? ['absences' => 0, 'retards' => 0, 'non_justifiees' => 0];
            $tuteur = $referent($eleve);
            $naissance = $contact?->date_naissance;
        @endphp
        <article class="ro-card" data-nom="{{ mb_strtolower($nom) }}">
            <div class="ro-tete">
                <span class="ro-avatar">{{ mb_strtoupper(mb_substr($contact?->prenom ?: '?', 0, 1).mb_substr($contact?->nom ?: '', 0, 1)) }}</span>
                <span class="ro-id">
                    <span class="ro-nom">{{ $nom }}</span>
                    <span class="ro-meta">
                        @if ($naissance)
                            {{ \Carbon\Carbon::parse($naissance)->age }} ans
                        @endif
                        @if ($eleve->numero_massar) · {{ $eleve->numero_massar }} @endif
                    </span>
                </span>
            </div>

            @if ($tuteur)
                <div class="ro-ligne">
                    <span class="ro-cle">{{ $tuteur->lien_libelle ?: 'Famille' }}</span>
                    <span class="ro-val">
                        {{ $tuteur->nom_complet }}
                        @if ($tuteur->telephone)
                            <a href="tel:{{ preg_replace('/\s+/', '', $tuteur->telephone) }}" class="ro-tel">{{ $tuteur->telephone }}</a>
                        @endif
                    </span>
                </div>
            @endif

            @if ($contact?->email)
                <div class="ro-ligne">
                    <span class="ro-cle">Élève</span>
                    <span class="ro-val ro-mail" title="{{ $contact->email }}">{{ $contact->email }}</span>
                </div>
            @endif

            <div class="ro-pied">
                @if ($stats['absences'] || $stats['retards'])
                    @if ($stats['absences'])
                        <span class="ro-pill is-abs">{{ $stats['absences'] }} absence{{ $stats['absences'] > 1 ? 's' : '' }}</span>
                    @endif
                    @if ($stats['retards'])
                        <span class="ro-pill is-ret">{{ $stats['retards'] }} retard{{ $stats['retards'] > 1 ? 's' : '' }}</span>
                    @endif
                    @if ($stats['non_justifiees'])
                        <span class="ro-pill is-nj">{{ $stats['non_justifiees'] }} non justifiée{{ $stats['non_justifiees'] > 1 ? 's' : '' }}</span>
                    @endif
                @else
                    <span class="ro-pill is-ok">Assiduité sans réserve</span>
                @endif
            </div>
        </article>
    @empty
        <div class="ro-vide">
            @include('partials.icon', ['n' => 'users', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
            <p>Aucun élève dans cette classe</p>
            <span>La classe est vide ou ses élèves ne sont pas encore affectés.</span>
        </div>
    @endforelse
</div>

<div class="ro-aucun" data-aucun hidden>Aucun élève ne correspond à ce filtre.</div>

<style>
    /* Liste des élèves d'une classe : une fiche par élève — nom, référent
       joignable, assiduité — au lieu d'un tableau nom / email. */
    .ro-recherche {
        position: relative; display: flex; align-items: center; gap: 10px; margin-bottom: 14px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 10px 14px;
    }
    .ro-recherche svg { flex-shrink: 0; }
    .ro-recherche input { flex: 1; min-width: 0; max-width: none; margin: 0; border: 0; padding: 4px 0; background: transparent; font-size: 13.5px; }
    .ro-recherche input:focus { outline: none; box-shadow: none; }
    .ro-compte { font-size: 12px; color: var(--muted); white-space: nowrap; }

    .ro-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(268px, 1fr)); gap: 14px; }
    .ro-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 15px 16px; min-width: 0; }
    .ro-tete { display: flex; align-items: center; gap: 12px; }
    .ro-avatar {
        width: 42px; height: 42px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(140deg, #6366f1, var(--brand-dark)); color: #fff;
        font-size: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center;
    }
    .ro-id { min-width: 0; }
    .ro-nom { display: block; font-size: 14.5px; font-weight: 700; letter-spacing: -.015em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ro-meta { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }

    .ro-ligne { display: flex; gap: 10px; margin-top: 11px; font-size: 12.5px; }
    .ro-cle { width: 62px; flex-shrink: 0; color: var(--muted); }
    .ro-val { min-width: 0; color: #585e72; }
    .ro-tel { display: block; font-weight: 600; font-variant-numeric: tabular-nums; }
    .ro-mail { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .ro-pied { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 13px; padding-top: 12px; border-top: 1px solid var(--border-soft); }
    .ro-pill { padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .ro-pill.is-ok { background: #e7f6f2; color: #0f766e; }
    .ro-pill.is-abs { background: var(--danger-bg); color: var(--danger-dark); }
    .ro-pill.is-ret { background: #fdf3e3; color: #b45309; }
    .ro-pill.is-nj { background: #f4f5fa; color: #585e72; }

    .ro-vide {
        grid-column: 1 / -1; text-align: center; padding: 48px 20px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
    }
    .ro-vide p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .ro-vide span { display: block; margin-top: 4px; font-size: 13px; color: var(--muted); }
    .ro-aucun { margin-top: 14px; text-align: center; font-size: 13px; color: var(--muted); }
</style>

<script>
    (function () {
        var filtre = document.getElementById('ro-filtre');
        if (!filtre) return;

        var fiches = Array.prototype.slice.call(document.querySelectorAll('.ro-card'));
        var compte = document.querySelector('[data-compte]');
        var aucun = document.querySelector('[data-aucun]');

        function appliquer() {
            var q = filtre.value.trim().toLowerCase();
            var visibles = 0;

            fiches.forEach(function (fiche) {
                var garde = !q || fiche.dataset.nom.indexOf(q) !== -1;
                fiche.hidden = !garde;
                if (garde) visibles++;
            });

            compte.textContent = visibles + ' / ' + fiches.length;
            aucun.hidden = visibles > 0;
        }

        filtre.addEventListener('input', appliquer);
        appliquer();
    })();
</script>
@endsection
