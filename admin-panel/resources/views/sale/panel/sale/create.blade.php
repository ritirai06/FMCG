@extends('sale.panel.layout')
@section('title', 'New Sale')
@section('back')1@endsection
@section('back_url', route('sale.panel.parties'))

@section('content')

<!-- STEP INDICATOR -->
<div class="step-bar">
    @foreach(['Select Party','Add Items','Review & Save'] as $i => $step)
    <div id="step_tab_{{ $i }}" class="step-tab {{ $i===0 ? 'active' : '' }}">
        <div class="step-num">{{ $i+1 }}</div>
        <div class="step-lbl">{{ $step }}</div>
    </div>
    @endforeach
</div>

<!-- ══════════════════════════════════════
     STEP 1 — SELECT PARTY
══════════════════════════════════════ -->
<div id="step1">
    <div class="sp-section-hdr">Select Party</div>
    <div class="sp-search">
        <i class="bi bi-search"></i>
        <input type="text" id="partySearch" placeholder="Search party..." oninput="filterParties(this.value)">
    </div>
    <div id="partyList">
        @foreach($parties as $p)
        @php
            $colors = ['#2563EB','#059669','#D97706','#DC2626','#7C3AED'];
            $c = $colors[$p->id % 5];
            $initials = strtoupper(substr($p->business_name,0,2));
        @endphp
        <div class="sp-party-item party-row"
             data-name="{{ strtolower($p->business_name) }}" data-id="{{ $p->id }}"
             onclick="selectParty({{ $p->id }}, '{{ addslashes($p->business_name) }}', '{{ $p->mobile }}')"
             style="cursor:pointer;{{ $party && $party->id==$p->id ? 'border-color:var(--primary);background:var(--primary-light);' : '' }}">
            <div class="sp-party-avatar" style="background:{{ $c }}18;color:{{ $c }};">{{ $initials }}</div>
            <div style="flex:1;min-width:0;">
                <div class="sp-party-name">{{ $p->business_name }}</div>
                <div class="sp-party-meta">{{ $p->mobile }}</div>
            </div>
            @if($party && $party->id==$p->id)
            <i class="bi bi-check-circle-fill" style="color:var(--primary);font-size:20px;flex-shrink:0;"></i>
            @endif
        </div>
        @endforeach
    </div>
    <div style="height:80px;"></div>
    <div class="sp-sticky-save">
        <button onclick="goStep(2)" class="sp-save-btn" id="step1Btn" disabled>
            Next: Add Items <i class="bi bi-arrow-right ms-2"></i>
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════
     STEP 2 — ADD ITEMS
