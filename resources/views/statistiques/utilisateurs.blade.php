@extends('layouts.app')

@section('title','Stats Utilisateurs')

@section('content')

<h4 class="fw-bold mb-3" style="color:#1F3864">
    <i class="bi bi-people me-2"></i>
    Statistiques des Utilisateurs
</h4>

@include('statistiques._nav')

<div class="row g-3 mb-3">

    <!-- Utilisateurs actifs -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
                <i class="bi bi-person-check fs-1" style="color:#1F3864"></i>
                <div class="fs-2 fw-bold">{{ $actifs }}</div>
                <div class="text-muted">Utilisateurs actifs (30j)</div>
            </div>
        </div>
    </div>

    <!-- Top utilisateurs -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                Top 5 utilisateurs les plus actifs
            </div>
            <div class="card-body">

                @foreach($plusActifs as $idx => $per)
                <div class="d-flex align-items-center mb-2 {{ $idx < 3 ? 'fw-semibold' : '' }}">

                    <span class="badge bg-{{ 
                        $idx === 0 ? 'warning text-dark' : 
                        ($idx === 1 ? 'secondary' : 
                        ($idx === 2 ? 'info' : 'light text-dark')) 
                    }} me-2" style="width:24px">
                        {{ $idx + 1 }}
                    </span>

                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                         style="width:32px;height:32px;flex-shrink:0">
                        <span class="text-white" style="font-size:11px">
                            {{ $per->initiales }}
                        </span>
                    </div>

                    <div class="flex-grow-1">
                        <div>{{ $per->prenom }} {{ $per->nom }}</div>
                        <small class="text-muted">
                            {{ $per->division?->code ?? '—' }}
                        </small>
                    </div>

                    <span class="badge bg-primary">
                        {{ $per->reservations_count }} réservation(s)
                    </span>

                </div>
                @endforeach

            </div>
        </div>
    </div>

</div>

<!-- Historique -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-semibold">
        Historique d'emprunt par utilisateur
    </div>

    <div class="table-scroll">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Personnel</th>
                    <th>Matricule</th>
                    <th>Division</th>
                    <th>Nb réservations</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($historiques as $per)
                <tr>
                    <td>{{ $per->prenom }} {{ $per->nom }}</td>
                    <td><code>{{ $per->matricule }}</code></td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ $per->division?->code ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-primary">
                            {{ $per->reservations_count }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('personnels.show',$per) }}"
                           class="btn btn-outline-info btn-sm">
                           <i class="bi bi-eye"></i> Détail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-3 text-muted">
                        Aucune donnée
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-3 py-2 border-top">
        {{ $historiques->links() }}
    </div>
</div>

@endsection