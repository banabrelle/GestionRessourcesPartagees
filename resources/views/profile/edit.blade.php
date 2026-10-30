@extends('layouts.app')
@section('title', 'Mon Profil')

@section('content')
<style>
  body{
    background-color: #E3F3FC;
  }
</style>
 

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="color:#1F3864">
            <i class="bi bi-person-circle me-2"></i>Mon Profil
        </h4>
        <small class="text-muted">
            @if(auth()->user()->hasRole('administrateur'))
                Vous pouvez modifier vos informations et gérer votre compte
            @else
                Consultez vos informations personnelles
            @endif
        </small>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="row g-4">

    {{-- ── Colonne gauche : avatar + infos rapides ── --}}
    <div class="col-lg-4">

        <div class="card shadow-sm border-0 mb-3" style="border-top: 3px solid #1F3864 !important;">
            <div class="card-body text-center py-4">

                @if(auth()->user()->personnel?->photo)
                    <img src="{{ Storage::url(auth()->user()->personnel->photo) }}"
                         class="rounded-circle mb-3"
                         style="width:90px;height:90px;object-fit:cover;
                                border:3px solid #1F3864;box-shadow:0 4px 15px rgba(31,56,100,.2)">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center
                                fw-bold text-white mx-auto mb-3"
                         style="width:90px;height:90px;font-size:30px;
                                background:linear-gradient(135deg,#1F3864,#2E75B6);
                                box-shadow:0 4px 15px rgba(31,56,100,.3)">
                        {{ strtoupper(substr(auth()->user()->personnel?->prenom ?? auth()->user()->name, 0, 1))
                         . strtoupper(substr(auth()->user()->personnel?->nom ?? '', 0, 1)) }}
                    </div>
                @endif

                <h5 class="fw-bold mb-1">
                    {{ auth()->user()->personnel?->prenom ?? '' }}
                    {{ auth()->user()->personnel?->nom ?? auth()->user()->name }}
                </h5>
                <div class="text-muted small mb-3">{{ auth()->user()->email }}</div>

                @php
                    $role = auth()->user()->getRoleNames()->first() ?? '—';
                    $roleColor = match($role) {
                        'administrateur' => 'danger',
                        'chef_division'  => 'warning text-dark',
                        'personnel'      => 'primary',
                        default          => 'secondary',
                    };
                @endphp
                <span class="badge bg-{{ $roleColor }} px-3 py-2 mb-2">
                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                </span>

                <div class="border-top pt-3 mt-2">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Matricule</span>
                        <code class="fw-bold small">{{ auth()->user()->matricule }}</code>
                    </div>
                    @if(auth()->user()->personnel?->division)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Division</span>
                        <span class="badge" style="background:#1F3864;font-size:11px">
                            {{ auth()->user()->personnel->division->code }}
                        </span>
                    </div>
                    @endif
                    @if(auth()->user()->personnel?->service)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Service</span>
                        <span class="small text-end" style="max-width:160px">
                            {{ Str::limit(auth()->user()->personnel->service->nom, 25) }}
                        </span>
                    </div>
                    @endif
                    @if(auth()->user()->personnel?->telephone)
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Téléphone</span>
                        <span class="small">{{ auth()->user()->personnel->telephone }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Statistiques --}}
        @if(auth()->user()->personnel)
        @php
            $reservations = auth()->user()->personnel->reservations;
            $total    = $reservations->count();
            $valides  = $reservations->where('statut','valide_admin')->count();
            $attente  = $reservations->whereIn('statut',['en_attente','valide_chef'])->count();
        @endphp
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom fw-semibold small text-uppercase"
                 style="color:#1F3864;letter-spacing:.5px">
                Mon activité
            </div>
            <div class="card-body py-3">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="fw-bold fs-4" style="color:#1F3864">{{ $total }}</div>
                        <div class="text-muted" style="font-size:11px">Total</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-success">{{ $valides }}</div>
                        <div class="text-muted" style="font-size:11px">Validées</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-warning">{{ $attente }}</div>
                        <div class="text-muted" style="font-size:11px">En attente</div>
                    </div>
                </div>
                @if($total > 0)
                <div class="progress mt-3" style="height:6px;border-radius:10px">
                    <div class="progress-bar bg-success"
                         style="width:{{ round($valides/$total*100) }}%"></div>
                    <div class="progress-bar bg-warning"
                         style="width:{{ round($attente/$total*100) }}%"></div>
                </div>
                <small class="text-muted">
                    Taux de validation : {{ $total > 0 ? round($valides/$total*100) : 0 }}%
                </small>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ── Colonne droite ── --}}
    <div class="col-lg-8">

        {{-- Messages flash --}}
        @if(session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>Profil mis à jour avec succès.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3">
            <i class="bi bi-shield-check me-2"></i>Mot de passe modifié avec succès.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- CAS 1 : UTILISATEUR SIMPLE — lecture seule            --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @if(!auth()->user()->hasRole('administrateur'))

        <div class="card shadow-sm border-0" style="border-top: 3px solid #2E75B6 !important;">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <div class="rounded d-flex align-items-center justify-content-center"
                     style="width:32px;height:32px;background:#e8f4fd">
                    <i class="bi bi-person-lines-fill" style="color:#1F3864"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="color:#1F3864">Informations personnelles</div>
                    <small class="text-muted">Vos informations sont en lecture seule</small>
                </div>
            </div>
            <div class="card-body p-4">

                {{-- Bannière info --}}
                <div class="d-flex align-items-start gap-3 p-3 rounded mb-4"
                     style="background:#f0f6ff; border:1px solid #c7ddf7;">
                    <i class="bi bi-info-circle-fill mt-1" style="color:#2E75B6; flex-shrink:0;"></i>
                    <div style="font-size:.845rem; color:#374151;">
                        Pour modifier vos informations (email, nom),
                        contactez un <strong>administrateur</strong>.
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase"
                               style="color:#6b7280; letter-spacing:.4px;">Nom affiché</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ auth()->user()->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase"
                               style="color:#6b7280; letter-spacing:.4px;">Matricule</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ auth()->user()->matricule }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase"
                               style="color:#6b7280; letter-spacing:.4px;">Adresse email</label>
                        <input type="email" class="form-control bg-light"
                               value="{{ auth()->user()->email }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase"
                               style="color:#6b7280; letter-spacing:.4px;">Rôle</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ ucfirst(str_replace('_', ' ', $role)) }}" readonly>
                    </div>
                    @if(auth()->user()->personnel?->prenom)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase"
                               style="color:#6b7280; letter-spacing:.4px;">Prénom</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ auth()->user()->personnel->prenom }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase"
                               style="color:#6b7280; letter-spacing:.4px;">Nom</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ auth()->user()->personnel->nom }}" readonly>
                    </div>
                    @endif
                    @if(auth()->user()->personnel?->telephone)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase"
                               style="color:#6b7280; letter-spacing:.4px;">Téléphone</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ auth()->user()->personnel->telephone }}" readonly>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- CAS 2 : ADMINISTRATEUR — formulaires complets         --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @else

        {{-- Formulaire 1 : Informations --}}
        <div class="card shadow-sm border-0 mb-4" style="border-top: 3px solid #2E75B6 !important;">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <div class="rounded d-flex align-items-center justify-content-center"
                     style="width:32px;height:32px;background:#e8f4fd">
                    <i class="bi bi-person-gear" style="color:#1F3864"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="color:#1F3864">Informations personnelles</div>
                    <small class="text-muted">Modifiez vos informations de connexion</small>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase"
                                   style="color:#555;letter-spacing:.4px">Nom affiché</label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase"
                                   style="color:#555;letter-spacing:.4px">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-envelope text-muted"></i>
                                </span>
                                <input type="email" name="email"
                                       class="form-control border-start-0 @error('email') is-invalid @enderror"
                                       value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase"
                                   style="color:#555;letter-spacing:.4px">
                                Matricule
                                <span class="badge bg-light text-muted fw-normal ms-1"
                                      style="font-size:10px">Non modifiable</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-upc text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 bg-light"
                                       value="{{ auth()->user()->matricule }}" readonly>
                            </div>
                        </div>

                        @if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                            && ! auth()->user()->hasVerifiedEmail())
                        <div class="col-12">
                            <div class="alert alert-warning py-2 mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Votre adresse email n'est pas vérifiée.
                                <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm p-0 ms-1">
                                        Renvoyer l'email de vérification
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Formulaire 2 : Mot de passe --}}
        <div class="card shadow-sm border-0 mb-4" style="border-top: 3px solid #198754 !important;">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <div class="rounded d-flex align-items-center justify-content-center"
                     style="width:32px;height:32px;background:#d1fadf">
                    <i class="bi bi-shield-lock" style="color:#198754"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="color:#198754">Sécurité — Mot de passe</div>
                    <small class="text-muted">Choisissez un mot de passe fort d'au moins 8 caractères</small>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase"
                                   style="color:#555;letter-spacing:.4px">Mot de passe actuel</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input type="password" name="current_password" id="currentMdp"
                                       class="form-control border-start-0 border-end-0
                                              @error('current_password','updatePassword') is-invalid @enderror"
                                       autocomplete="current-password">
                                <button type="button"
                                        class="btn btn-outline-secondary border-start-0"
                                        onclick="togglePwd('currentMdp','eyeCurrent')">
                                    <i class="bi bi-eye" id="eyeCurrent"></i>
                                </button>
                                @error('current_password','updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase"
                                   style="color:#555;letter-spacing:.4px">Nouveau mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock-fill text-muted"></i>
                                </span>
                                <input type="password" name="password" id="newMdp"
                                       class="form-control border-start-0 border-end-0
                                              @error('password','updatePassword') is-invalid @enderror"
                                       autocomplete="new-password"
                                       oninput="checkStrength(this.value)">
                                <button type="button"
                                        class="btn btn-outline-secondary border-start-0"
                                        onclick="togglePwd('newMdp','eyeNew')">
                                    <i class="bi bi-eye" id="eyeNew"></i>
                                </button>
                                @error('password','updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mt-2">
                                <div class="progress" style="height:4px;border-radius:4px">
                                    <div id="strengthBar" class="progress-bar"
                                         style="width:0%;transition:all .3s"></div>
                                </div>
                                <small id="strengthLabel" class="text-muted"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase"
                                   style="color:#555;letter-spacing:.4px">Confirmer le nouveau mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock-fill text-muted"></i>
                                </span>
                                <input type="password" name="password_confirmation" id="confirmMdp"
                                       class="form-control border-start-0 border-end-0"
                                       autocomplete="new-password">
                                <button type="button"
                                        class="btn btn-outline-secondary border-start-0"
                                        onclick="togglePwd('confirmMdp','eyeConfirm')">
                                    <i class="bi bi-eye" id="eyeConfirm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-shield-check me-2"></i>Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Formulaire 3 : Zone dangereuse --}}
        <div class="card shadow-sm border-0" style="border-top: 3px solid #dc3545 !important;">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <div class="rounded d-flex align-items-center justify-content-center"
                     style="width:32px;height:32px;background:#ffe0e0">
                    <i class="bi bi-exclamation-triangle" style="color:#dc3545"></i>
                </div>
                <div>
                    <div class="fw-semibold text-danger">Zone dangereuse</div>
                    <small class="text-muted">La suppression du compte est irréversible</small>
                </div>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    Une fois votre compte supprimé, toutes vos données et réservations seront
                    définitivement supprimées. Cette action ne peut pas être annulée.
                </p>
                <button type="button" class="btn btn-outline-danger btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalSupprimerCompte">
                    <i class="bi bi-trash me-2"></i>Supprimer mon compte
                </button>
            </div>
        </div>

        @endif {{-- fin @if(!hasRole administrateur) --}}

    </div>
