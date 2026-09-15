@extends('layouts.app')

@section('title', 'Assiduité')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Assiduité</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Assiduité</h1>
            <span class="badge badge-brand">{{ number_format($compteurs['total'], 0, ',', ' ') }} sur la période</span>
        </div>
        <p class="page-sub">Absences et retards saisis, justifiés ou non.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('absences.appel') }}" class="btn">
            @include('partials.icon', ['n' => 'check-simple', 's' => 15, 'w' => 2.2])Faire l'appel
        </a>
    </div>
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

<div class="stats-row">
    <div class="stat"><span class="stat-value">{{ $compteurs['absences'] }}</span><span class="stat-label">Absences</span></div>
    <div class="stat"><span class="stat-value">{{ $compteurs['retards'] }}</span><span class="stat-label">Retards</span></div>
    <div class="stat is-alert"><span class="stat-value">{{ $compteurs['non_justifiees'] }}</span><span class="stat-label">Non justifiées</span></div>
</div>

<form method="get" class="filter-card">
    <div class="filter-row">
        <label class="stack"><span>Du</span><input type="date" name="du" value="{{ $du }}"></label>
        <label class="stack"><span>Au</span><input type="date" name="au" value="{{ $au }}"></label>
        <label class="stack" style="flex:1 1 180px">
            <span>Classe</span>
            <select name="classe">
                <option value="">Toutes</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id_classe }}" @selected(($filtres['classe'] ?? null) == $c->id_classe)>{{ $c->classe }}</option>
                @endforeach
            </select>
        </label>
        <label class="stack">
            <span>Nature</span>
            <select name="nature">
                <option value="">Toutes</option>
                @foreach (\App\Models\AbsenceEleve::NATURES as $valeur => $libelle)
                    <option value="{{ $valeur }}" @selected(($filtres['nature'] ?? null) === $valeur)>{{ $libelle }}</option>
                @endforeach
            </select>
        </label>
        <label class="check" style="margin:0">
            <input type="checkbox" name="non_justifiees" value="1" @checked($filtres['non_justifiees'] ?? false)>
            Non justifiées seulement
        </label>
    </div>
    <div class="filter-foot">
        <a href="{{ route('absences.index') }}" class="filter-reset">Réinitialiser</a>
        <button type="submit" class="btn">Filtrer</button>
    </div>
</form>

<div class="table-card">
    <div class="table-head">
        <span class="table-count">
            @if ($absences->total())
                {{ $absences->firstItem() }}–{{ $absences->lastItem() }} sur {{ number_format($absences->total(), 0, ',', ' ') }}
            @else
                Aucun résultat
            @endif
        </span>
    </div>

    <div class="table-scroll">
        <table class="data-table" style="min-width:820px">
            <thead>
                <tr>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Nature</th>
                    <th>Statut</th>
                    <th>Motif</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($absences as $absence)
                    <tr>
                        <td>
                            <a href="{{ route('eleves.show', $absence->id_eleve) }}" class="cell-name">
                                {{ $absence->eleve?->contact?->nom_complet ?? 'Élève #'.$absence->id_eleve }}
                            </a>
                        </td>
                        <td class="muted">{{ $absence->eleve?->classe?->classe ?? '—' }}</td>
                        <td>{{ $absence->date_absence?->format('d/m/Y') }}</td>
                        <td class="muted">{{ \Illuminate\Support\Str::of($absence->heure_absence)->substr(0, 5) }}</td>
                        <td>
                            <span class="pill" style="background:{{ $absence->nature === 'retard' ? '#fdf3e3' : '#fdecef' }};color:{{ $absence->nature === 'retard' ? '#b45309' : '#be123c' }}">
                                {{ $absence->nature_libelle }}
                            </span>
                        </td>
                        <td>
                            @if ($absence->justifie)
                                <span class="dot-status" style="color:#0f766e"><span class="dot"></span>Justifiée</span>
                            @else
                                <span class="dot-status" style="color:#b45309"><span class="dot"></span>Non justifiée</span>
                            @endif
                        </td>
                        <td class="muted">
                            {{ $absence->annotation ?: '—' }}
                            @if ($absence->justificatif_fichers)
                                <a href="{{ Storage::disk('public')->url('justificatifs/'.$absence->justificatif_fichers) }}" target="_blank">pièce</a>
                            @endif
                        </td>
                        <td class="col-actions">
                            <button type="button" class="row-btn" data-just-toggle="{{ $absence->id_absence }}" title="Justifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72'])
                            </button>
                        </td>
                    </tr>
                    <tr class="just-row" id="just-{{ $absence->id_absence }}" hidden>
                        <td colspan="8">
                            <form method="post" action="{{ route('absences.justifier', $absence) }}" enctype="multipart/form-data" class="just-form">
                                @csrf
                                <label class="stack" style="flex:1 1 240px">
                                    <span>Motif</span>
                                    <input type="text" name="annotation" value="{{ $absence->annotation }}" placeholder="Certificat médical, raison familiale…">
                                </label>
                                <label class="stack">
                                    <span>Justificatif (PDF ou image)</span>
                                    <input type="file" name="piece">
                                </label>
                                <input type="hidden" name="justifie" value="{{ $absence->justifie ? 0 : 1 }}">
                                <button type="submit" class="btn">{{ $absence->justifie ? 'Retirer la justification' : 'Justifier' }}</button>
                            </form>

                            <form method="post" action="{{ route('absences.destroy', $absence) }}" style="margin-top:8px"
                                  onsubmit="return confirm('Supprimer cette ligne ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-cell">
                            @include('partials.icon', ['n' => 'check', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune absence sur cette période.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-foot">{{ $absences->links() }}</div>
</div>

<style>
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 14px; }
    .stat { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 16px 18px; }
    .stat-value { display: block; font-size: 26px; font-weight: 700; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
    .stat-label { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; }
    .stat.is-alert .stat-value { color: #b45309; }

    .filter-card .stack { margin: 0; }
    .filter-card .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .filter-card .stack input, .filter-card .stack select { width: 100%; max-width: none; }

    .just-row td { background: #fbfbff; }
    .just-form { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; max-width: none; margin: 0; }
    .just-form .stack { margin: 0; }
    .just-form .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .just-form input { max-width: none; }
    .dot-status { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; }
    .dot-status .dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .pill { display: inline-flex; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 700; }
</style>

<script>
    document.querySelectorAll('[data-just-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var ligne = document.getElementById('just-' + btn.dataset.justToggle);
            ligne.hidden = !ligne.hidden;
        });
    });
</script>
@endsection
