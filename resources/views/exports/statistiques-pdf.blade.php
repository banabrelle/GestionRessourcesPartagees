<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
/* ══ FORMAT A4 STRICT ══ */
@page {
    size: A4 portrait;
    margin: 10mm 12mm 10mm 12mm;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
    width: 186mm;
    font-family: Arial, sans-serif;
    font-size: 9px;
    color: #222;
}

/* ══ ENTÊTE BILINGUE ══ */
.header {
    border: 2px solid #1F3864;
    border-radius: 4px;
    padding: 8px 10px 6px;
    margin-bottom: 8px;
    position: relative;
}

.bi-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.bi-header .left,
.bi-header .right {
    width: 38%;
    font-size: 8px;
    line-height: 1.5;
    color: #1F3864;
}

.bi-header .right { text-align: right; }

.bi-header .center {
    width: 22%;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

/* Logo dans un cercle */
.logo-circle {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    border: 2px solid #1F3864;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #fff;
    margin: 0 auto;
}

.logo-circle img {
    width: 38px;
    height: 38px;
    object-fit: contain;
}

.separator {
    border: none;
    border-top: 1px dashed #1F3864;
    margin: 5px 0 4px;
}

/* ══ TITRE DU DOCUMENT ══ */
.titre-doc {
    text-align: center;
    margin: 6px 0 4px;
    padding: 5px;
    background: #1F3864;
    border-radius: 3px;
}

.titre-doc h2 {
    color: #fff;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.titre-doc h3 {
    color: #BDE0FE;
    font-size: 9px;
    font-weight: normal;
}

/* ══ META INFO ══ */
.meta-bar {
    display: flex;
    justify-content: space-between;
    background: #f0f4f8;
    border: 1px solid #ddd;
    border-radius: 3px;
    padding: 5px 10px;
    margin-bottom: 8px;
    font-size: 8px;
    color: #555;
}

/* ══ CARTES STATS ══ */
.stats-grid {
    display: flex;
    gap: 5px;
    margin-bottom: 8px;
}

.stat-box {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 7px 5px;
    text-align: center;
}

.stat-box.green  { border-top: 3px solid #1f9d73; background: #f0fdf6; }
.stat-box.orange { border-top: 3px solid #f59e0b; background: #fffbf0; }
.stat-box.blue   { border-top: 3px solid #2094a8; background: #f0fafc; }
.stat-box.red    { border-top: 3px solid #ef4444; background: #fff5f5; }
.stat-box.purple { border-top: 3px solid #8b5cf6; background: #faf5ff; }
.stat-box.gray   { border-top: 3px solid #6b7280; background: #f9fafb; }

.stat-value {
    font-size: 22px;
    font-weight: 800;
    color: #1F3864;
    line-height: 1;
    margin-bottom: 2px;
}

.stat-label {
    font-size: 7.5px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.stat-sub {
    font-size: 7px;
    color: #999;
    margin-top: 2px;
}

/* ══ SECTION TITRE ══ */
.section-title {
    font-size: 9.5px;
    font-weight: bold;
    color: #1F3864;
    border-bottom: 2px solid #1F3864;
    padding-bottom: 3px;
    margin: 8px 0 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ══ TABLEAU ══ */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8px;
    margin-bottom: 8px;
}

thead th {
    background: #1F3864;
    color: #fff;
    padding: 5px 6px;
    text-align: left;
    font-size: 7.5px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

thead th.center { text-align: center; }

tbody td {
    padding: 4px 6px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

tbody tr:nth-child(even) { background: #f8fafc; }

tbody td.center { text-align: center; }
tbody td.right  { text-align: right; }

.badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 7px;
    font-weight: bold;
}

.badge-green  { background: #dff6eb; color: #14915f; }
.badge-orange { background: #fff1d9; color: #d48a09; }
.badge-red    { background: #ffe3e3; color: #d93838; }
.badge-blue   { background: #dff3f7; color: #1e7f92; }
.badge-gray   { background: #f3f4f6; color: #6b7280; }

/* ══ BARRE DE PROGRESSION ══ */
.progress-wrap {
    background: #e5e7eb;
    border-radius: 10px;
    height: 6px;
    width: 60px;
    display: inline-block;
    vertical-align: middle;
}

.progress-fill {
    height: 6px;
    border-radius: 10px;
    background: linear-gradient(90deg, #1F3864, #2094a8);
    display: block;
}

/* ══ DOUBLE COLONNE ══ */
.two-col {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
}

.two-col > div { flex: 1; }

/* ══ LISTE ══ */
.list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 6px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 8px;
}

.list-item:last-child { border-bottom: none; }
.list-item .name { color: #333; font-weight: 600; }
.list-item .val  { color: #1F3864; font-weight: bold; }

/* ══ PIED DE PAGE ══ */
.footer {
    margin-top: 8px;
    text-align: center;
    font-size: 7px;
    color: #999;
    border-top: 1px solid #ddd;
    padding-top: 5px;
}
</style>
</head>
<body>

{{-- ══ ENTÊTE BILINGUE CENADI ══ --}}
<div class="header">
    <div class="bi-header">
        <div class="left">
            <strong>REPUBLIQUE DU CAMEROUN</strong><br>
            Paix &mdash; Travail &mdash; Patrie<br>
            <hr class="separator">
            <strong>MINISTERE DES FINANCES</strong><br>
            <strong>SECRETARIAT GENERAL</strong><br>
            <strong>CENTRE NATIONAL DE DEVELOPPEMENT<br>DE L'INFORMATIQUE</strong>
        </div>
        <div class="center">
            <div class="logo-circle">
                <img src="{{ public_path('image/logo.png') }}" alt="CENADI">
            </div>
            <div style="font-size:7px;color:#1F3864;font-weight:bold;margin-top:2px;">CENADI</div>
        </div>
        <div class="right">
            <strong>REPUBLIC OF CAMEROON</strong><br>
            Peace &mdash; Work &mdash; Fatherland<br>
            <hr class="separator">
            <strong>MINISTRY OF FINANCE</strong><br>
            <strong>GENERAL SECRETARIAT</strong><br>
            <strong>NATIONAL CENTRE FOR THE<br>DEVELOPMENT OF COMPUTER SERVICES</strong>
        </div>
    </div>
</div>

{{-- ══ TITRE DOCUMENT ══ --}}
<div class="titre-doc">
    <h2>GERES-CENADI &mdash; RAPPORT STATISTIQUES GLOBAL</h2>
    <h3>Tableau de bord complet — Toutes divisions confondues</h3>
</div>

{{-- ══ META ══ --}}
<div class="meta-bar">
    <span>&#128197; Généré le : <strong>{{ $date_export }}</strong></span>
    <span>&#128100; Par : <strong>{{ $genere_par }}</strong></span>
    <span>&#128202; Période : <strong>Données en temps réel</strong></span>
</div>

{{-- ══ CARTES STATISTIQUES PRINCIPALES ══ --}}
<div class="section-title">&#128202; Vue d'ensemble</div>

<div class="stats-grid">
    <div class="stat-box green">
        <div class="stat-value">{{ $stats['total_ressources'] }}</div>
        <div class="stat-label">Ressources</div>
        <div class="stat-sub">{{ $stats['disponibles'] }} dispo. / {{ $stats['occupees'] }} occ.</div>
    </div>
    <div class="stat-box orange">
        <div class="stat-value">{{ $stats['total_reservations'] }}</div>
        <div class="stat-label">Réservations</div>
        <div class="stat-sub">{{ $stats['en_attente'] }} en attente</div>
    </div>
    <div class="stat-box blue">
        <div class="stat-value">{{ $stats['taux_utilisation'] }}%</div>
        <div class="stat-label">Taux utilisation</div>
        <div class="stat-sub">
            <div class="progress-wrap">
                <span class="progress-fill" style="width:{{ $stats['taux_utilisation'] }}%"></span>
            </div>
        </div>
    </div>
    <div class="stat-box red">
        <div class="stat-value">{{ $stats['licences_alerte'] }}</div>
        <div class="stat-label">Licences alerte</div>
        <div class="stat-sub">Expiration &lt; 30 jours</div>
    </div>
    <div class="stat-box purple">
        <div class="stat-value">{{ $stats['total_personnels'] }}</div>
        <div class="stat-label">Personnels</div>
        <div class="stat-sub">{{ $stats['total_divisions'] }} division(s)</div>
    </div>
    <div class="stat-box gray">
        <div class="stat-value">{{ $stats['conflits'] }}</div>
        <div class="stat-label">Conflits IA</div>
        <div class="stat-sub">détectés</div>
    </div>
</div>

{{-- ══ DOUBLE COLONNE : RÉSERVATIONS + RESSOURCES ══ --}}
<div class="two-col">

    {{-- Détail réservations --}}
    <div>
        <div class="section-title">&#128197; Détail réservations</div>
        <table>
            <thead>
                <tr>
                    <th>Statut</th>
                    <th class="center">Nombre</th>
                    <th class="center">Part</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalRes = max($stats['total_reservations'], 1);
                @endphp
                <tr>
                    <td><span class="badge badge-orange">En attente</span></td>
                    <td class="center">{{ $stats['en_attente'] }}</td>
                    <td class="center">{{ round($stats['en_attente'] / $totalRes * 100) }}%</td>
                </tr>
                <tr>
                    <td><span class="badge badge-blue">Validé chef</span></td>
                    <td class="center">{{ $stats['valide_chef'] }}</td>
                    <td class="center">{{ round($stats['valide_chef'] / $totalRes * 100) }}%</td>
                </tr>
                <tr>
                    <td><span class="badge badge-green">Validé admin</span></td>
                    <td class="center">{{ $stats['valide_admin'] }}</td>
                    <td class="center">{{ round($stats['valide_admin'] / $totalRes * 100) }}%</td>
                </tr>
                <tr>
                    <td><span class="badge badge-red">Rejetées</span></td>
                    <td class="center">{{ $stats['rejetes'] }}</td>
                    <td class="center">{{ round($stats['rejetes'] / $totalRes * 100) }}%</td>
                </tr>
                <tr style="background:#f0f4f8;font-weight:bold;">
                    <td>Total</td>
                    <td class="center">{{ $stats['total_reservations'] }}</td>
                    <td class="center">100%</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Détail ressources --}}
    <div>
        <div class="section-title">&#128230; Détail ressources</div>
        <table>
            <thead>
                <tr>
                    <th>État</th>
                    <th class="center">Nombre</th>
                    <th class="center">Part</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalRess = max($stats['total_ressources'], 1);
                @endphp
                <tr>
                    <td><span class="badge badge-green">Disponibles</span></td>
                    <td class="center">{{ $stats['disponibles'] }}</td>
                    <td class="center">{{ round($stats['disponibles'] / $totalRess * 100) }}%</td>
                </tr>
                <tr>
                    <td><span class="badge badge-red">Occupées</span></td>
                    <td class="center">{{ $stats['occupees'] }}</td>
                    <td class="center">{{ round($stats['occupees'] / $totalRess * 100) }}%</td>
                </tr>
                <tr>
                    <td><span class="badge badge-gray">Maintenance</span></td>
                    <td class="center">{{ $stats['en_maintenance'] }}</td>
                    <td class="center">{{ round($stats['en_maintenance'] / $totalRess * 100) }}%</td>
                </tr>
                <tr style="background:#f0f4f8;font-weight:bold;">
                    <td>Total</td>
                    <td class="center">{{ $stats['total_ressources'] }}</td>
                    <td class="center">100%</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

{{-- ══ TABLEAU PAR DIVISION ══ --}}
@if(isset($statsDivisions) && $statsDivisions->count() > 0)
<div class="section-title">&#127968; Statistiques par division</div>
<table>
    <thead>
        <tr>
            <th>Division</th>
            <th class="center">Ressources</th>
            <th class="center">Disponibles</th>
            <th class="center">Taux util.</th>
            <th class="center">Réservations</th>
            <th class="center">En attente</th>
            <th class="center">Personnels</th>
        </tr>
    </thead>
    <tbody>
        @foreach($statsDivisions as $div)
        @php
            $tauxDiv = $div->total_ressources > 0
                ? round(($div->ressources_occupees / $div->total_ressources) * 100)
                : 0;
        @endphp
        <tr>
            <td>
                <strong>{{ $div->code }}</strong>
                <span style="color:#999;font-size:7px;"> — {{ \Illuminate\Support\Str::limit($div->nom, 25) }}</span>
            </td>
            <td class="center">{{ $div->total_ressources }}</td>
            <td class="center">
                <span class="badge badge-green">{{ $div->ressources_disponibles }}</span>
            </td>
            <td class="center">
                {{ $tauxDiv }}%
                <div class="progress-wrap" style="width:40px;">
                    <span class="progress-fill" style="width:{{ $tauxDiv }}%"></span>
                </div>
            </td>
            <td class="center">{{ $div->total_reservations }}</td>
            <td class="center">
                @if($div->reservations_attente > 0)
                    <span class="badge badge-orange">{{ $div->reservations_attente }}</span>
                @else
                    <span style="color:#999">0</span>
                @endif
            </td>
            <td class="center">{{ $div->total_personnels }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ══ LICENCES EN ALERTE ══ --}}
@if(isset($licencesAlerte) && $licencesAlerte->count() > 0)
<div class="section-title">&#9888; Licences logicielles — Alertes expiration</div>
<table>
    <thead>
        <tr>
            <th>Logiciel</th>
            <th>Éditeur</th>
            <th class="center">Division</th>
            <th class="center">Expiration</th>
            <th class="center">Jours restants</th>
        </tr>
    </thead>
    <tbody>
        @foreach($licencesAlerte as $lic)
        @php
            $joursRestants = now()->diffInDays($lic->date_fin_licence, false);
        @endphp
        <tr>
            <td><strong>{{ $lic->ressource?->nom }}</strong></td>
            <td>{{ $lic->editeur }}</td>
            <td class="center">{{ $lic->ressource?->division?->code ?? '—' }}</td>
            <td class="center">{{ $lic->date_fin_licence?->format('d/m/Y') }}</td>
            <td class="center">
                <span class="badge {{ $joursRestants <= 7 ? 'badge-red' : 'badge-orange' }}">
                    {{ $joursRestants }}j
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ══ PIED DE PAGE ══ --}}
<div class="footer">
    Rapport officiel GERES-CENADI — MINFI / Cameroun &copy; {{ date('Y') }} —
    Document généré automatiquement le {{ $date_export }} par {{ $genere_par }}
</div>

</body>
</html>