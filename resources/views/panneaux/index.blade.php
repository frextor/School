@extends('layouts.app')

@section('title', "Panneaux d'affichage")

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Panneaux d'affichage</span>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

<div class="page-head">
    <div>
        <h1>Panneaux d'affichage</h1>
        <p class="page-sub">Les écrans de couloir qui annoncent les cours à venir. Ouvrez l'adresse d'un panneau sur l'écran concerné : il se met à jour tout seul.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('panneaux.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouveau panneau
        </a>
    </div>
</div>

<div class="pa-grid">
    @forelse ($panneaux as $panneau)
        @php $lien = route('panneaux.affichage', $panneau->identifiant_panneaux); @endphp
        <article class="pa-card">
            <div class="pa-tete">
                <span class="pa-icone">@include('partials.icon', ['n' => 'bulb', 's' => 19, 'c' => 'var(--brand-deep)', 'w' => 1.9])</span>
                <span class="pa-id">
                    <span class="pa-titre">{{ $panneau->titre }}</span>
                    <span class="pa-lieu">{{ $panneau->etablissement?->nom_etablissement ?: 'Établissement non précisé' }}</span>
                </span>
            </div>

            {{-- L'adresse du panneau : c'est elle qu'on saisit une fois dans
                 le navigateur de l'écran, et elle n'était affichée nulle part. --}}
            <div class="pa-lien">
                <span class="pa-lien-label">Adresse à ouvrir sur l'écran</span>
                <div class="pa-lien-ligne">
                    <input type="text" value="{{ $lien }}" readonly data-url aria-label="Adresse du panneau">
                    <button type="button" class="pa-copier" data-copier title="Copier l'adresse">
                        @include('partials.icon', ['n' => 'copy', 's' => 14, 'c' => '#585e72', 'w' => 2])
                    </button>
                </div>
            </div>

            <div class="pa-reglages">
                <span><strong>{{ $panneau->plage_horaire }} h</strong> affichées</span>
                <span><strong>{{ $panneau->delai_horaire }} s</strong> entre deux mises à jour</span>
            </div>

            <div class="pa-portee">
                @forelse ($panneau->classes->take(6) as $classe)
                    <span class="pa-puce">{{ $classe->classe }}</span>
                @empty
                    @if ($panneau->groupes->isEmpty())
                        <span class="pa-puce is-tout">Tout l'établissement</span>
                    @endif
                @endforelse
                @foreach ($panneau->groupes->take(3) as $groupe)
                    <span class="pa-puce">{{ $groupe->nom_groupe }}</span>
                @endforeach
                @if ($panneau->classes->count() > 6)
                    <span class="pa-puce is-plus">+{{ $panneau->classes->count() - 6 }}</span>
                @endif
            </div>

            <div class="pa-pied">
                <a href="{{ $lien }}" target="_blank" rel="noopener" class="btn btn-ghost">
                    @include('partials.icon', ['n' => 'eye', 's' => 14, 'w' => 2])Ouvrir l'affichage
                </a>
                <a href="{{ route('panneaux.edit', $panneau) }}" class="row-btn" title="Modifier">
                    @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                </a>
                <form method="post" action="{{ route('panneaux.destroy', $panneau) }}" class="pa-suppr"
                      onsubmit="return confirm('Supprimer le panneau « {{ $panneau->titre }} » ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="row-btn" title="Supprimer">
                        @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#585e72', 'w' => 2])
                    </button>
                </form>
            </div>
        </article>
    @empty
        <div class="pa-vide">
            @include('partials.icon', ['n' => 'bulb', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
            <p>Aucun panneau configuré</p>
            <span>Créez un panneau, puis ouvrez son adresse sur l'écran du couloir : il affichera les cours des prochaines heures.</span>
            <a href="{{ route('panneaux.create') }}">Créer un panneau</a>
        </div>
    @endforelse
</div>

@if ($panneaux->hasPages())
    <div class="pa-pagination">{{ $panneaux->links() }}</div>
@endif

<style>
    /* Panneaux : une carte par écran, avec son adresse en évidence. */
    .pa-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(330px, 1fr)); gap: 14px; }
    .pa-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 16px; min-width: 0; }

    .pa-tete { display: flex; align-items: center; gap: 12px; }
    .pa-icone {
        width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0; background: var(--brand-light);
        display: flex; align-items: center; justify-content: center;
    }
    .pa-id { min-width: 0; }
    .pa-titre { display: block; font-size: 15px; font-weight: 700; letter-spacing: -.015em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .pa-lieu { display: block; font-size: 12px; color: var(--muted); margin-top: 1px; }

    .pa-lien { margin-top: 14px; }
    .pa-lien-label { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
    .pa-lien-ligne { display: flex; gap: 6px; }
    .pa-lien-ligne input {
        flex: 1; min-width: 0; max-width: none; margin: 0; padding: 8px 11px;
        background: #fafbfd; font-size: 12px; font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    }
    .pa-copier {
        flex-shrink: 0; width: 34px; padding: 0; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); border-radius: 9px; background: #fff;
    }
    .pa-copier:hover { border-color: #c3c6f5; background: #fafbff; }
    .pa-copier.is-ok { border-color: #a7f3d0; background: #ecfdf3; }

    .pa-reglages { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 13px; font-size: 12px; color: var(--muted); }
    .pa-reglages strong { color: var(--ink); font-weight: 600; }

    .pa-portee { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 12px; }
    .pa-puce { padding: 3px 9px; border-radius: 999px; background: #f4f5fa; color: #585e72; font-size: 11.5px; font-weight: 600; }
    .pa-puce.is-tout { background: var(--brand-light); color: var(--brand-deep); }
    .pa-puce.is-plus { background: #fff; border: 1px dashed var(--border); }

    .pa-pied { display: flex; align-items: center; gap: 7px; margin-top: 15px; padding-top: 13px; border-top: 1px solid var(--border-soft); }
    .pa-pied .btn { display: inline-flex; align-items: center; gap: 6px; margin-right: auto; }
    .pa-suppr { display: inline-block; margin: 0; }

    .pa-vide {
        grid-column: 1 / -1; text-align: center; padding: 48px 20px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
    }
    .pa-vide p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .pa-vide span { display: block; margin: 4px auto 0; max-width: 54ch; font-size: 13px; color: var(--muted); }
    .pa-vide a { display: inline-block; margin-top: 10px; font-size: 12.5px; font-weight: 600; }

    .pa-pagination { margin-top: 16px; }
</style>

<script>
    (function () {
        document.querySelectorAll('[data-copier]').forEach(function (bouton) {
            bouton.addEventListener('click', function () {
                var champ = bouton.closest('.pa-lien-ligne').querySelector('[data-url]');
                champ.select();

                // `clipboard` n'est pas disponible hors HTTPS : on retombe sur
                // la sélection, que l'on peut copier au clavier.
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(champ.value).then(function () {
                        bouton.classList.add('is-ok');
                        setTimeout(function () { bouton.classList.remove('is-ok'); }, 1200);
                    });
                } else {
                    document.execCommand('copy');
                    bouton.classList.add('is-ok');
                    setTimeout(function () { bouton.classList.remove('is-ok'); }, 1200);
                }
            });
        });
    })();
</script>
@endsection
