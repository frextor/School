@extends('layouts.app')

@section('title', "Épreuves d'admission")

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Épreuves d'admission</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Épreuves d'admission</h1>
            <span class="badge badge-brand">{{ $epreuves->total() }}</span>
        </div>
        <p class="page-sub">Les sessions de test auxquelles les candidats s'inscrivent, avec leur capacité.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('epreuves.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouvelle épreuve
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:180px">Date</th>
                    <th style="width:180px">Lieu</th>
                    <th>Cycles concernés</th>
                    <th style="width:180px">Remplissage</th>
                    <th style="width:120px">Résultats</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($epreuves as $epreuve)
                    @php
                        $capacite = max(1, (int) $epreuve->effectif);
                        $taux = min(100, (int) round($epreuve->inscriptions_count / $capacite * 100));
                        $cycles = $epreuve->formations->pluck('niveau');
                    @endphp
                    <tr>
                        <td class="cell-name">{{ $epreuve->date_epreuve->format('d/m/Y') }}
                            <span class="cell-sub">{{ $epreuve->date_epreuve->format('H:i') }}</span>
                        </td>
                        <td>
                            {{ $epreuve->lieu }}
                            @if ($epreuve->distanciel)
                                <span class="badge">à distance</span>
                            @endif
                        </td>
                        <td>
                            @forelse ($cycles as $cycle)
                                <span class="badge">{{ $cycle }}</span>
                            @empty
                                <span style="color:var(--faint)">tous</span>
                            @endforelse
                        </td>
                        <td>
                            <span class="jauge" aria-hidden="true"><span style="width:{{ $taux }}%"></span></span>
                            <span class="jauge-txt">{{ $epreuve->inscriptions_count }} / {{ $epreuve->effectif }} inscrits</span>
                        </td>
                        <td>
                            @if ($epreuve->resultats_count)
                                <span class="badge badge-success">{{ $epreuve->resultats_count }}</span>
                            @else
                                <span class="badge">en attente</span>
                            @endif
                        </td>
                        <td class="col-actions">
                            <a href="{{ route('epreuves.edit', $epreuve) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                            </a>
                            <form method="post" action="{{ route('epreuves.destroy', $epreuve) }}" class="inline-form"
                                  onsubmit="return confirm('Supprimer cette épreuve ? Les inscriptions seront perdues.')">
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
                            @include('partials.icon', ['n' => 'calc', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune épreuve programmée.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">{{ $epreuves->links() }}</div>
</div>

<style>
    .jauge { display: block; height: 5px; border-radius: 999px; background: #eef0f5; overflow: hidden; max-width: 140px; }
    .jauge > span { display: block; height: 100%; border-radius: 999px; background: var(--brand); }
    .jauge-txt { display: block; margin-top: 3px; font-size: 11.5px; color: var(--muted); font-variant-numeric: tabular-nums; }
</style>
@endsection
