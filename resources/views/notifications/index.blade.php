@extends('layouts.app')
@section('title','Notifications')
@section('content')
<style>
  body{
    background-color: #E3F3FC;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold mb-0" style="color:#1F3864"><i class="bi bi-bell me-2"></i>Notifications</h4>
  <form method="POST" action="{{ route('notifications.lire-tout') }}">@csrf @method('PATCH')
    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-check-all me-1"></i>Tout marquer lu</button>
  </form>
</div>
<div class="card shadow-sm border-0"><div class="card-body p-0">
  <div class="table-scroll">
    @forelse($notifications as $n)
    <div class="d-flex align-items-start border-bottom px-3 py-3 {{ $n->lu ? '' : 'bg-light-blue' }}" style="{{ $n->lu ? '' : 'background:#f0f7ff;border-left:4px solid #2E75B6' }}">
      <div class="me-3 mt-1">
        @php $ico=['reservation_soumise'=>['bi-calendar-plus','text-warning'],'valide_chef'=>['bi-check-circle','text-info'],'valide_admin'=>['bi-check2-all','text-success'],'rejete'=>['bi-x-circle','text-danger'],'licence_expiration'=>['bi-key','text-warning']][$n->type]??['bi-bell','text-secondary']; @endphp
        <i class="bi {{ $ico[0] }} fs-4 {{ $ico[1] }}"></i>
      </div>
      <div class="flex-grow-1">
        <div class="fw-semibold">{{ $n->titre }}</div>
        <div class="text-muted small">{{ $n->message }}</div>
        <div class="text-muted" style="font-size:11px">{{ $n->created_at->diffForHumans() }}</div>
      </div>
      <div class="ms-2">
        @if(!$n->lu)
        <span class="badge bg-primary">Nouveau</span>
        @endif
        @if(!empty($n->data['reservation_id']))
        <a href="{{ route('reservations.show',$n->data['reservation_id']) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-right"></i></a>
        @endif
      </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted"><i class="bi bi-bell-slash fs-1 d-block mb-2"></i>Aucune notification</div>
    @endforelse
  </div>
  <div class="px-3 py-2 border-top">{{ $notifications->links() }}</div>
</div></div>
@endsection
