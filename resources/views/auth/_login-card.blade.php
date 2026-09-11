{{-- Partial de connexion partagé par les 4 guards (admin/intervenant/entreprise/élève).
     Paramètres attendus via @include('auth._login-card', [...]) :
     icon, title, subtitle, action, usernameLabel, remember (bool), footer (bloc HTML optionnel). --}}
<div class="auth-icon">{{ $icon }}</div>
<h1>{{ $title }}</h1>
<p class="auth-subtitle">{{ $subtitle }}</p>

@if ($errors->any())
    <div class="status error">
        @foreach ($errors->all() as $error)
            <span>{{ $error }}</span>
        @endforeach
    </div>
@endif

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

<form method="post" action="{{ $action }}">
    @csrf

    <label for="username">{{ $usernameLabel }}</label>
    <div class="input-icon">
        <span class="ico">👤</span>
        <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus autocomplete="username">
    </div>

    <label for="password">Mot de passe</label>
    <div class="input-icon">
        <span class="ico">🔒</span>
        <input type="password" name="password" id="password" required autocomplete="current-password">
    </div>

    @if ($remember ?? false)
        <label><input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
    @endif

    <button type="submit" class="btn">Se connecter →</button>
</form>

@isset($footer)
    {{ $footer }}
@endisset

<a href="{{ route('landing') }}" class="auth-back">← Retour à l'accueil</a>
