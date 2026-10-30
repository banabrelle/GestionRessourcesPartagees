@if ($paginator->hasPages())
<div style="display:flex; align-items:center; justify-content:space-between; gap:.5rem; padding:.4rem 0;">

    <span style="font-size:.75rem; color:#6b7280; white-space:nowrap;">
        {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }}
        sur {{ $paginator->total() }} résultat(s)
    </span>

    <div style="display:flex; align-items:center; gap:.25rem;">

        {{-- Précédent --}}
        @if ($paginator->onFirstPage())
            <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;border:1px solid #e4e7ec;color:#d1d5db;font-size:.8rem;cursor:default;user-select:none;">&#8249;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;border:1px solid #cdd2db;color:#6b7280;font-size:.8rem;text-decoration:none;transition:all .15s;"
               onmouseover="this.style.background='#e8edf6';this.style.color='#1F3864';this.style.borderColor='#1F3864';"
               onmouseout="this.style.background='';this.style.color='#6b7280';this.style.borderColor='#cdd2db';">
                &#8249;
            </a>
        @endif

        {{-- Numéros de page --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;font-size:.75rem;color:#9ca3af;">&hellip;</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;border:1px solid #1F3864;background:#1F3864;color:#fff;font-size:.75rem;font-weight:600;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;border:1px solid #e4e7ec;color:#6b7280;font-size:.75rem;text-decoration:none;transition:all .15s;"
                           onmouseover="this.style.background='#e8edf6';this.style.color='#1F3864';this.style.borderColor='#1F3864';"
                           onmouseout="this.style.background='';this.style.color='#6b7280';this.style.borderColor='#e4e7ec';">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Suivant --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;border:1px solid #cdd2db;color:#6b7280;font-size:.8rem;text-decoration:none;transition:all .15s;"
               onmouseover="this.style.background='#e8edf6';this.style.color='#1F3864';this.style.borderColor='#1F3864';"
               onmouseout="this.style.background='';this.style.color='#6b7280';this.style.borderColor='#cdd2db';">
                &#8250;
            </a>
        @else
            <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;border:1px solid #e4e7ec;color:#d1d5db;font-size:.8rem;cursor:default;user-select:none;">&#8250;</span>
        @endif

    </div>
</div>
@endif