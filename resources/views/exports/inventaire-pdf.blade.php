<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body{font-family:Arial,sans-serif;font-size:9px}
        .header{border-bottom:2px solid #1F3864;margin-bottom:12px;padding-bottom:8px}
        .bi-header{display:flex;justify-content:space-between;font-size:9px;margin-bottom:6px}
        table{width:100%;border-collapse:collapse;margin-top:8px}
        th{background:#1F3864;color:#fff;padding:5px 4px;text-align:left;font-size:8px}
        td{padding:4px;border-bottom:1px solid #eee;font-size:8px}
        tr:nth-child(even){background:#f9f9f9}
        .badge-m{background:#0d6efd;color:#fff;padding:2px 5px;border-radius:3px}
        .badge-l{background:#0dcaf0;color:#000;padding:2px 5px;border-radius:3px}
        .badge-ok{background:#198754;color:#fff;padding:2px 5px;border-radius:3px}
        .badge-ko{background:#dc3545;color:#fff;padding:2px 5px;border-radius:3px}
        .footer{margin-top:12px;text-align:center;color:#999;font-size:8px;border-top:1px solid #eee;padding-top:5px}
    </style>
</head>

<body>

<!-- En-tête bilingue CENADI -->
<div class="header">
    <div class="bi-header">
        <div>
            <strong>REPUBLIQUE DU CAMEROUN</strong><br>
            Paix - Travail - Patrie<br>
            <strong>MINFI / CENADI</strong>
        </div>

        <div style="text-align:center">
            <div style="border:2px solid #1F3864;border-radius:50%;width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;color:#1F3864;font-size:9px;margin:auto">
                CENADI
            </div>
        </div>

        <div style="text-align:right">
            <strong>REPUBLIC OF CAMEROON</strong><br>
            Peace - Work - Fatherland<br>
            <strong>MINFI / CENADI</strong>
        </div>
    </div>

    <h3 style="color:#1F3864;margin:5px 0 2px;text-align:center">
        GERES-CENADI — INVENTAIRE DES RESSOURCES
    </h3>

    <div style="text-align:center">
        Généré le : {{ $date_export }} |
        Par : {{ $genere_par }} |
        Total : {{ $ressources->count() }} ressource(s)
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Désignation</th>
            <th>Type</th>
            <th>Division</th>
            <th>Localisation</th>
            <th>N° Série</th>
            <th>Statut</th>
            <th>Infos spécifiques</th>
        </tr>
    </thead>

    <tbody>
        @foreach($ressources as $r)
        <tr>
            <td>{{ $r->id }}</td>

            <td>
                <strong>{{ $r->nom }}</strong>
            </td>

            <td>
                <span class="{{ $r->type_ressource === 'materiel' ? 'badge-m' : 'badge-l' }}">
                    {{ ucfirst($r->type_ressource) }}
                </span>
            </td>

            <td>{{ $r->division?->code ?? '—' }}</td>
            <td>{{ $r->localisation ?? '—' }}</td>
            <td><small>{{ $r->numero_serie ?? '—' }}</small></td>

            <td>
                @php
                    $cls = $r->etat === 'disponible' ? 'badge-ok' : 'badge-ko';
                @endphp

                <span class="{{ $cls }}">
                    {{ ucfirst(str_replace('_',' ',$r->etat)) }}
                </span>
            </td>

            <td>
                @if($r->ficheMaterielle)
                    {{ $r->ficheMaterielle->marque }}
                    {{ $r->ficheMaterielle->modele }}

                @elseif($r->ficheLogicielle)
                    {{ $r->ficheLogicielle->editeur }} —
                    {{ $r->ficheLogicielle->cles_utilisees }}/{{ $r->ficheLogicielle->nombre_licence }} lic.
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Inventaire officiel GERES-CENADI — MINFI Cameroun &copy; {{ date('Y') }}
</div>

</body>
</html>