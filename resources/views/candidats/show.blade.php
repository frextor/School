@extends('layouts.app')

@section('title', 'Candidat · '.($candidat->contact?->nom_complet ?? ''))

@section('content')
@php
    $contact = $candidat->contact;
    $initiales = mb_strtoupper(mb_substr($contact->nom ?? '?', 0, 1).mb_substr($contact->prenom ?? '', 0, 1));
    // `visible` marque le candidat archivé côté legacy (archive() le passe à
    // true) : le nommer « archivé » ici évite de lire l'inverse.
    $archive = (bool) $candidat->visible;
@endphp

<div class="crumb">
    <a href="{{ route('candidats.index') }}">Candidats</a>
    <span class="sep">/</span>
    <span class="current">{{ $contact?->nom_complet }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div class="fiche-tete">
        <span class="fiche-photo fiche-initiales">{{ $initiales }}</span>
        <div>
            <div class="title-row">
                <h1>{{ $contact?->nom_complet }}</h1>
                @if ($estAdmis)
                    <span class="badge badge-success">Admis</span>
                @endif
                @if ($archive)
                    <span class="badge">Archivé</span>
                @endif
            </div>
            <p class="page-sub">
                {{ $contact?->email ?: 'Aucune adresse e-mail' }}
                @if ($candidat->niveau) · candidature en {{ $candidat->niveau->nom_niveau }} @endif
            </p>
        </div>
    </div>
    <div class="page-actions">
        @if ($contact)
            <a class="btn btn-ghost" href="{{ route('contacts.show', $contact) }}">
                @include('partials.icon', ['n' => 'contact', 's' => 15, 'w' => 2])Fiche famille
            </a>
        @endif
        <form method="post" action="{{ $archive ? route('candidats.unarchive', $candidat) : route('candidats.archive', $candidat) }}" class="inline-form">
            @csrf
            <button type="submit" class="btn btn-ghost">{{ $archive ? 'Désarchiver' : 'Archiver' }}</button>
        </form>
    </div>
</div>

<div class="form-page is-wide">
    @if ($estAdmis)
        {{-- L'inscription est l'action attendue sur un candidat admis :
             elle vient avant les tableaux, pas après. --}}
        <form method="post" action="{{ route('candidats.inscrire-eleve', $candidat) }}">
            @csrf
            <section class="form-card cd-admis">
                <div class="form-head">
                    <h2>Inscrire comme élève</h2>
                    <span class="form-sub">Le candidat a un résultat d'admission favorable.</span>
                </div>

                <div class="form-grid">
                    <label class="field">
                        <span>Niveau</span>
                        <select name="id_niveau" required id="ie_niveau">
                            <option value="">— Choisir —</option>
                            @foreach ($niveaux as $niveau)
                                <option value="{{ $niveau->id_niveau }}" @selected(old('id_niveau', $candidat->id_niveau) == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="field">
                        <span>Classe</span>
                        <select name="id_classe" required id="ie_classe" disabled>
                            <option value="">Choisissez un niveau d'abord</option>
                        </select>
                    </label>

                    <div class="field cd-valider">
                        <button type="submit" class="btn">Inscrire comme élève</button>
                    </div>
                </div>
            </section>
        </form>
    @endif

    <section class="form-card">
        <div class="form-head"><h2>Épreuves d'admission</h2></div>
        <div class="table-scroll">
            <table class="data-table" style="min-width:auto">
                <thead>
                    <tr>
                        <th style="width:200px">Date</th>
                        <th>Lieu</th>
                        <th style="width:140px">Présence</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($candidat->epreuvesInscriptions as $inscription)
                        <tr>
                            <td class="cell-name">{{ $inscription->epreuve?->date_epreuve?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>{{ $inscription->epreuve?->lieu ?? '—' }}</td>
                            <td>
                                @if ($inscription->presence)
                                    <span class="badge badge-success">Présent</span>
                                @else
                                    <span class="badge">Absent</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty-cell">
                                @include('partials.icon', ['n' => 'calc', 's' => 24, 'c' => '#c9cdd9', 'w' => 1.6])
                                <span>Aucune épreuve programmée pour ce candidat.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="form-card">
        <div class="form-head"><h2>Résultats</h2></div>
        <div class="table-scroll">
            <table class="data-table" style="min-width:auto">
                <thead>
                    <tr>
                        <th style="width:150px">Épreuve</th>
                        <th class="num">Anglais</th>
                        <th class="num">Culture générale</th>
                        <th class="num">Rédaction</th>
                        <th class="num">Entretien</th>
                        <th style="width:150px">Décision</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($candidat->resultatsEpreuves as $resultat)
                        <tr>
                            <td class="cell-name">{{ $resultat->epreuve?->date_epreuve?->format('d/m/Y') ?? '—' }}</td>
                            <td class="num">{{ $resultat->anglais ?: '—' }}</td>
                            <td class="num">{{ $resultat->culture_generale ?: '—' }}</td>
                            <td class="num">{{ $resultat->epreuve_redaction ?: '—' }}</td>
                            <td class="num">{{ $resultat->entretien ?: '—' }}</td>
                            <td>
                                @if ($resultat->decision)
                                    <span class="badge badge-brand">{{ $resultat->decision }}</span>
                                @else
                                    <span style="color:var(--faint)">en attente</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">
                                @include('partials.icon', ['n' => 'file', 's' => 24, 'c' => '#c9cdd9', 'w' => 1.6])
                                <span>Aucun résultat saisi.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<form method="post" action="{{ route('candidats.destroy', $candidat) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer le candidat {{ $contact?->nom_complet }} ? Ses inscriptions aux épreuves et ses résultats seront perdus.')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer ce candidat
    </button>
</form>

<style>
    .fiche-tete { display: flex; align-items: center; gap: 15px; }
    .fiche-tete h1 { margin: 0; }
    .fiche-photo { width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0; }
    .fiche-initiales {
        display: inline-flex; align-items: center; justify-content: center;
        background: linear-gradient(140deg, #6366f1, var(--brand-dark));
        color: #fff; font-size: 17px; font-weight: 700;
    }
    .cd-admis { border-color: #c3c6f5; }
    .cd-valider { display: flex; align-items: flex-end; }
    .data-table th.num, .data-table td.num { text-align: right; font-variant-numeric: tabular-nums; }
</style>

@if ($estAdmis)
    <script>
        (function () {
            // La classe dépend du niveau : on ne propose que celles qui existent.
            var classes = @json($classes->map(fn ($c) => ['id_classe' => $c->id_classe, 'classe' => $c->classe, 'id_niveau' => $c->id_niveau]));
            var niveau = document.getElementById('ie_niveau');
            var classe = document.getElementById('ie_classe');
            if (!niveau || !classe) return;

            function filtrerClasses() {
                var id = parseInt(niveau.value, 10);
                var options = classes.filter(function (c) { return c.id_niveau === id; });

                if (!options.length) {
                    classe.innerHTML = '<option value="">Aucune classe à ce niveau</option>';
                    classe.disabled = true;
                    return;
                }

                classe.innerHTML = '<option value="">— Choisir —</option>' + options.map(function (c) {
                    return '<option value="' + c.id_classe + '">' + c.classe + '</option>';
                }).join('');
                classe.disabled = false;
            }

            niveau.addEventListener('change', filtrerClasses);
            if (niveau.value) filtrerClasses();
        })();
    </script>
@endif
@endsection
