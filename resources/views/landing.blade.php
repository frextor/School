<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Logiciel de gestion scolaire</title>
    <meta name="description" content="{{ config('app.name') }} réunit admissions, dossiers élèves, référentiel pédagogique, notation, CRM, planning et facturation dans un seul logiciel.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%234f46e5%22 stroke-width=%222%22><path d=%22M22 10L12 5 2 10l10 5 10-5z%22/><path d=%22M6 12.5V17c0 1.7 2.7 3 6 3s6-1.3 6-3v-4.5%22/></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #4f46e5; --brand-dark: #4338ca; --brand-deep: #3730a3; --brand-light: #eef0fe;
            --ink: #171a23; --muted: #6b7280; --soft: #585e72;
            --border: #e8eaf1; --rule: #eceef4;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Instrument Sans', system-ui, -apple-system, sans-serif;
            color: var(--ink); background: #fff; line-height: 1.55; -webkit-font-smoothing: antialiased;
        }
        a { color: var(--brand); text-decoration: none; transition: color .15s ease; }
        a:hover { color: var(--brand-dark); }
        ::selection { background: var(--brand); color: #fff; }

        .wrap { max-width: 1120px; margin: 0 auto; padding: 0 32px; }
        .display { font-family: 'Instrument Serif', Georgia, serif; font-weight: 400; letter-spacing: -.02em; line-height: 1.08; margin: 0; }
        .lede { font-size: 16.5px; color: var(--soft); text-wrap: pretty; }

        /* En-tête */
        header { display: flex; align-items: center; gap: 24px; padding: 26px 0 0; }
        .brand { display: flex; align-items: center; gap: 10px; color: var(--ink); }
        .brand-mark {
            width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
            background: linear-gradient(140deg, #6366f1, var(--brand-dark));
            display: flex; align-items: center; justify-content: center;
        }
        .brand-name { font-size: 17px; font-weight: 700; letter-spacing: -.015em; }
        header nav { display: flex; gap: 26px; margin-left: auto; align-items: center; flex-wrap: wrap; }
        header nav a { font-size: 14px; font-weight: 500; color: var(--soft); }
        header nav a:hover { color: var(--ink); }
        header nav a.nav-btn {
            display: inline-flex; align-items: center; gap: 7px; padding: 9px 15px;
            border-radius: 9px; border: 1px solid var(--border); color: var(--ink); font-weight: 600;
        }
        header nav a.nav-btn:hover { border-color: #c3c6f5; color: var(--brand-deep); }

        /* Boutons */
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 13px 22px; border-radius: 10px;
            background: var(--brand); color: #fff; font-size: 15px; font-weight: 600; border: none;
            font-family: inherit; cursor: pointer; box-shadow: 0 2px 10px rgba(79,70,229,.25);
            transition: background .15s ease;
        }
        .btn:hover { background: var(--brand-dark); color: #fff; }
        .btn-ghost { background: #fff; color: var(--ink); border: 1px solid var(--border); box-shadow: none; }
        .btn-ghost:hover { background: #fff; border-color: #c3c6f5; color: var(--brand-deep); }

        /* Hero */
        .hero { padding: 96px 0 84px; border-bottom: 1px solid var(--rule); }
        .eyebrow { margin: 0 0 26px; font-size: 12px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
        .hero h1 { max-width: 17ch; font-size: 76px; text-wrap: pretty; }
        .hero h1 em { font-style: italic; color: var(--brand); }
        .hero p { margin: 30px 0 0; max-width: 56ch; font-size: 18px; color: var(--soft); text-wrap: pretty; }
        .hero-cta { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 38px; }

        /* Chiffres */
        .facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 32px; padding: 46px 0; border-bottom: 1px solid var(--rule); }
        .fact-value { font-family: 'Instrument Serif', Georgia, serif; font-size: 40px; line-height: 1; letter-spacing: -.02em; }
        .fact-label { margin-top: 8px; font-size: 13.5px; color: var(--muted); max-width: 26ch; text-wrap: pretty; }

        /* Sections */
        section.band { padding: 86px 0 76px; border-bottom: 1px solid var(--rule); }
        section.band h2 { max-width: 22ch; font-size: 46px; }
        section.band > .lede { margin: 20px 0 0; max-width: 54ch; }

        /* Modules */
        .modules { display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); margin-top: 52px; border-top: 1px solid var(--rule); }
        .module { padding: 28px 28px 30px 0; border-bottom: 1px solid var(--rule); }
        .module-head { display: flex; align-items: center; gap: 11px; }
        .module-icon { width: 34px; height: 34px; border-radius: 9px; background: var(--brand-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .module h3 { margin: 0; font-size: 16px; font-weight: 600; letter-spacing: -.01em; }
        .module p { margin: 14px 0 0; font-size: 14.5px; color: var(--soft); max-width: 40ch; text-wrap: pretty; }

        /* Espaces */
        .portals { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 14px; margin-top: 46px; }
        .portal {
            display: block; padding: 24px 22px 22px; border: 1px solid var(--border); border-radius: 14px; color: inherit;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .portal:hover { border-color: #c3c6f5; box-shadow: 0 8px 24px rgba(23,26,35,.06); color: inherit; }
        .portal-icon { width: 34px; height: 34px; border-radius: 9px; background: #f5f6fa; display: flex; align-items: center; justify-content: center; }
        .portal-title { display: block; margin-top: 18px; font-size: 15.5px; font-weight: 600; letter-spacing: -.01em; }
        .portal-body { display: block; margin-top: 7px; font-size: 13.5px; color: var(--muted); text-wrap: pretty; }
        .portal-link { display: inline-flex; align-items: center; gap: 6px; margin-top: 18px; font-size: 13.5px; font-weight: 600; color: var(--brand); }

        /* Arguments */
        .split { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 56px; }
        .points { display: flex; flex-direction: column; gap: 26px; padding-top: 8px; }
        .point { display: flex; gap: 14px; }
        .point svg { flex-shrink: 0; margin-top: 3px; }
        .point-title { font-size: 15.5px; font-weight: 600; letter-spacing: -.01em; }
        .point-body { margin-top: 5px; font-size: 14.5px; color: var(--soft); max-width: 46ch; text-wrap: pretty; }

        /* Démo */
        .demo { padding: 86px 0 96px; }
        .demo .split { align-items: start; }
        .demo h2 { max-width: 16ch; font-size: 46px; }
        .demo-intro { margin: 20px 0 0; max-width: 46ch; }
        .demo-card { border: 1px solid var(--border); border-radius: 16px; padding: 26px; }
        .demo-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 14px; }
        .demo-card label { display: block; min-width: 0; }
        .demo-card label > span { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        .demo-card input, .demo-card textarea {
            width: 100%; border: 1px solid var(--border); background: #fafbfd; border-radius: 10px;
            padding: 10px 12px; font: inherit; font-size: 14px; color: var(--ink); outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .demo-card textarea { resize: vertical; }
        .demo-card input:focus, .demo-card textarea:focus { border-color: #a5a8f0; background: #fff; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
        .demo-card .field-full { margin-top: 14px; }
        .demo-card .btn { width: 100%; justify-content: center; margin-top: 16px; padding: 12px 18px; }
        .demo-note { margin: 12px 0 0; font-size: 12.5px; color: var(--muted); text-align: center; }

        footer { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; padding: 26px 0 40px; border-top: 1px solid var(--rule); }
        footer span, footer a { font-size: 13px; color: var(--muted); }
        footer a:hover { color: var(--ink); }
        .footer-links { display: flex; gap: 20px; margin-left: auto; }

        .status {
            background: #ecfdf3; border: 1px solid #a7f3d0; color: #15803d;
            padding: .7rem 1rem; border-radius: 10px; margin-bottom: 1rem; font-size: 13px;
        }
        .status.error { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }

        @media (max-width: 860px) {
            .wrap { padding: 0 20px; }
            .hero { padding: 64px 0 56px; }
            .hero h1 { font-size: 50px; }
            section.band, .demo { padding: 60px 0 54px; }
            section.band h2, .demo h2 { font-size: 34px; }
            .split { gap: 32px; }
            .module { padding-right: 0; }
        }
    </style>
</head>
<body>
@php
    $modules = [
        ['Élèves & admissions', 'Candidats, épreuves d\'admission, dossiers élèves, échéanciers de paiement.', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8 M22 21v-2a4 4 0 0 0-3-3.9'],
        ['Référentiel pédagogique', 'Niveaux, unités d\'enseignement, cours, classes, volumes horaires et ECTS.', 'M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z'],
        ['Notation & bulletins', 'Saisie des notes par intervenant, décisions de jury, bulletins PDF envoyés.', 'M12 20h9 M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z'],
        ['CRM & recrutement', 'Contacts, entreprises partenaires, relances, salons et réunions d\'information.', 'M20 4H8a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z M2 8h4 M2 12h4 M2 16h4'],
        ['Planning & salles', 'Créneaux par classe et par intervenant, salles, panneaux d\'affichage.', 'M8 2v4 M16 2v4 M3 10h18 M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z'],
        ['Administration', 'Rôles et permissions fins, modèles d\'emails, notifications, multi-campus.', 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
    ];

    $portails = [
        ['admin.login', 'Administration', 'Élèves, pédagogie, CRM, planning, bulletins.', 'M3 21h18 M5 21V7l7-4 7 4v14 M9 10h.01 M15 10h.01'],
        ['eleve.login', 'Élève', 'Planning, résultats, documents, scolarité.', 'M22 10L12 5 2 10l10 5 10-5z M6 12.5V17c0 1.7 2.7 3 6 3s6-1.3 6-3v-4.5'],
        ['intervenant.login', 'Intervenant', 'Classes, émargement, saisie des notes, heures.', 'M2 3h20 M4 3v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3 M12 16v5 M8 21h8'],
        ['entreprise.login', 'Entreprise', 'Alternants, contrats et conventions.', 'M20 4H8a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z M2 8h4 M2 12h4 M2 16h4'],
    ];

    $arguments = [
        ['Multi-campus dès le départ', 'Chaque établissement garde ses formations, ses classes et ses intervenants, sous une direction commune.'],
        ['Droits par rôle', 'Un responsable admissions ne voit pas les mêmes écrans qu\'un responsable pédagogique. Les permissions se règlent écran par écran.'],
        ['Reprise de votre existant', 'Import des contacts et des dossiers élèves depuis vos fichiers actuels, sans repartir de zéro.'],
        ['Bulletins conformes', 'Modèles de bulletins avec signatures, décisions de jury et génération PDF en lot.'],
    ];

    $chiffres = [
        ['6', 'domaines couverts : admissions, pédagogie, notation, CRM, RH, facturation'],
        ['4', 'espaces connectés : administration, élève, intervenant, entreprise'],
        ['1', 'saisie unique du dossier élève, de l\'admission au bulletin'],
        ['0', 'export intermédiaire entre vos services'],
    ];

    $champs = [
        ['nom', 'Nom', 'Votre nom', 'text'],
        ['etablissement', 'Établissement', 'Nom de l\'école', 'text'],
        ['email', 'Email', 'vous@ecole.fr', 'email'],
        ['telephone', 'Téléphone', '06 00 00 00 00', 'text'],
    ];
@endphp

<div class="wrap">
    <header>
        <a href="{{ route('landing') }}" class="brand">
            <span class="brand-mark">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12.5V17c0 1.7 2.7 3 6 3s6-1.3 6-3v-4.5"/></svg>
            </span>
            <span class="brand-name">{{ config('app.name') }}</span>
        </a>
        <nav>
            <a href="#modules">Modules</a>
            <a href="#espaces">Espaces</a>
            <a href="#demo">Démo</a>
            <a href="{{ route('admin.login') }}" class="nav-btn">Se connecter</a>
        </nav>
    </header>

    <section class="hero">
        <p class="eyebrow">Logiciel de gestion scolaire</p>
        <h1 class="display">Toute votre école dans <em>un seul</em> logiciel.</h1>
        <p>
            Admissions, dossiers élèves, référentiel pédagogique, notation et bulletins, CRM, planning et
            facturation. {{ config('app.name') }} réunit ce que votre établissement gère aujourd'hui dans une
            dizaine d'outils séparés.
        </p>
        <div class="hero-cta">
            <a href="#demo" class="btn">
                Demander une démo
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
            </a>
            <a href="{{ route('admin.login') }}" class="btn btn-ghost">Accéder à mon espace</a>
        </div>
    </section>

    <section class="facts">
        @foreach ($chiffres as [$valeur, $libelle])
            <div>
                <div class="fact-value">{{ $valeur }}</div>
                <div class="fact-label">{{ $libelle }}</div>
            </div>
        @endforeach
    </section>

    <section class="band" id="modules">
        <h2 class="display">Six domaines, une seule base de données.</h2>
        <p class="lede">Un élève saisi à l'admission suit tout son parcours : classe, notes, bulletin, alternance, facturation. Sans ressaisie, sans export intermédiaire.</p>

        <div class="modules">
            @foreach ($modules as [$titre, $texte, $d])
                <div class="module">
                    <div class="module-head">
                        <span class="module-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                @foreach (preg_split('/\s(?=M)/', $d) as $seg)<path d="{{ $seg }}"/>@endforeach
                            </svg>
                        </span>
                        <h3>{{ $titre }}</h3>
                    </div>
                    <p>{{ $texte }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="band" id="espaces">
        <h2 class="display">Chaque profil a son espace.</h2>
        <p class="lede">Quatre portails distincts, une seule administration. Vos équipes publient une information une fois, elle apparaît au bon endroit.</p>

        <div class="portals">
            @foreach ($portails as [$route, $titre, $texte, $d])
                <a href="{{ Route::has($route) ? route($route) : '#' }}" class="portal">
                    <span class="portal-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            @foreach (preg_split('/\s(?=M)/', $d) as $seg)<path d="{{ $seg }}"/>@endforeach
                        </svg>
                    </span>
                    <span class="portal-title">{{ $titre }}</span>
                    <span class="portal-body">{{ $texte }}</span>
                    <span class="portal-link">
                        Se connecter
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="band">
        <div class="split">
            <div><h2 class="display" style="max-width:18ch;font-size:46px">Pensé pour le quotidien administratif.</h2></div>
            <div class="points">
                @foreach ($arguments as [$titre, $texte])
                    <div class="point">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        <div>
                            <div class="point-title">{{ $titre }}</div>
                            <div class="point-body">{{ $texte }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="demo" id="demo">
        <div class="split">
            <div>
                <h2 class="display">Voyons votre école dans {{ config('app.name') }}.</h2>
                <p class="lede demo-intro">Trente minutes de démonstration sur vos propres cas : vos formations, vos campus, votre calendrier d'admission.</p>
            </div>

            <div class="demo-card">
                @if (session('demande_demo'))
                    <div class="status">Merci, votre demande est enregistrée. Nous vous répondons sous un jour ouvré.</div>
                @endif
                @if ($errors->any())
                    <div class="status error">@foreach ($errors->all() as $error){{ $error }} @endforeach</div>
                @endif

                <form method="post" action="{{ Route::has('demo.store') ? route('demo.store') : '#' }}">
                    @csrf
                    <div class="demo-grid">
                        @foreach ($champs as [$nom, $libelle, $exemple, $type])
                            <label>
                                <span>{{ $libelle }}</span>
                                <input type="{{ $type }}" name="{{ $nom }}" value="{{ old($nom) }}" placeholder="{{ $exemple }}" required>
                            </label>
                        @endforeach
                    </div>
                    <label class="field-full">
                        <span>Votre besoin</span>
                        <textarea name="message" rows="3" placeholder="Nombre d'élèves, campus, outils actuels…">{{ old('message') }}</textarea>
                    </label>
                    <button type="submit" class="btn">Demander une démo</button>
                </form>
                <p class="demo-note">Réponse sous un jour ouvré. Aucun engagement.</p>
            </div>
        </div>
    </section>

    <footer>
        <span>© {{ date('Y') }} {{ config('app.name') }} — School Tech</span>
        <span class="footer-links">
            <a href="#">Mentions légales</a>
            <a href="#">Confidentialité</a>
            <a href="#demo">Contact</a>
        </span>
    </footer>
</div>
</body>
</html>
