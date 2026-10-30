<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
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

.header {
    border: 2px solid #1F3864;
    border-radius: 4px;
    padding: 8px 10px 6px;
    margin-bottom: 8px;
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

.separator { border: none; border-top: 1px dashed #1F3864; margin: 5px 0 4px; }

.titre-doc {
    text-align: center;
    margin: 6px 0 4px;
    padding: 5px;
    background: #1F3864;
    border-radius: 3px;
}

.titre-doc h2 { color: #fff; font-size: 12px; font-weight: bold; margin-bottom: 2px; text-transform: uppercase; }
.titre-doc h3 { color: #BDE0FE; font-size: 9px; font-weight: normal; }

/* Badge division en évidence */
.division-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #dff3f7;
    border: 1px solid #2094a8;
    border-left: 5px solid #1F3864;
    border-radius: 4px;
    padding: 7px 12px;
    margin-bottom: 8px;
}

.division-badge .code {
    font-size: 22px;
    font-weight: 800;
    color: #1F3864;
    line-height: 1;
}

.division-badge .info { flex: 1; }
.division-badge .info .nom { font-size: 10px; font-weight: bold; color: #1F3864; }
.division-badge .info .sub { font-size: 8px; color: #555; margin-top: 1px; }

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

.stat-value { font-size: 22px; font-weight: 800; color: #1F3864; line-height: 1; margin-bottom: 2px; }
.stat-label { font-size: 7.5px; color: #666; text-transform: uppercase; }
.stat-sub   { font-size: 7px; color: #999; margin-top: 2px; }

.section-title {
    font-size: 9.5px;
    font-weight: bold;
    color: #1F3864;
    border-bottom: 2px solid #1F3864;
    padding-bottom: 3px;
    margin: 8px 0 5px;
    text-transform: uppercase;
}

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
}

thead th.center { text-align: center; }
thead th.right  { text-align: right; }

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

.progress-wrap {
    background: #e5e7eb;
    border-radius: 10px;
    height: 6px;
    width: 50px;
    display: inline-block;
    vertical-align: middle;
}

.progress-fill {
    height: 6px;
    border-radius: 10px;
    background: linear-gradient(90deg, #1F3864, #2094a8);
    display: block;
}

.two-col { display: flex; gap: 8px; margin-bottom: 8px; }
.two-col > div { flex: 1; }

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

{{-- ══ ENTÊTE ══ --}}
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

{{-- ══ TITRE ══ --}}
<div class="titre-doc">
    <h2>GERES-CENADI &mdash; RAPPORT STATISTIQUES PAR DIVISION</h2>
    <h3>Données filtrées — Division concernée uniquement</h3>
</div>

{{-- ══ BADGE DIVISION ══ --}}
<div class="division-badge">
    <div class="code">{{ $division->code }}</div>
    <div class="info">
        <div class="nom">{{ $division->nom }}</div>
        <div class="sub">Rapport de division &mdash; {{ $stats['total_personnels'] }} agent(s) concerné(s)</div>
    </div>
    <div style="text-align:right;font-size:8px;color:#555;">
        <strong>{{ $date_export }}</strong><br>
        Par : {{ $genere_par }}
    </div>
</div>

{{-- ══ CARTES STATS ══ --}}
<div class="section-title">&#128202; Vue d'ensemble de la division</div>

<div class="stats-grid">
    <div class="stat-box green">
        <div class="stat-value">{{ $stats['total_ressources'] }}</div>
        <div class="stat-label">Ressources</div>
        <div class="stat-sub">{{ $stats['disponibles'] }} dispo.</div>
    </div>
    <div class="stat-box orange">
        <div class="stat-value">{{ $stats['total_reservations'] }}</div>
        <div class="stat-label">Réservations</div>
        <div class="stat-sub">{{ $stats['en_attente'] }} attente</div>
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
        <div class="stat-sub">&lt; 30 jours</div>
    </div>
    <div class="stat-box purple">
        <div class="stat-value">{{ $stats['total_personnels'] }}</div>
        <div class="stat-label">Personnels</div>
        <div class="stat-sub">{{ $stats['rejetes'] }} rés. rejetées</div>
    </div>
</div>

{{-- ══ DOUBLE COLONNE ══ --}}
<div class="two-col">

    {{-- Réservations --}}
    <div>
        <div class="section-title">&#128197; Réservations</div>
        <table>
            <thead>
                <tr><th>Statut</th><th class="center">Nb</th><th class="center">%</th></tr>
            </thead>
            <tbody>
                @php $totalRes = max($stats['total_reservations'], 1); @endphp
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

    {{-- Ressources --}}
    <div>
        <div class="section-title">&#128230; Ressources</div>
        <table>
            <thead>
                <tr><th>État</th><th class="center">Nb</th><th class="center">%</th></tr>
            </thead>
            <tbody>
                @php $totalRess = max($stats['total_ressources'], 1); @endphp
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

{{-- ══ RESSOURCES DE LA DIVISION ══ --}}
@if(isset($ressourcesDivision) && $ressourcesDivision->count() > 0)
<div class="section-title">&#128230; Liste des ressources de la division</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Désignation</th>
            <th class="center">Type</th>
            <th>N° Série</th>
            <th class="center">Statut</th>
            <th>Infos</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ressourcesDivision as $i => $res)
        <tr>
            <td class="center" style="color:#999;">{{ $i + 1 }}</td>
            <td><strong>{{ $res->nom }}</strong></td>
            <td class="center">
                <span class="badge {{ $res->type_ressource === 'materiel' ? 'badge-blue' : 'badge-gray' }}">
                    {{ ucfirst($res->type_ressource) }}
                </span>
            </td>
            <td style="color:#666;">{{ $res->numero_serie ?? '—' }}</td>
            <td class="center">
                @php
                    $badgeEtat = match($res->etat) {
                        'disponible'            => 'badge-green',
                        'occupe'                => 'badge-red',
                        'en_maintenance'        => 'badge-gray',
                        'en_attente_validation' => 'badge-orange',
                        default                 => 'badge-gray',
                    };
                @endphp
                <span class="badge {{ $badgeEtat }}">
                    {{ ucfirst(str_replace('_', ' ', $res->etat)) }}
                </span>
            </td>
            <td style="color:#666;font-size:7.5px;">
                @if($res->ficheMaterielle)
                    {{ $res->ficheMaterielle->marque }} {{ $res->ficheMaterielle->modele }}
                @elseif($res->ficheLogicielle)
                    {{ $res->ficheLogicielle->editeur }} v{{ $res->ficheLogicielle->version }}
                @else
                    —
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ══ LICENCES EN ALERTE ══ --}}
@if(isset($licencesAlerte) && $licencesAlerte->count() > 0)
<div class="section-title">&#9888; Licences en alerte dans cette division</div>
<table>
    <thead>
        <tr>
            <th>Logiciel</th>
            <th>Éditeur</th>
            <th class="center">Expiration</th>
            <th class="center">Jours restants</th>
        </tr>
    </thead>
    <tbody>
        @foreach($licencesAlerte as $lic)
        @php $joursRestants = now()->diffInDays($lic->date_fin_licence, false); @endphp
        <tr>
            <td><strong>{{ $lic->ressource?->nom }}</strong></td>
            <td>{{ $lic->editeur }}</td>
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
    Rapport division {{ $division->code }} — GERES-CENADI &mdash; MINFI / Cameroun &copy; {{ date('Y') }} —
    Généré le {{ $date_export }} par {{ $genere_par }}
</div>

</body>
</html>