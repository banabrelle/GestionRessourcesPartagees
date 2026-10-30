<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  /* ══ FORMAT A4 STRICT ══ */
  @page {
    size: A4 portrait;
    margin: 12mm 14mm 12mm 14mm;
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  html, body {
    width: 210mm;
    /* hauteur A4 moins marges haut+bas = 297 - 24 = 273mm */
    height: 273mm;
    overflow: hidden;
    font-family: Arial, sans-serif;
    font-size: 9px;
    color: #333;
  }

  /* ══ MISE EN PAGE FLEX VERTICALE — tout tient dans 273mm ══ */
  .page {
    display: flex;
    flex-direction: column;
    height: 273mm;
    gap: 5px;
  }

  /* ══ EN-TÊTE ══ */
  .header {
    border-bottom: 3px double #1F3864;
    padding-bottom: 7px;
    flex-shrink: 0;
  }

  .bi-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    font-size: 8.5px;
    line-height: 1.4;
  }

  .bi-header .left,
  .bi-header .right { width: 38%; }
  .bi-header .center { width: 20%; text-align: center; }

  .logo-box {
    border: 2px solid #1F3864;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: bold;
    color: #1F3864;
  }

  .titre-doc { text-align: center; margin-top: 6px; }
  .titre-doc h2 { color: #1F3864; font-size: 12px; margin-bottom: 2px; }
  .titre-doc h3 { color: #2E75B6; font-size: 10px; margin-bottom: 4px; }

  .numero-bon {
    display: inline-block;
    background: #1F3864;
    color: #fff;
    padding: 3px 12px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
  }

  /* ══ SECTIONS — flex-grow pour remplir la page sans déborder ══ */
  .sections-wrapper {
    flex: 1 1 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-height: 0;
  }

  .section {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 6px 8px;
    flex-shrink: 0;
  }

  .section h4 {
    color: #1F3864;
    font-size: 9.5px;
    border-bottom: 1px solid #eee;
    padding-bottom: 3px;
    margin-bottom: 5px;
  }

  .row-info {
    display: flex;
    margin-bottom: 3px;
    line-height: 1.3;
  }

  .row-info .label {
    width: 150px;
    min-width: 150px;
    font-weight: bold;
    color: #555;
  }

  /* ══ SIGNATURES ══ */
  .signatures {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
  }

  .sign-col { flex: 1; }

  .sign-label {
    font-size: 8.5px;
    font-weight: bold;
    display: block;
    margin-bottom: 3px;
  }

  .sign-zone {
    border: 1px solid #aaa;
    border-radius: 4px;
    height: 55px;
    margin-bottom: 3px;
  }

  .sign-name { font-size: 8px; color: #555; line-height: 1.3; }

  /* ══ PIED DE PAGE ══ */
  .footer {
    text-align: center;
    color: #999;
    font-size: 7px;
    border-top: 1px solid #eee;
    padding-top: 4px;
    flex-shrink: 0;
  }
</style>
</head>
<body>
<div class="page">

  <!-- ══ EN-TÊTE BILINGUE CENADI ══ -->
  <div class="header">
    <div class="bi-header">
      <div class="left">
        <strong>REPUBLIQUE DU CAMEROUN</strong><br>Paix — Travail — Patrie<br><br>
        <strong>MINISTERE DES FINANCES</strong><br>
        <strong>SECRETARIAT GENERAL</strong><br>
        <strong>CENTRE NATIONAL DE DEVELOPPEMENT<br>DE L'INFORMATIQUE</strong>
      </div>
      <div class="logo-circle mx-auto mb-3">
            <img src="{{ asset('image/logo.png') }}"
                alt="Logo"
                style="width:45px;">
      </div>
      <div class="right" style="text-align:right">
        <strong>REPUBLIC OF CAMEROON</strong><br>Peace — Work — Fatherland<br><br>
        <strong>MINISTRY OF FINANCE</strong><br>
        <strong>GENERAL SECRETARIAT</strong><br>
        <strong>NATIONAL CENTRE FOR THE DEVELOPMENT<br>OF COMPUTER SERVICES</strong>
      </div>
    </div>
    <div class="titre-doc">
      <h2>PLATEFORME GERES-CENADI</h2>
      <h3>BON D'EMPRUNT DE RESSOURCE / EQUIPMENT BORROWING SLIP</h3>
      <div style="margin-top:4px">
        <span class="numero-bon">N° {{ $bon->numero_bon }}</span>
        <span style="margin-left:12px;font-size:8.5px">Émis le : {{ $bon->created_at->format('d/m/Y à H:i') }}</span>
      </div>
    </div>
  </div>

  <!-- ══ CORPS ══ -->
  <div class="sections-wrapper">

    <!-- Emprunteur -->
    <div class="section">
      <h4>&#128100; Informations de l'Emprunteur</h4>
      <div class="row-info"><span class="label">Nom complet :</span><span>{{ $bon->reservation->personnel->prenom }} {{ $bon->reservation->personnel->nom }}</span></div>
      <div class="row-info"><span class="label">Matricule :</span><span>{{ $bon->reservation->personnel->matricule }}</span></div>
      <div class="row-info"><span class="label">Division :</span><span>{{ $bon->reservation->personnel->division?->nom }} ({{ $bon->reservation->personnel->division?->code }})</span></div>
      <div class="row-info"><span class="label">Email :</span><span>{{ $bon->reservation->personnel->email }}</span></div>
    </div>

    <!-- Ressource -->
    <div class="section">
      <h4>&#128230; Ressource Empruntée</h4>
      <div class="row-info"><span class="label">Désignation :</span><span>{{ $bon->reservation->ressource->nom }}</span></div>
      <div class="row-info"><span class="label">Type :</span><span>{{ ucfirst($bon->reservation->ressource->type_ressource) }}</span></div>
      <div class="row-info"><span class="label">N° Série :</span><span>{{ $bon->reservation->ressource->numero_serie ?? '—' }}</span></div>
      @if($bon->reservation->ressource->ficheMaterielle)
      <div class="row-info"><span class="label">Marque / Modèle :</span><span>{{ $bon->reservation->ressource->ficheMaterielle->marque }} {{ $bon->reservation->ressource->ficheMaterielle->modele }}</span></div>
      @endif
      {{-- <div class="row-info"><span class="label">Localisation :</span><span>{{ $bon->reservation->ressource->localisation ?? '—' }}</span></div> --}}
    </div>

    <!-- Période -->
    <div class="section">
      <h4>&#128197; Période d'utilisation</h4>
      <div class="row-info"><span class="label">Date de début :</span><span>{{ $bon->reservation->date_debut->format('d/m/Y à H:i') }}</span></div>
      <div class="row-info"><span class="label">Date de fin :</span><span>{{ $bon->reservation->date_fin->format('d/m/Y à H:i') }}</span></div>
      <div class="row-info"><span class="label">Durée :</span><span>{{ $bon->reservation->duree_heures }} heure(s)</span></div>
      <div class="row-info"><span class="label">Retour prévu :</span><span>{{ $bon->date_retour_prevue?->format('d/m/Y à H:i') ?? '—' }}</span></div>
      <div class="row-info"><span class="label">Motif :</span><span>{{ $bon->reservation->motif }}</span></div>
    </div>

    <!-- Signatures -->
    <div class="signatures">
      <div class="sign-col">
        <span class="sign-label">Signature de l'Emprunteur</span>
        <div class="sign-zone"></div>
        <div class="sign-name">{{ $bon->reservation->personnel->prenom }} {{ $bon->reservation->personnel->nom }}<br></div>
      </div>
      <div class="sign-col">
        <span class="sign-label">Visa Chef de Division</span>
        <div class="sign-zone"></div>
        <div class="sign-name">{{ $bon->reservation->validePar?->prenom }} {{ $bon->reservation->validePar?->nom ?? '...' }}</div>
      </div>
      <div class="sign-col">
        <span class="sign-label">Validation Service Patrimoine / Admin</span>
        <div class="sign-zone"></div>
        <div class="sign-name">{{ $bon->reservation->valideParAdmin?->prenom }} {{ $bon->reservation->valideParAdmin?->nom ?? '...' }}</div>
      </div>
    </div>

  </div><!-- /sections-wrapper -->

  <!-- ══ PIED DE PAGE ══ -->
  <div class="footer">
    Ce bon est un document officiel CENADI. Toute falsification est passible de sanctions disciplinaires.<br>
    GERES-CENADI — MINFI / Cameroun &copy; {{ date('Y') }} — Document généré automatiquement
  </div>

</div><!-- /page -->
</body>
</html>