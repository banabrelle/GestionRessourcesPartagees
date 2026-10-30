<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changement de mot de passe — GERES-CENADI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html, body {
        height: 100%;
        overflow: hidden; /* ← BLOQUE le défilement de la page */
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #dfeee7, #b7d3c5);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .neo-card {
        width: 430px;
        max-height: 96vh;       /* ← carte limitée à 96% de l'écran */
        border-radius: 25px;
        background: #dfeee7;
        box-shadow:
            10px 10px 25px #b7c9be,
            -10px -10px 25px #ffffff;
        overflow: hidden;
        display: flex;
        flex-direction: column; /* ← structure verticale */
    }

    /* En-tête fixe — ne défile pas */
    .card-top {
        flex-shrink: 0;
        text-align: center;
        padding-top: 20px;
        padding-bottom: 15px;
    }

    /* Corps défilant SI le contenu dépasse */
    .form-container {
        flex: 1;
        overflow-y: auto;       /* ← défilement INTERNE à la carte seulement */
        padding: 25px 35px;
        scrollbar-width: thin;
        scrollbar-color: #b7c9be transparent;
    }

    /* Pied fixe — ne défile pas */
    .footer-card {
        flex-shrink: 0;
        text-align: center;
        font-size: 12px;
        color: #5d7569;
        padding: 12px 15px;
        border-top: 1px solid rgba(0,0,0,0.05);
    }

    .logo-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #dfeee7;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow:
            6px 6px 15px #b7c9be,
            -6px -6px 15px #ffffff;
        margin: 0 auto 10px auto;
    }

    h2 {
        margin-bottom: 20px;
        color: #3b5d50;
        text-align: center;
        font-weight: bold;
        font-size: 20px;
    }

    .input-box {
        display: flex;
        align-items: center;
        background: #dfeee7;
        padding: 12px 15px;
        border-radius: 30px;
        margin-bottom: 15px;
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
        color: #3b5d50;
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
        font-size: 15px;
    }

    .login-btn:hover {
        background: #345c42;
    }

    .alert-neo {
        border-radius: 15px;
        font-size: 13px;
        padding: 10px 15px;
        margin-bottom: 15px;
        background: #fff8e1;
        border: none;
        box-shadow:
            inset 3px 3px 8px #d6cfa8,
            inset -3px -3px 8px #fffde0;
        color: #7a6a20;
    }

    .alert-danger-neo {
        border-radius: 15px;
        font-size: 13px;
        padding: 10px 15px;
        margin-bottom: 15px;
        background: #fdecea;
        border: none;
        box-shadow:
            inset 3px 3px 8px #e8c5c2,
            inset -3px -3px 8px #fff0ef;
        color: #9b3a35;
    }

    .form-text-neo {
        font-size: 11px;
        margin-top: -8px;
        margin-bottom: 12px;
        color: #5d7569;
        padding-left: 10px;
    }

    .badge-first {
        display: inline-block;
        background: #3f6f4f;
        color: white;
        font-size: 10px;
        padding: 3px 10px;
        border-radius: 20px;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }
    </style>
</head>

<body>

<div class="neo-card">

    {{-- EN-TETE FIXE --}}
    <div class="card-top">
        <div class="logo-circle">
            <img src="{{ asset('image/logo.png') }}" alt="Logo" style="width:40px;">
        </div>
        <h5 class="fw-bold mb-1" style="color:#3b5d50; font-size:15px;">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>GERES-CENADI
        </h5>
        <small style="color:#5d7569; font-size:11px;">
            Plateforme de suivi et de planification des Ressources Partagées
        </small>
    </div>

    {{-- CORPS DEFILANT UNIQUEMENT SI NECESSAIRE --}}
    <div class="form-container">

        {{-- <div class="text-center mb-2">
            <span class="badge-first">
                <i class="bi bi-shield-lock me-1"></i>Première connexion
            </span>
        </div> --}}

        <h2>
            <i class="" style="color:#3f6f4f;"></i>
            Nouveau mot de passe
        </h2>

        <div class="alert-neo">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Votre mot de passe a été défini par l'administrateur.
            Modifiez-le pour sécuriser votre compte.
        </div>

        @if ($errors->any())
        <div class="alert-danger-neo">
            <i class="bi bi-x-circle me-2"></i>
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
        @endif

        <form method="POST" action="/changer-mot-de-passe">
            @csrf

            <div class="input-box">
                <i class="bi bi-lock icon"></i>
                <input type="password" name="password" id="password"
                       placeholder="Nouveau mot de passe" required>
                <button type="button" onclick="toggleVisu('password','eye1')">
                    <i class="bi bi-eye" id="eye1"></i>
                </button>
            </div>
            <div class="form-text-neo">
                <i class="bi bi-info-circle me-1"></i>Minimum 8 caractères.
            </div>

            <div class="input-box">
                <i class="bi bi-lock-fill icon"></i>
                <input type="password" name="password_confirmation"
                       id="password_confirmation"
                       placeholder="Confirmer le mot de passe" required>
                <button type="button" onclick="toggleVisu('password_confirmation','eye2')">
                    <i class="bi bi-eye" id="eye2"></i>
                </button>
            </div>
            <div class="form-text-neo" style="margin-bottom:20px;">
                <i class="bi bi-info-circle me-1"></i>Répétez exactement le même mot de passe.
            </div>

            <button type="submit" class="login-btn">
                <i class="bi bi-check-circle-fill me-2"></i>
                Confirmer mon mot de passe
            </button>
        </form>

    </div>

    {{-- PIED FIXE --}}
    <div class="footer-card">
        Connecté en tant que :
        <strong style="color:#3b5d50;">
            {{ auth()->user()->personnel?->nom_complet ?? auth()->user()->name }}
        </strong>
        — {{ auth()->user()->matricule }}
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleVisu(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon  = document.getElementById(iconId);
        if (field.type === 'password') {
            field.type     = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            field.type     = 'password';
            icon.className = 'bi bi-eye';
        }
    }
</script>

</body>
</html>