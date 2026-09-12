{{-- Écran de connexion partagé par les 4 guards (admin/intervenant/entreprise/élève).
     Paramètres via @include('auth._login-card', [...]) :
     iconName (clé de partials.icon), title, subtitle, action, usernameLabel,
     remember (bool), footer (bloc HTML optionnel), panelTitle, panelPoints (array). --}}
@php
    $panelTitle = $panelTitle ?? "L'administration de votre école, au même endroit.";
    $panelPoints = $panelPoints ?? [
        'Dossiers élèves et candidats centralisés',
        'Saisie des notes et bulletins PDF',
        'Espaces élève, intervenant et entreprise',
    ];
@endphp

<div class="auth-split">
    @include('auth._panel', ['panelTitle' => $panelTitle, 'panelPoints' => $panelPoints, 'panelTagline' => $panelTagline ?? null])

    <div class="auth-form-col">
        <div class="auth-form-wrap">
            <div class="auth-chip">
                @include('partials.icon', ['n' => $iconName ?? 'lock', 's' => 12, 'w' => 2.2]){{ $title }}
            </div>

            <h1>Connexion</h1>
            <p class="auth-subtitle">{{ $subtitle }}</p>

            @if ($errors->any())
                <div class="status error">
                    @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
                    <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
                </div>
            @endif

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            <form method="post" action="{{ $action }}" class="auth-form">
                @csrf

                <div class="field">
                    <label for="username">{{ $usernameLabel }}</label>
                    <div class="input-icon">
                        @include('partials.icon', ['n' => 'user', 's' => 16, 'c' => '#9aa0b0', 'style' => 'position:absolute;left:12px;top:11px'])
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="field">
                    <div class="field-head">
                        <label for="password">Mot de passe</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                        @endif
                    </div>
                    <div class="input-icon">
                        @include('partials.icon', ['n' => 'lock', 's' => 16, 'c' => '#9aa0b0', 'style' => 'position:absolute;left:12px;top:11px'])
                        <input type="password" name="password" id="password" required autocomplete="current-password">
                        <button type="button" class="reveal" data-reveal="password" aria-label="Afficher le mot de passe">
                            @include('partials.icon', ['n' => 'eye', 's' => 16, 'c' => '#6b7280'])
                        </button>
                    </div>
                </div>

                @if ($remember ?? false)
                    <label class="check"><input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
                @endif

                <button type="submit" class="btn btn-block">
                    Se connecter @include('partials.icon', ['n' => 'arrow-right', 's' => 16, 'c' => '#fff', 'w' => 2.2])
                </button>
            </form>

            @isset($footer)
                {{ $footer }}
            @endisset

            @if (Route::has('eleve.login'))
                <div class="auth-others">
                    <div class="auth-others-title">Autres espaces</div>
                    <div class="auth-others-links">
                        <a href="{{ route('eleve.login') }}">@include('partials.icon', ['n' => 'cap', 's' => 14])Élève</a>
                        <a href="{{ route('intervenant.login') }}">@include('partials.icon', ['n' => 'school', 's' => 14])Intervenant</a>
                        <a href="{{ route('entreprise.login') }}">@include('partials.icon', ['n' => 'building', 's' => 14])Entreprise</a>
                    </div>
                </div>
            @endif

            <a href="{{ route('landing') }}" class="auth-back">
                @include('partials.icon', ['n' => 'arrow-left', 's' => 14, 'w' => 2])Retour à l'accueil
            </a>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('[data-reveal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.reveal);
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.classList.toggle('is-on');
        });
    });
</script>
