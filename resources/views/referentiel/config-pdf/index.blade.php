@extends('layouts.app')

@section('title', 'Configurations PDF')

@section('content')
    <h1>Configurations PDF — {{ ucfirst($type) }}</h1>
    <a class="btn" href="{{ route('referentiel.config-pdf.create', $type) }}">+ Nouvelle configuration</a>

    <table>
        <thead>
            <tr>
                <th>Logo</th>
                <th>Établissements</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($configs as $config)
                <tr>
                    <td>
                        @if ($config->logo)
                            <img src="{{ Storage::disk('public')->url('logo_etablissements/'.$config->logo) }}" style="max-height:40px">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $config->etablissements->pluck('nom_etablissement')->join(', ') ?: '-' }}</td>
                    <td>
                        <a href="{{ route('referentiel.config-pdf.edit', [$type, $config]) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.config-pdf.destroy', [$type, $config]) }}" style="display:inline" onsubmit="return confirm('Supprimer cette configuration ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">Aucune configuration.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $configs->links() }}
@endsection
