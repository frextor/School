@extends('layouts.app')

@section('title', 'Périodes scolaires')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Périodes scolaires</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Périodes scolaires</h1>
            <span class="badge badge-brand">{{ $periodes->total() }}</span>
        </div>
        <p class="page-sub">Le découpage de l'année et son volume horaire, par niveau ou par classe.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('referentiel.periodes-formation.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouvelle période
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:140px">Année</th>
                    <th>Période</th>
                    <th>Niveaux</th>
                    <th>Classes</th>
                    <th style="width:120px">Heures/an</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($periodes as $periode)
                    @php
                        $nomsNiveaux = $periode->niveaux->pluck('nom_niveau');
                        $nomsClasses = $periode->classes->pluck('classe');
                    @endphp
                    <tr>
                        <td><span class="badge">{{ $periode->annee_scolaire }}</span></td>
                        <td class="cell-name">{{ $periode->periode }}</td>
                        <td>
                            @forelse ($nomsNiveaux->take(3) as $nom)
                                <span class="badge">{{ $nom }}</span>
                            @empty
                                <span style="color:var(--faint)">—</span>
                            @endforelse
                            @if ($nomsNiveaux->count() > 3)
                                <span class="badge">+{{ $nomsNiveaux->count() - 3 }}</span>
                            @endif
                        </td>
                        <td>
                            @forelse ($nomsClasses->take(3) as $nom)
                                <span class="badge">{{ $nom }}</span>
                            @empty
                                <span style="color:var(--faint)">—</span>
                            @endforelse
                            @if ($nomsClasses->count() > 3)
                                <span class="badge">+{{ $nomsClasses->count() - 3 }}</span>
                            @endif
                        </td>
                        <td>{{ $periode->nb_heure_annuel ?: '—' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('referentiel.periodes-formation.edit', $periode) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                            </a>
                            <form method="post" action="{{ route('referentiel.periodes-formation.destroy', $periode) }}" class="inline-form"
                                  onsubmit="return confirm('Supprimer la période « {{ $periode->periode }} » ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="row-btn is-danger" title="Supprimer">
                                    @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#b91c1c', 'w' => 2])
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">
                            @include('partials.icon', ['n' => 'cal', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune période scolaire définie.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">{{ $periodes->links() }}</div>
</div>
@endsection
