{{-- Bandeau d'erreurs de validation, identique sur tous les formulaires. --}}
@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>
            @foreach ($errors->all() as $erreur){{ $erreur }}@if (! $loop->last) @endif @endforeach
        </span>
    </div>
@endif