══════════════════════════════════════ -->
<div id="step2" style="display:none;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
        <div class="sp-section-hdr" style="margin:0;">Products</div>
        <button onclick="goStep(1)" style="background:none;border:none;color:var(--primary);font-size:13px;cursor:pointer;font-weight:600;">
            <i class="bi bi-arrow-left me-1"></i>Change Party
        </button>
    </div>

    <!-- Selected party badge -->
    <div id="selectedPartyBadge" style="background:var(--primary-light);color:var(--primary);border-radius:8px;padding:8px 12px;font-size:13px;font-weight:600;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
        <i class="bi bi-person-fill"></i> <span id="partyBadgeText"></span>
    </div>

    <!-- Search + Category -->
    <div class="sp-search">
        <i class="bi bi-search"></i>
        <input type="text" id="productSearch" placeholder="Search products..." oninput="filterProducts(this.value)">
    </div>
    <div class="sp-filter-tabs" id="catTabs">
        <span class="sp-filter-tab active" onclick="filterCat('all',this)">All</span>
        @foreach($categories as $cat)
        <span class="sp-filter-tab" onclick="filterCat('{{ $cat }}',this)" style="cursor:pointer;">{{ $cat }}</span>
        @endforeach
    </div>

    <!-- PRODUCT GRID -->
    <div class="sale-product-grid" id="productGrid">
        @forelse($products as $product)
        @php
            $stock  = (int)($product->quantity ?? $product->stock_quantity ?? 0);
            $price  = (float)($product->sale_price ?? $product->mrp ?? 0);
            $mrp    = (float)($product->mrp ?? 0);
            $disc   = ($mrp > 0 && $mrp > $price) ? round((($mrp-$price)/$mrp)*100) : 0;
            $imgUrl = $product->image
                ? (str_starts_with($product->image,'http')
                    ? $product->image
                    : (str_contains($product->image, '/') ? asset('storage/'.$product->image) : asset('storage/products/'.$product->image)))
                : null;
            $unit   = $product->unit ?? 'pcs';
            $brand  = $product->brand ?? null;
        @endphp
        <div class="sale-product-card product-row"
             data-id="{{ $product->id }}"
             data-name="{{ $product->name }}"
             data-price="{{ $price }}"
             data-stock="{{ $stock }}"
             data-cat="{{ strtolower($product->category ?? '') }}"
             data-search="{{ strtolower($product->name) }}">

            <!-- IMAGE -->
            <div class="sale-prod-img">
                @if($disc > 0)
                <span class="disc-badge">{{ $disc }}% OFF</span>
                @endif
                @if($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}"
                         onerror="this.parentElement.innerHTML='<div class=\'img-placeholder\'><i class=\'bi bi-box\'></i></div>'">
                @else
                    <div class="img-placeholder"><i class="bi bi-box"></i></div>
                @endif
            </div>

            <!-- DETAILS -->
            <div class="sale-prod-body">
                <div class="sale-prod-name">{{ $product->name }}</div>
                @if($brand)
                <div style="font-size:11px;color:var(--muted);margin-bottom:3px;">{{ $brand }}</div>
                @endif
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
                    <span style="font-size:15px;font-weight:700;color:var(--primary);">₹{{ number_format($price,2) }}</span>
                    @if($mrp > 0 && $mrp > $price)
                    <span style="font-size:11px;color:var(--muted);text-decoration:line-through;">₹{{ number_format($mrp,2) }}</span>
                    @endif
                </div>
                @if($stock > 0)
                    <span class="sp-stock-badge" style="background:#DCFCE7;color:#16A34A;">{{ $stock }} {{ $unit }}</span>
                @else
                    <span class="sp-stock-badge" style="background:#FEE2E2;color:#DC2626;">Stock Out</span>
                @endif

        <!-- QTY CTRL -->
        <div class="sp-qty-ctrl" id="qtyCtrl_{{ $product->id }}" style="display:none;">
            <button type="button" class="sp-qty-btn" onclick="changeQty({{ $product->id }},-1)">−</button>
            <span class="sp-qty-val" id="qty_{{ $product->id }}">0</span>
            <button type="button" class="sp-qty-btn" onclick="changeQty({{ $product->id }},1)">+</button>
        </div>
        <!-- ADD TO CART BTN -->
        <button type="button" class="add-item-btn" id="addBtn_{{ $product->id }}"
                onclick="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $price }})"
                {{ $stock <= 0 ? 'disabled' : '' }}>
            <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
            </div>
        </div>
        @empty
        <div class="empty-state" style="grid-column:1/-1;">
            <i class="bi bi-box-seam"></i><p>No products found</p>
        </div>
        @endforelse
    </div>

    <div style="height:80px;"></div>
    <div class="sp-sticky-save">
        <button onclick="goStep(3)" class="sp-save-btn" id="step2Btn" disabled>
            Review Cart <span id="cartBadge" style="background:rgba(255,255,255,.25);border-radius:20px;padding:1px 8px;font-size:12px;margin-left:6px;display:none;">0</span>
            <i class="bi bi-arrow-right ms-2"></i>
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════
     STEP 3 — REVIEW & SAVE
