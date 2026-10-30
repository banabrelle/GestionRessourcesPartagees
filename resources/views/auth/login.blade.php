<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — GERES-CENADI</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #dfeee7, #b7d3c5);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .neo-card {
            width: 430px;
            border-radius: 25px;
            background: #dfeee7;
            box-shadow:
                10px 10px 25px #b7c9be,
                -10px -10px 25px #ffffff;
            overflow: hidden;
        }

        /* HEADER */
        .login-header {
            padding: 25px;
            background: #dfeee7;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .login-header-bilingue {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 10px;
            color: #3b5d50;
        }

        .logo-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #dfeee7;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                6px 6px 15px #b7c9be,
                -6px -6px 15px #ffffff;
        }

        .logo-circle i {
            font-size: 28px;
            color: #3f6f4f;
        }

        /* BODY */
        .form-container {
            padding: 35px;
        }

        h2 {
            margin-bottom: 25px;
            color: #3b5d50;
            text-align: center;
            font-weight: bold;
        }

        .input-box {
            display: flex;
            align-items: center;
            background: #dfeee7;
            padding: 12px 15px;
            border-radius: 30px;
            margin-bottom: 18px;
            box-shadow:
                inset 4px 4px 10px #b7c9be,
                inset -4px -4px 10px #ffffff;
        }

        .input-box .icon {
            color: #3f6f4f;
            font-size: 16px;
            margin-right: 10px;
        }

        .input-box input {
            border: none !important;
            outline: none !important;
            background: transparent !important;
            width: 100%;
            box-shadow: none !important;
        }

        .input-box button {
            border: none;
            background: transparent;
            color: #3f6f4f;
            padding: 0;
            margin: 0;
            width: auto;
        }

        .input-box button:hover {
            background: transparent;
            color: #345c42;
        }

        .remember-zone {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .remember-zone a {
            text-decoration: none;
            color: #3f6f4f;
            font-weight: 600;
        }

        .remember-zone a:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 30px;
            background: #3f6f4f;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-btn:hover {
            background: #345c42;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #5d7569;
            padding: 15px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .alert {
            border-radius: 15px;
            font-size: 14px;
        }

        .form-text {
            font-size: 11px;
            margin-top: -10px;
            margin-bottom: 15px;
            color: #5d7569;
            padding-left: 10px;
        }
    </style>
</head>

<body>

<div class="neo-card">

    <!-- HEADER -->
        <div class="text-center pt-4">

        <div class="logo-circle mx-auto mb-3">
            <img src="{{ asset('image/logo.png') }}"
                alt="Logo"
                style="width:45px;">
        </div>

        <h5 class="fw-bold mb-1" style="color:#3b5d50;">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>
            GERES-CENADI
        </h5>

        <small style="color:#5d7569;">
            Plateforme de suivie et de planification des Ressources Partagées
        </small>

    </div>

    <!-- FORMULAIRE -->
    <div class="form-container">

        <h2>
            <i class=""></i>
            Connexion
        </h2>

        {{-- MESSAGE ERREUR --}}
        @if($errors->any())
            <div class="alert alert-danger py-2">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ $errors->first('login') ?? $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- EMAIL OU MATRICULE -->
            <div class="input-box">
                <i class="bi bi-person-badge icon"></i>

                <input
                    type="text"
                    name="login"
                    value="{{ old('login') }}"
                    class="@error('login') is-invalid @enderror"
                    placeholder="ADM01"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>

            <div class="form-text">
                Entrez votre matricule.
            </div>

            <!-- MOT DE PASSE -->
            <div class="input-box">

                <i class="bi bi-lock icon"></i>

                <input
                    type="password"
                    name="password"
                    id="mdp"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >

                <button type="button" onclick="toggleMdp()">
                    <i class="bi bi-eye" id="eyeIco"></i>
                </button>

            </div>

            <!-- OPTIONS -->
            <div class="remember-zone">

                <div>
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                    >

                    <label for="remember">
                        Se souvenir de moi
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                @endif

            </div>

            <!-- BOUTON -->
            <button type="submit" class="login-btn">
                <i class=""></i>
                Se connecter
            </button>

        </form>

    </div>

    {{-- <!-- FOOTER -->
    <div class="footer">
        CENADI &copy; {{ date('Y') }} — Tous droits réservés
    </div> --}}

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleMdp() {

        const input = document.getElementById('mdp');
        const eye = document.getElementById('eyeIco');

        if (input.type === 'password') {
            input.type = 'text';
            eye.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            eye.className = 'bi bi-eye';
        }
    }
</script>

</body>
</html>