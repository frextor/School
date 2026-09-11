@extends('layouts.app')

@section('title', "Modèles d'email")

@section('content')
    <h1>Modèles d'email</h1>

    <table>
        <thead><tr><th>Catégorie</th><th>Langue</th><th>Sujet</th><th>Actif</th><th></th></tr></thead>
        <tbody>
            @forelse ($textes as $email)
                <tr>
                    <td>{{ $email->categorie }}</td>
                    <td>{{ $email->lang }}</td>
                    <td>{{ $email->sujet }}</td>
                    <td>{{ $email->statut ? 'Oui' : 'Non' }}</td>
                    <td><a href="{{ route('emails.edit', $email) }}">Modifier</a></td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun modèle.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $textes->links() }}
@endsection
