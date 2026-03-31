@if ($paginator->hasPages())
<div style="display:flex; justify-content:center; gap:8px; margin-top:12px; flex-wrap:wrap;">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="padding:8px 14px; border-radius:10px; background:#f1f5f9; color:#94a3b8; font-size:13px;">‹ Prev</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="padding:8px 14px; border-radius:10px; background:#fff; color:var(--sp-primary); font-size:13px; text-decoration:none; border:1.5px solid var(--sp-border);">‹ Prev</a>
    @endif

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="padding:8px 14px; border-radius:10px; background:var(--sp-primary); color:#fff; font-size:13px; text-decoration:none;">Next ›</a>
    @else
        <span style="padding:8px 14px; border-radius:10px; background:#f1f5f9; color:#94a3b8; font-size:13px;">Next ›</span>
    @endif
</div>
@endif