══════════════════════════════════════ -->
<div id="step3" style="display:none;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <div class="sp-section-hdr" style="margin:0;">Review Cart</div>
        <button onclick="goStep(2)" style="background:none;border:none;color:var(--primary);font-size:13px;cursor:pointer;font-weight:600;">
            <i class="bi bi-arrow-left me-1"></i>Edit Items
        </button>
    </div>

    <div id="cartReview"></div>

    <div class="sp-section-hdr" style="margin-top:14px;">Payment Summary</div>
    <div class="summary-box" id="paymentSummary"></div>

    <div class="sp-section-hdr" style="margin-top:14px;">Notes</div>
    <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);padding:12px 14px;">
        <textarea id="saleNotes" rows="3" style="width:100%;border:none;outline:none;font-size:13px;resize:none;font-family:'Inter',sans-serif;" placeholder="Add notes or delivery instructions..."></textarea>
    </div>

    <div style="height:80px;"></div>

    <form method="POST" action="{{ route('sale.panel.sale.store') }}" id="saleForm">
        @csrf
        <input type="hidden" name="customer_id" id="finalCustomerId">
        <input type="hidden" name="notes"        id="finalNotes">
        <div id="itemInputs"></div>
    </form>

    <div class="sp-sticky-save">
        <button onclick="submitSale()" class="sp-save-btn">
            <i class="bi bi-check-lg me-2"></i>Save Sale
        </button>
    </div>
</div>

