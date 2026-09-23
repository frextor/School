@extends('layouts.app')

@section('title', 'Règlement · '.$paiement->titre)

@section('content')
@php
    $dh = fn ($v) => number_format((float) $v, 2, ',', ' ').' DH';
    $totalOptions = $paiement->options->sum('montant');
    $totalVerse = $paiement->cheques->sum('montant_paiement');
@endphp

<div class="crumb">
    <a href="{{ route('eleves.index') }}">Élèves</a>
    <span class="sep">/</span>
    <a href="{{ route('paiements.index', $paiement->id_eleve) }}">Règlements</a>
    <span class="sep">/</span>
    <span class="current">{{ $paiement->titre }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>{{ $paiement->titre }}</h1>
        <p class="page-sub">
            {{ $paiement->eleve?->contact?->nom_complet }}
            @if ($paiement->date) · {{ $paiement->date->format('d/m/Y') }} @endif
        </p>
    </div>
</div>

<div class="form-page is-wide">
    <div class="rg-totaux">
        <div class="rg-total is-brand">
            <span class="rg-label">Total versé</span>
            <span class="rg-value">{{ $dh($totalVerse) }}</span>
            <span class="rg-hint">{{ $paiement->cheques->count() }} versement{{ $paiement->cheques->count() > 1 ? 's' : '' }}</span>
        </div>
        @if ($paiement->options->isNotEmpty())
            <div class="rg-total">
                <span class="rg-label">Options facturées</span>
                <span class="rg-value">{{ $dh($totalOptions) }}</span>
                <span class="rg-hint">{{ $paiement->options->count() }} option{{ $paiement->options->count() > 1 ? 's' : '' }}</span>
            </div>
        @endif
    </div>

    @if ($paiement->options->isNotEmpty())
        <section class="form-card">
            <div class="form-head"><h2>Options facturées</h2></div>
            <div class="table-scroll">
                <table class="data-table" style="min-width:auto">
                    <thead>
                        <tr><th>Option</th><th style="width:170px;text-align:right">Montant</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($paiement->options as $option)
                            <tr>
                                <td class="cell-name">{{ $option->niveauOption?->titre ?? '—' }}</td>
                                <td style="text-align:right">{{ $dh($option->montant) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td style="text-align:right">{{ $dh($totalOptions) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    @endif

    <section class="form-card">
        <div class="form-head">
            <h2>Versements</h2>
            <span class="form-sub">Chèques, virements et espèces rattachés à ce règlement.</span>
        </div>

        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:150px">Montant</th>
                        <th style="width:150px">Mode</th>
                        <th style="width:150px">N° de chèque</th>
                        <th>Banque</th>
                        <th style="width:170px">Encaissement</th>
                        <th style="width:130px">Justificatif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paiement->cheques as $cheque)
                        <tr>
                            <td class="cell-name">{{ $dh($cheque->montant_paiement) }}</td>
                            <td>{{ $cheque->mode_paiement ?: '—' }}</td>
                            <td>{{ $cheque->numero_cheque ?: '—' }}</td>
                            <td>{{ $cheque->nom_banque ?: '—' }}</td>
                            <td>{{ $cheque->date_encaissement?->format('d/m/Y') ?: '—' }}</td>
                            <td>
                                @if ($cheque->photo_cheque)
                                    <a href="{{ Storage::disk('public')->url('cheques/'.$cheque->photo_cheque) }}" target="_blank" rel="noopener">Voir</a>
                                @else
                                    <span style="color:var(--faint)">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">
                                @include('partials.icon', ['n' => 'card', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                                <span>Aucun versement enregistré sur ce règlement.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <form method="post" action="{{ route('paiements.cheques.store', $paiement) }}" enctype="multipart/form-data">
        @csrf
        <section class="form-card">
            <div class="form-head"><h2>Ajouter un versement</h2></div>
            <div class="form-grid is-tight">
                <label class="field">
                    <span>Montant (DH)</span>
                    <input type="number" step="0.01" min="0" name="montant_paiement" required value="{{ old('montant_paiement') }}">
                </label>

                <label class="field">
                    <span>Mode de règlement</span>
                    <input type="text" name="mode_paiement" list="modes-reglement" value="{{ old('mode_paiement') }}" placeholder="Espèces, chèque…">
                    <datalist id="modes-reglement">
                        <option value="Espèces"></option>
                        <option value="Chèque"></option>
                        <option value="Virement"></option>
                        <option value="Carte bancaire"></option>
                    </datalist>
                </label>

                <label class="field">
                    <span>N° de chèque</span>
                    <input type="text" name="numero_cheque" value="{{ old('numero_cheque') }}">
                </label>

                <label class="field">
                    <span>Banque</span>
                    <input type="text" name="nom_banque" value="{{ old('nom_banque') }}">
                </label>

                <label class="field">
                    <span>Date d'encaissement</span>
                    <input type="date" name="date_encaissement" value="{{ old('date_encaissement') }}">
                </label>

                <label class="field">
                    <span>Photo du chèque</span>
                    <input type="file" name="photo_cheque" accept="image/*">
                </label>
            </div>
        </section>

        <div class="form-actions">
            <button type="submit" class="btn">Ajouter le versement</button>
        </div>
    </form>
</div>

<form method="post" action="{{ route('paiements.destroy', $paiement) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer le règlement « {{ $paiement->titre }} » et tous ses versements ?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer ce règlement
    </button>
</form>

<style>
    .rg-totaux { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; margin-bottom: 14px; }
    .rg-total { background: var(--surface); border: 1px solid var(--border); border-radius: 13px; padding: 13px 15px; }
    .rg-total.is-brand { background: #fafbff; border-color: #dfe2fb; }
    .rg-label { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .rg-value { display: block; margin-top: 5px; font-size: 22px; font-weight: 700; letter-spacing: -.025em; font-variant-numeric: tabular-nums; }
    .rg-total.is-brand .rg-value { color: var(--brand-deep); }
    .rg-hint { display: block; margin-top: 2px; font-size: 11.5px; color: var(--muted); }
    .rg-totaux + .form-card, .form-page > form { margin-top: 0; }
    .form-page > form + form { margin-top: 14px; }
</style>
@endsection
