<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>
body{font-family:Arial,sans-serif;font-size:9px}
.header{text-align:center;border-bottom:2px solid #1F3864;margin-bottom:12px;padding-bottom:8px}
.bi-header{display:flex;justify-content:space-between;font-size:9px;margin-bottom:6px}
table{width:100%;border-collapse:collapse;margin-top:8px}
th{background:#1F3864;color:#fff;padding:5px 4px;text-align:left;font-size:8px}
td{padding:4px;border-bottom:1px solid #eee;font-size:8px}
tr:nth-child(even){background:#f9f9f9}
.badge-valide{background:#198754;color:#fff;padding:2px 5px;border-radius:3px}
.badge-attente{background:#ffc107;color:#000;padding:2px 5px;border-radius:3px}
.badge-rejete{background:#dc3545;color:#fff;padding:2px 5px;border-radius:3px}
.footer{margin-top:12px;text-align:center;color:#999;font-size:8px;border-top:1px solid #eee;padding-top:5px}
</style></head><body>
<!-- En-tête bilingue CENADI -->
<div class="header">
  <div class="bi-header">
    <div><strong>REPUBLIQUE DU CAMEROUN</strong><br>Paix - Travail - Patrie<br><strong>MINFI / CENADI</strong></div>
    <div style="text-align:center"><div style="border:2px solid #1F3864;border-radius:50%;width:45px;height:45px;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;color:#1F3864;font-size:9px;margin:auto">CENADI</div></div>
    <div style="text-align:right"><strong>REPUBLIC OF CAMEROON</strong><br>Peace - Work - Fatherland<br><strong>MINFI / CENADI</strong></div>
  </div>
  <h3 style="color:#1F3864;margin:5px 0 2px">GERES-CENADI — HISTORIQUE DES RÉSERVATIONS</h3>
  <div>Période : {{ $periode }} | Généré le : {{ $date_export }} | Par : {{ $genere_par }}</div>
</div>
<table>
  <thead><tr><th>#</th><th>Personnel</th><th>Matricule</th><th>Division</th><th>Ressource</th><th>Début</th><th>Fin</th><th>Durée</th><th>Statut</th><th>Motif</th></tr></thead>
  <tbody>
    @foreach($reservations as $r)
    <tr>
      <td>{{ $r->id }}</td>
      <td>{{ $r->personnel->prenom }} {{ $r->personnel->nom }}</td>
      <td>{{ $r->personnel->matricule }}</td>
      <td>{{ $r->personnel->division?->code ?? '—' }}</td>
      <td>{{ Str::limit($r->ressource->nom,20) }}</td>
      <td>{{ $r->date_debut->format('d/m/Y H:i') }}</td>
      <td>{{ $r->date_fin->format('d/m/Y H:i') }}</td>
      <td>{{ $r->duree_heures }}h</td>
      <td>
        @php $cls=match(true){in_array($r->statut,['valide_admin','termine'])=>'badge-valide',in_array($r->statut,['rejete_chef','rejete_admin'])=>'badge-rejete',default=>'badge-attente'}; @endphp
        <span class="{{ $cls }}">{{ $r->statut_label }}</span>
      </td>
      <td>{{ Str::limit($r->motif,30) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
<div class="footer">
  Total : {{ $reservations->count() }} réservation(s) — Document officiel GERES-CENADI — MINFI Cameroun
</div>
</body></html>
