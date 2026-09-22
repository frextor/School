@extends('layouts.app')

@section('title', 'Évaluations & notes')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Évaluations &amp; notes</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Évaluations &amp; notes</h1>
            <span class="badge badge-brand">{{ $evaluations->total() }}</span>
        </div>
        <p class="page-sub">Contrôles et examens programmés. La saisie des notes s'ouvre depuis chaque ligne.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('evaluations.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouvelle évaluation
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Évaluation</th>
                    <th style="width:130px">Date</th>
                    <th style="width:180px">Matière</th>
                    <th style="width:200px">Campus</th>
                    <th style="width:140px">Notes</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($evaluations as $evaluation)
                    <tr>
                        <td class="cell-name">
                            {{ $evaluation->nom_evaluation }}
                            <span class="cell-sub">
                                {{ $evaluation->referentiel === 'groupe' ? 'Groupe' : 'Classe' }}
                                · année {{ $evaluation->annee }} · S{{ $evaluation->semestre }}
                            </span>
                        </td>
                        <td>{{ $evaluation->date_evaluation->format('d/m/Y') }}</td>
                        <td>{{ $evaluation->matiere?->nom_cours ?? '—' }}</td>
                        <td>{{ $evaluation->campus?->nom_etablissement ?? '—' }}</td>
                        <td>
                            <a href="{{ route('notes.index', $evaluation) }}" class="lien-notes">
                                @include('partials.icon', ['n' => 'pencil', 's' => 13, 'w' => 2])Saisir
                            </a>
                        </td>
                        <td class="col-actions">
                            <a href="{{ route('evaluations.edit', $evaluation) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                            </a>
                            <form method="post" action="{{ route('evaluations.destroy', $evaluation) }}" class="inline-form"
                                  onsubmit="return confirm('Supprimer l\'évaluation « {{ $evaluation->nom_evaluation }} » ? Les notes saisies seront perdues.')">
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
                            @include('partials.icon', ['n' => 'pencil', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune évaluation programmée.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">{{ $evaluations->links() }}</div>
</div>

<style>
    .lien-notes { display: inline-flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 600; }
    .lien-notes svg { stroke: currentColor; }
</style>
@endsection