</div>

{{-- ── MODAL suppression — visible uniquement pour l'admin ── --}}
@if(auth()->user()->hasRole('administrateur'))
<div class="modal fade" id="modalSupprimerCompte" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0 bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-octagon me-2"></i>Confirmer la suppression
                    </h5>
                    <button type="button" class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-danger border-0 mb-4">
                        <strong>Attention !</strong> Cette action est irréversible.
                        Toutes vos données seront définitivement supprimées.
                    </div>
                    <label class="form-label fw-semibold">
                        Confirmez votre mot de passe pour continuer
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-lock text-muted"></i>
                        </span>
                        <input type="password" name="password"
                               class="form-control border-start-0
                                      @error('password','userDeletion') is-invalid @enderror"
                               placeholder="Votre mot de passe actuel"
                               autocomplete="current-password">
                        @error('password','userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-trash me-2"></i>Supprimer définitivement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type     = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type     = 'password';
        icon.className = 'bi bi-eye';
    }
}

function checkStrength(val) {
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    if (!val) { bar.style.width = '0%'; label.textContent = ''; return; }
    let score = 0;
    if (val.length >= 8)           score++;
    if (val.length >= 12)          score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val))  score++;
    const levels = [
        { width:'20%',  color:'#dc3545', text:'Très faible' },
        { width:'40%',  color:'#fd7e14', text:'Faible'      },
        { width:'60%',  color:'#ffc107', text:'Moyen'       },
        { width:'80%',  color:'#2E75B6', text:'Fort'        },
        { width:'100%', color:'#198754', text:'Très fort'   },
    ];
    const l = levels[Math.min(score - 1, 4)] || levels[0];
    bar.style.width           = l.width;
    bar.style.backgroundColor = l.color;
    label.textContent         = l.text;
    label.style.color         = l.color;
}

@if(auth()->user()->hasRole('administrateur') && $errors->userDeletion->isNotEmpty())
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalSupprimerCompte')).show();
    });
@endif
</script>
@endpush

@endsection