@push('styles')
<style>
/* STEP BAR */
.step-bar { display:flex; margin-bottom:20px; border-radius:10px; overflow:hidden; border:1.5px solid var(--border); }
.step-tab { flex:1; padding:10px 4px; text-align:center; font-size:11px; font-weight:600; background:var(--card-bg); color:var(--muted); border-right:1.5px solid var(--border); transition:all .2s; }
.step-tab:last-child { border-right:none; }
.step-tab.active { background:var(--primary); color:#fff; }
.step-num { font-size:15px; font-weight:700; }
.step-lbl { font-size:10.5px; margin-top:1px; }

/* PRODUCT GRID */
.sale-product-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
@media(min-width:640px)  { .sale-product-grid { grid-template-columns:repeat(3,1fr); } }
@media(min-width:1024px) { .sale-product-grid { grid-template-columns:repeat(4,1fr); } }

/* PRODUCT CARD */
.sale-product-card {
    background:var(--card-bg); border:1px solid var(--border);
    border-radius:var(--radius); overflow:hidden;
    box-shadow:var(--shadow); transition:box-shadow .2s, transform .2s;
}
.sale-product-card:hover { box-shadow:var(--shadow-md); transform:translateY(-2px); }
.sale-product-card.in-cart { border-color:var(--primary); }

/* PRODUCT IMAGE */
.sale-prod-img {
    position:relative; width:100%; height:120px;
    background:var(--bg); overflow:hidden;
    display:flex; align-items:center; justify-content:center;
}
.sale-prod-img img { width:100%; height:100%; object-fit:cover; }
.img-placeholder { display:flex; align-items:center; justify-content:center; width:100%; height:100%; color:#CBD5E1; font-size:32px; }
.disc-badge { position:absolute; top:6px; left:6px; background:#DC2626; color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px; z-index:1; }

/* PRODUCT BODY */
.sale-prod-body { padding:10px 12px 12px; }
.sale-prod-name { font-weight:700; font-size:13px; margin-bottom:3px; color:var(--text); line-height:1.3; }

/* QTY ROW */
.sale-qty-row { margin-top:8px; }

/* ADD ITEM BUTTON */
.add-item-btn {
    width:100%; padding:8px 10px; border-radius:8px; border:1.5px solid var(--primary);
    background:var(--primary-light); color:var(--primary);
    font-size:13px; font-weight:600; cursor:pointer;
    display:flex; align-items:center; justify-content:center; gap:6px;
    transition:all .15s;
}
.add-item-btn:hover:not(:disabled) { background:var(--primary); color:#fff; }
.add-item-btn:disabled { opacity:.45; cursor:not-allowed; background:var(--bg); color:var(--muted); border-color:var(--border); }
</style>
@endpush

@endsection

@push('scripts')
<script>
let selectedPartyId   = {{ $party ? $party->id : 'null' }};
let selectedPartyName = '{{ $party ? addslashes($party->business_name) : '' }}';
let cart = {};
try { cart = JSON.parse(sessionStorage.getItem('sp_cart') || '{}'); } catch(e){}

document.addEventListener('DOMContentLoaded', () => {
    if (selectedPartyId) {
        document.getElementById('step1Btn').disabled = false;
        document.getElementById('partyBadgeText').textContent = selectedPartyName;
    }
    // Restore qty UI
    Object.keys(cart).forEach(id => {
        const el = document.getElementById('qty_' + id);
        if (el) {
            el.textContent = cart[id].qty;
            showQtyCtrl(id);
        }
    });
    updateStep2Btn();
    if (selectedPartyId) goStep(2);
});

function selectParty(id, name, mobile) {
    selectedPartyId   = id;
    selectedPartyName = name;
    document.querySelectorAll('.party-row').forEach(r => {
        r.style.borderColor = '';
        r.style.background  = '';
        r.querySelector('.bi-check-circle-fill') && r.querySelector('.bi-check-circle-fill').remove();
    });
    const row = document.querySelector('.party-row[data-id="'+id+'"]');
    if (row) {
        row.style.borderColor = 'var(--primary)';
        row.style.background  = 'var(--primary-light)';
    }
    document.getElementById('step1Btn').disabled = false;
}

function filterParties(q) {
    q = q.toLowerCase();
    document.querySelectorAll('.party-row').forEach(r => {
        r.style.display = r.dataset.name.includes(q) ? '' : 'none';
    });
}

function filterProducts(q) {
    q = q.toLowerCase();
    document.querySelectorAll('.product-row').forEach(r => {
        r.style.display = r.dataset.search.includes(q) ? '' : 'none';
    });
}

function filterCat(cat, el) {
    document.querySelectorAll('#catTabs .sp-filter-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.product-row').forEach(r => {
        r.style.display = (cat === 'all' || r.dataset.cat === cat.toLowerCase()) ? '' : 'none';
    });
}

function addItem(id, name, price) {
    const card = document.querySelector('.product-row[data-id="'+id+'"]');
    if (!card) return;
    if (parseInt(card.dataset.stock) <= 0) return;
    cart[id] = { id, name: name || card.dataset.name, price: price || parseFloat(card.dataset.price), qty: 1 };
    sessionStorage.setItem('sp_cart', JSON.stringify(cart));
    showQtyCtrl(id);
    updateStep2Btn();
}

function showQtyCtrl(id) {
    const ctrl   = document.getElementById('qtyCtrl_' + id);
    const addBtn = document.getElementById('addBtn_' + id);
    if (ctrl)   ctrl.style.display   = 'flex';
    if (addBtn) addBtn.style.display = 'none';
    const card = document.querySelector('.product-row[data-id="'+id+'"]');
    if (card) card.classList.add('in-cart');
}

function hideQtyCtrl(id) {
    const ctrl   = document.getElementById('qtyCtrl_' + id);
    const addBtn = document.getElementById('addBtn_' + id);
    if (ctrl)   ctrl.style.display   = 'none';
    if (addBtn) addBtn.style.display = 'flex';
    const card = document.querySelector('.product-row[data-id="'+id+'"]');
    if (card) card.classList.remove('in-cart');
}

function changeQty(id, delta) {
    const el   = document.getElementById('qty_' + id);
    const card = document.querySelector('.product-row[data-id="'+id+'"]');
    let   qty  = parseInt(el.textContent) + delta;
    if (qty < 0) qty = 0;
    el.textContent = qty;
    if (qty > 0) {
        cart[id] = { id, name: card.dataset.name, price: parseFloat(card.dataset.price), qty };
    } else {
        delete cart[id];
        hideQtyCtrl(id);
    }
    sessionStorage.setItem('sp_cart', JSON.stringify(cart));
    updateStep2Btn();
}

function updateStep2Btn() {
    const count = Object.values(cart).reduce((s,i) => s + i.qty, 0);
    const btn   = document.getElementById('step2Btn');
    const badge = document.getElementById('cartBadge');
    if (btn) btn.disabled = count === 0;
    if (badge) {
        badge.textContent    = count;
        badge.style.display  = count > 0 ? 'inline' : 'none';
    }
}

function goStep(n) {
    [1,2,3].forEach(i => {
        document.getElementById('step' + i).style.display = i === n ? '' : 'none';
        const tab = document.getElementById('step_tab_' + (i-1));
        tab.classList.toggle('active', i === n);
    });
    if (n === 2) {
        document.getElementById('partyBadgeText').textContent = selectedPartyName;
    }
    if (n === 3) buildReview();
    window.scrollTo(0,0);
}

function buildReview() {
    const items = Object.values(cart);
    let html = '', subtotal = 0;
    items.forEach(item => {
        const lineTotal = item.qty * item.price;
        subtotal += lineTotal;
        html += `<div class="sp-cart-item">
            <div style="flex:1;">
                <div class="sp-cart-name">${item.name}</div>
                <div style="font-size:12px;color:var(--muted);">₹${item.price.toFixed(2)} × ${item.qty}</div>
            </div>
            <div class="sp-cart-price">₹${lineTotal.toFixed(2)}</div>
            <button type="button" class="sp-remove-btn" onclick="removeItem(${item.id})">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>`;
    });
    if (!html) html = '<div class="empty-state"><i class="bi bi-cart-x"></i><p>Cart is empty</p></div>';
    document.getElementById('cartReview').innerHTML = html;

    const gstRate = 18;
    const taxable = subtotal / (1 + gstRate / 100);
    const cgst    = (subtotal - taxable) / 2;
    document.getElementById('paymentSummary').innerHTML = `
        <div class="summary-row"><span>Taxable Amount</span><span>₹${taxable.toFixed(2)}</span></div>
        <div class="summary-row"><span>CGST (9%)</span><span>₹${cgst.toFixed(2)}</span></div>
        <div class="summary-row"><span>SGST (9%)</span><span>₹${cgst.toFixed(2)}</span></div>
        <div class="summary-row total"><span>Total Amount</span><span>₹${subtotal.toFixed(2)}</span></div>
    `;
}

function removeItem(id) {
    delete cart[id];
    sessionStorage.setItem('sp_cart', JSON.stringify(cart));
    const el = document.getElementById('qty_' + id);
    if (el) { el.textContent = 0; hideQtyCtrl(id); }
    buildReview();
    updateStep2Btn();
    if (Object.keys(cart).length === 0) goStep(2);
}

function submitSale() {
    const items = Object.values(cart);
    if (!selectedPartyId) { alert('Please select a party first.'); goStep(1); return; }
    if (items.length === 0) { alert('Please add at least one item.'); goStep(2); return; }
    document.getElementById('finalCustomerId').value = selectedPartyId;
    document.getElementById('finalNotes').value      = document.getElementById('saleNotes').value;
    const container = document.getElementById('itemInputs');
    container.innerHTML = '';
    items.forEach((item, i) => {
        container.innerHTML += `
            <input type="hidden" name="items[${i}][product_id]" value="${item.id}">
            <input type="hidden" name="items[${i}][quantity]"   value="${item.qty}">
        `;
    });
    sessionStorage.removeItem('sp_cart');
    document.getElementById('saleForm').submit();
}
</script>
@endpush
