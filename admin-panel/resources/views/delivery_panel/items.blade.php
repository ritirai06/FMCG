@extends('delivery_panel.layout')
@section('page_title', 'Items')

@push('styles')
<style>
    .items-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:14px; }
    .item-card {
        background:#fff; border:1px solid var(--border); border-radius:var(--radius);
        box-shadow:var(--shadow); padding:14px; cursor:pointer;
        transition:all .15s; display:flex; flex-direction:column; gap:8px;
    }
    .item-card:hover { border-color:var(--primary); transform:translateY(-2px); box-shadow:0 6px 20px rgba(37,99,235,.12); }
    .item-img {
        width:100%; height:120px; border-radius:8px; object-fit:cover;
        background:#f1f5f9; display:flex; align-items:center; justify-content:center;
        color:#cbd5e1; font-size:32px;
    }
    .item-img img { width:100%; height:100%; object-fit:cover; border-radius:8px; }
    .item-name { font-weight:700; font-size:13px; color:var(--text); line-height:1.3; }
    .item-meta { font-size:12px; color:var(--muted); }
    .item-price { font-size:15px; font-weight:800; color:var(--primary); }
    .item-mrp { font-size:11px; color:var(--muted); text-decoration:line-through; }
    .item-stock-ok  { background:#dcfce7; color:#16a34a; }
    .item-stock-low { background:#fef3c7; color:#d97706; }
    .item-stock-out { background:#fee2e2; color:#dc2626; }

    /* Modal */
    .item-modal-overlay {
        display:none; position:fixed; inset:0;
        background:rgba(0,0,0,.45); z-index:2000;
        align-items:center; justify-content:center; padding:16px;
    }
    .item-modal-overlay.show { display:flex; }
    .item-modal {
        background:#fff; border-radius:16px; width:100%; max-width:520px;
        box-shadow:0 20px 60px rgba(0,0,0,.2); overflow:hidden;
        animation:slideUp .2s ease;
    }
    @keyframes slideUp { from { transform:translateY(30px); opacity:0; } to { transform:translateY(0); opacity:1; } }
    .item-modal-header {
        padding:16px 20px; border-bottom:1px solid var(--border);
        display:flex; justify-content:space-between; align-items:center;
    }
    .item-modal-body { padding:20px; }
    .item-modal-close {
        width:32px; height:32px; border-radius:8px; border:1px solid var(--border);
        background:var(--bg); cursor:pointer; display:flex; align-items:center;
        justify-content:center; font-size:16px; color:var(--muted);
    }
    .item-modal-close:hover { background:#fee2e2; color:#dc2626; border-color:#fca5a5; }
    .detail-row { display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid var(--border); font-size:13px; }
    .detail-row:last-child { border-bottom:none; }
    .detail-label { color:var(--muted); font-weight:600; }
    .detail-value { font-weight:700; text-align:right; }
    .modal-img { width:100%; height:180px; object-fit:cover; border-radius:10px; margin-bottom:16px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#cbd5e1; font-size:48px; }
    .modal-img img { width:100%; height:100%; object-fit:cover; border-radius:10px; }
</style>
@endpush

@section('content')

{{-- SEARCH + COUNT --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
    <div>
        <h5 style="font-weight:800;margin:0;">Items Catalog</h5>
        <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $products->count() }} products · read-only view</div>
    </div>
    <div class="dp-search" style="margin:0;width:280px;">
        <i class="fas fa-search"></i>
        <input type="text" id="itemSearch" placeholder="Search by name, brand, category...">
    </div>
</div>

{{-- FILTER TABS --}}
<div class="dp-tabs" style="margin-bottom:16px;" id="stockTabs">
    <div class="dp-tab active" data-filter="all">All</div>
    <div class="dp-tab" data-filter="in">In Stock</div>
    <div class="dp-tab" data-filter="low">Low Stock</div>
    <div class="dp-tab" data-filter="out">Out of Stock</div>
</div>

{{-- ITEMS GRID --}}
<div class="items-grid" id="itemsGrid">
    @forelse($products as $product)
    @php
        $stock = (int)($product->available_units ?? $product->quantity ?? 0);
        $stockClass = $stock > 10 ? 'item-stock-ok' : ($stock > 0 ? 'item-stock-low' : 'item-stock-out');
        $stockFilter = $stock > 10 ? 'in' : ($stock > 0 ? 'low' : 'out');
        $hasImg = $product->image && file_exists(public_path('storage/'.$product->image));
    @endphp
    <div class="item-card"
         data-filter="{{ $stockFilter }}"
         data-search="{{ strtolower($product->name.' '.($product->brand ?? '').' '.($product->category ?? '')) }}"
         onclick="showItem({{ $product->id }})">

        <div class="item-img">
            @if($hasImg)
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
            @else
                <i class="fas fa-box"></i>
            @endif
        </div>

        <div class="item-name">{{ $product->name }}</div>

        @if($product->brand || $product->category)
        <div class="item-meta">
            {{ $product->brand ?? '' }}{{ $product->brand && $product->category ? ' · ' : '' }}{{ $product->category ?? '' }}
        </div>
        @endif

        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:4px;">
            <div>
                <div class="item-price">₹{{ number_format($product->sell_price ?? $product->mrp ?? 0) }}</div>
                @if(($product->mrp ?? 0) > ($product->sell_price ?? 0) && ($product->sell_price ?? 0) > 0)
                <div class="item-mrp">MRP ₹{{ number_format($product->mrp) }}</div>
                @endif
            </div>
            <span class="dp-badge {{ $stockClass }}" style="font-size:11px;">
                {{ $stock > 0 ? $stock.' units' : 'Out of Stock' }}
            </span>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted);">
        <i class="fas fa-box" style="font-size:36px;opacity:.2;display:block;margin-bottom:8px;"></i>
        No products found.
    </div>
    @endforelse
</div>

{{-- ITEM DETAIL MODAL --}}
<div class="item-modal-overlay" id="itemModal" onclick="closeItemModal(event)">
    <div class="item-modal">
        <div class="item-modal-header">
            <div style="font-weight:800;font-size:15px;" id="modalTitle">Product Details</div>
            <button class="item-modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="item-modal-body" id="modalBody">
            <div style="text-align:center;padding:20px;color:var(--muted);">Loading...</div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@php
$productsJson = $products->map(function($p) {
    return [
        'id'           => $p->id,
        'name'         => $p->name,
        'sku'          => $p->sku ?? '-',
        'brand'        => $p->brand ?? '-',
        'category'     => $p->category ?? '-',
        'sub_category' => $p->sub_category ?? '-',
        'mrp'          => $p->mrp ?? 0,
        'sell_price'   => $p->sell_price ?? $p->mrp ?? 0,
        'stock'        => (int)($p->available_units ?? $p->quantity ?? 0),
        'unit'         => $p->unit ?? 'pcs',
        'gst'          => $p->gst_percent ?? 0,
        'hsn'          => $p->hsn_code ?? '-',
        'description'  => $p->item_description ?? '-',
        'image'        => $p->image ? '/storage/'.$p->image : null,
        'status'       => $p->status ? 'Active' : 'Inactive',
    ];
});
@endphp
<script>
const products = {!! json_encode($productsJson) !!};

function showItem(id) {
    const p = products.find(x => x.id === id);
    if (!p) return;
    document.getElementById('modalTitle').textContent = p.name;
    const stock = p.stock;
    const stockBadge = stock > 10
        ? `<span class="dp-badge item-stock-ok">${stock} units</span>`
        : stock > 0
        ? `<span class="dp-badge item-stock-low">${stock} units (Low)</span>`
        : `<span class="dp-badge item-stock-out">Out of Stock</span>`;

    const imgHtml = p.image
        ? `<div class="modal-img"><img src="${p.image}" alt="${p.name}"></div>`
        : `<div class="modal-img"><i class="fas fa-box"></i></div>`;

    const rows = [
        ['SKU',          p.sku],
        ['Brand',        p.brand],
        ['Category',     p.category],
        ['Sub-Category', p.sub_category],
        ['Unit',         p.unit],
        ['MRP',          '₹' + Number(p.mrp).toLocaleString('en-IN')],
        ['Sell Price',   '₹' + Number(p.sell_price).toLocaleString('en-IN')],
        ['GST %',        p.gst + '%'],
        ['HSN Code',     p.hsn],
        ['Stock',        stockBadge],
        ['Status',       `<span class="dp-badge ${p.status==='Active'?'dp-badge-delivered':'dp-badge-failed'}">${p.status}</span>`],
        ['Description',  p.description],
    ].map(([label, value]) => `
        <div class="detail-row">
            <span class="detail-label">${label}</span>
            <span class="detail-value">${value}</span>
        </div>`).join('');

    document.getElementById('modalBody').innerHTML = imgHtml + rows;
    document.getElementById('itemModal').classList.add('show');
}

function closeModal() {
    document.getElementById('itemModal').classList.remove('show');
}

function closeItemModal(e) {
    if (e.target === document.getElementById('itemModal')) closeModal();
}

// Search
document.getElementById('itemSearch').addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('.item-card').forEach(card => {
        card.style.display = card.dataset.search.includes(q) ? '' : 'none';
    });
});

// Stock filter tabs
document.querySelectorAll('#stockTabs .dp-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('#stockTabs .dp-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const f = tab.dataset.filter;
        document.querySelectorAll('.item-card').forEach(card => {
            card.style.display = (f === 'all' || card.dataset.filter === f) ? '' : 'none';
        });
    });
});

// Close on Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endpush
