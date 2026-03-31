@extends('sale.panel.layout')
@section('title', 'Items')

@section('topnav_actions')
    <a href="{{ route('sale.panel.sale.new') }}" id="cartNavBtn" style="position:relative;display:none;">
        <i class="bi bi-cart3"></i>
        <span id="cartNavCount" style="position:absolute;top:-6px;right:-6px;background:#DC2626;color:#fff;border-radius:50%;width:16px;height:16px;font-size:10px;display:flex;align-items:center;justify-content:center;font-weight:700;">0</span>
    </a>
@endsection

@section('content')

<!-- SEARCH + VIEW TOGGLE -->
<div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;">
    <div class="sp-search" style="flex:1;margin-bottom:0;">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Search products..." id="searchInput" onkeyup="clientSearch(this.value)">
    </div>
    <div style="display:flex;gap:4px;flex-shrink:0;">
        <button type="button" id="btnGrid" onclick="setView('grid')" class="view-btn active" title="Grid View">
            <i class="bi bi-grid-3x3-gap-fill"></i>
        </button>
        <button type="button" id="btnList" onclick="setView('list')" class="view-btn" title="List View">
            <i class="bi bi-list-ul"></i>
        </button>
    </div>
</div>

<!-- CATEGORY TABS -->
<div class="sp-filter-tabs" style="margin-bottom:14px;flex-wrap:wrap;">
    <span class="sp-filter-tab cat-tab {{ $category==='all' ? 'active' : '' }}" onclick="filterCat('all',this)">All</span>
    @foreach($categories as $cat)
    <span class="sp-filter-tab cat-tab {{ $category===$cat ? 'active' : '' }}" onclick="filterCat('{{ addslashes($cat) }}',this)">{{ $cat }}</span>
    @endforeach
    <button type="button" class="sp-filter-tab sp-add-item-tab" data-bs-toggle="modal" data-bs-target="#addItemModal">
        <i class="bi bi-plus-lg"></i> Add Item
    </button>
</div>

<!-- ADD ITEM MODAL -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content sp-add-item-modal">
            <div class="modal-header">
                <h5 class="modal-title">Add Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('sale.panel.items.store') }}" enctype="multipart/form-data" id="addItemForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Item Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Item name" required>
                        </div>

                        <div class="col-6 col-lg-4">
                            <label class="form-label">Sell Price<span class="text-danger">*</span></label>
                            <input type="number" name="sale_price" class="form-control" placeholder="Sell Price" step="0.01" min="0" required>
                        </div>

                        <div class="col-6 col-lg-4">
                            <label class="form-label">Units<span class="text-danger">*</span></label>
                            <select name="unit" class="form-select" required>
                                <option value="pcs" selected>PIECES(PCS)</option>
                                <option value="kg">KILOGRAM(KG)</option>
                                <option value="g">GRAM(G)</option>
                                <option value="l">LITRE(L)</option>
                                <option value="ml">MILLILITRE(ML)</option>
                                <option value="box">BOX</option>
                            </select>
                        </div>

                        <div class="col-12 col-lg-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="addItemPriceIncludesGst" name="price_includes_gst">
                                <label class="form-check-label" for="addItemPriceIncludesGst">Price includes taxes</label>
                            </div>
                        </div>

                        <div class="col-6 col-lg-4">
                            <label class="form-label">HSN/SAC</label>
                            <input type="text" name="hsn_code" class="form-control" placeholder="HSN/SAC">
                        </div>

                        <div class="col-6 col-lg-4">
                            <label class="form-label">Tax</label>
                            <select name="gst_percent" class="form-select">
                                <option value="0" selected>0%</option>
                                <option value="5">5%</option>
                                <option value="12">12%</option>
                                <option value="18">18%</option>
                                <option value="28">28%</option>
                            </select>
                        </div>

                        <div class="col-12 col-lg-4">
                            <label class="form-label">Category<span class="text-danger">*</span></label>
                            <select name="category" class="form-select" id="addItemCategory" required>
                                @php
                                    $catList = collect($categories ?? [])->filter()->values();
                                    if ($catList->isEmpty()) $catList = collect(['Default Category']);
                                @endphp
                                @foreach($catList as $catName)
                                    <option value="{{ $catName }}">{{ $catName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-lg-4">
                            <label class="form-label">MRP<span class="text-danger">*</span></label>
                            <input type="number" name="mrp" class="form-control" placeholder="MRP" step="0.01" min="0" required>
                        </div>

                        <div class="col-6 col-lg-4">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" placeholder="Stock" step="1" min="0">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Images</label>
                            <input class="form-control d-none" type="file" name="images[]" id="addItemImages" accept="image/*" multiple>
                            <div class="sp-img-grid">
                                <button type="button" class="sp-img-slot" id="addItemImgSlot0"><i class="bi bi-plus-lg"></i></button>
                                <button type="button" class="sp-img-slot" id="addItemImgSlot1"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <div class="form-text">Tap a box to upload (max 2 images).</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 sp-save-btn">SAVE</button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
function itemVars($product) {
    return [
        'stock'  => (int)($product->quantity ?? $product->stock_quantity ?? 0),
        'price'  => (float)($product->sale_price ?? $product->mrp ?? 0),
        'mrp'    => (float)($product->mrp ?? 0),
        'imgUrl' => $product->image
            ? (str_starts_with($product->image,'http')
                ? $product->image
                : (str_contains($product->image, '/') ? asset('storage/'.$product->image) : asset('storage/products/'.$product->image)))
            : null,
        'unit'   => $product->unit ?? 'pcs',
        'brand'  => $product->brand ?? null,
    ];
}
@endphp

<!-- ── GRID VIEW ── -->
<div id="viewGrid" class="product-grid">
@forelse($products as $product)
@php extract(itemVars($product)); $disc = $mrp>0&&$mrp>$price ? round((($mrp-$price)/$mrp)*100) : 0; @endphp
<div class="sp-product-card item-card" data-id="{{ $product->id }}" data-name="{{ strtolower($product->name) }}" data-price="{{ $price }}" data-cat="{{ strtolower($product->category ?? '') }}">
    @if($disc > 0)
    <div style="position:absolute;top:8px;left:8px;background:#DC2626;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;z-index:1;">{{ $disc }}% OFF</div>
    @endif
    @if($imgUrl)
        <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="sp-product-img"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
        <div class="sp-product-img sp-product-img-placeholder" style="display:none;"><i class="bi bi-box"></i></div>
    @else
        <div class="sp-product-img sp-product-img-placeholder"><i class="bi bi-box"></i></div>
    @endif
    <div class="sp-product-body">
        <div class="sp-product-name">{{ $product->name }}</div>
        @if($brand)<div style="font-size:11px;color:var(--muted);margin-bottom:3px;">{{ $brand }}</div>@endif
        @if($mrp > 0 && $mrp > $price)<div class="sp-product-mrp">MRP ₹{{ number_format($mrp,2) }}</div>@endif
        <div class="sp-product-price">₹{{ number_format($price,2) }}</div>
        @if($stock > 0)
            <span class="sp-stock-badge" style="background:#DCFCE7;color:#16A34A;">{{ $stock }} {{ $unit }}</span>
        @else
            <span class="sp-stock-badge" style="background:#FEE2E2;color:#DC2626;">Stock Out</span>
        @endif
        <!-- QTY CTRL -->
        <div class="sp-qty-ctrl" id="gQtyCtrl_{{ $product->id }}" style="display:none;margin-top:8px;">
            <button type="button" class="sp-qty-btn" onclick="changeQty({{ $product->id }},-1)">−</button>
            <span class="sp-qty-val" id="qty_{{ $product->id }}">0</span>
            <button type="button" class="sp-qty-btn" onclick="changeQty({{ $product->id }},1)">+</button>
        </div>
        <!-- ADD TO CART BTN -->
        <button type="button" class="add-cart-btn" id="gAddBtn_{{ $product->id }}"
                onclick="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $price }})"
                {{ $stock <= 0 ? 'disabled' : '' }}>
            <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
    </div>
</div>
@empty
<div class="empty-state" style="grid-column:1/-1;"><i class="bi bi-box-seam"></i><p>No products found</p></div>
@endforelse
</div>

<!-- ── LIST VIEW ── -->
<div id="viewList" style="display:none;">
@forelse($products as $product)
@php extract(itemVars($product)); $disc = $mrp>0&&$mrp>$price ? round((($mrp-$price)/$mrp)*100) : 0; @endphp
<div class="list-card item-card" data-id="{{ $product->id }}" data-name="{{ strtolower($product->name) }}" data-price="{{ $price }}" data-cat="{{ strtolower($product->category ?? '') }}">
    <!-- IMAGE -->
    <div class="list-img">
        @if($imgUrl)
            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;"
                 onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#CBD5E1;font-size:28px;\'><i class=\'bi bi-box\'></i></div>'">
        @else
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#CBD5E1;font-size:28px;"><i class="bi bi-box"></i></div>
        @endif
    </div>
    <!-- DETAILS -->
    <div style="flex:1;min-width:0;">
        <div style="font-weight:700;font-size:14px;color:var(--text);margin-bottom:2px;">{{ $product->name }}</div>
        @if($brand)<div style="font-size:11px;color:var(--muted);margin-bottom:4px;">{{ $brand }}</div>@endif
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
            <span style="font-size:16px;font-weight:700;color:var(--primary);">₹{{ number_format($price,2) }}</span>
            @if($mrp > 0 && $mrp > $price)
            <span style="font-size:12px;color:var(--muted);text-decoration:line-through;">₹{{ number_format($mrp,2) }}</span>
            <span style="font-size:11px;font-weight:700;background:#FEE2E2;color:#DC2626;padding:1px 6px;border-radius:20px;">{{ $disc }}% OFF</span>
            @endif
        </div>
        @if($stock > 0)
            <span class="sp-stock-badge" style="background:#DCFCE7;color:#16A34A;">{{ $stock }} {{ $unit }}</span>
        @else
            <span class="sp-stock-badge" style="background:#FEE2E2;color:#DC2626;">Stock Out</span>
        @endif
    </div>
    <!-- ADD / QTY -->
    <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:6px;min-width:90px;">
        <div class="sp-qty-ctrl" id="lQtyCtrl_{{ $product->id }}" style="display:none;flex-direction:column;align-items:center;gap:4px;">
            <button type="button" class="sp-qty-btn" onclick="changeQty({{ $product->id }},1)" style="width:32px;height:32px;">+</button>
            <span class="sp-qty-val" id="qty_list_{{ $product->id }}" style="font-size:15px;text-align:center;">0</span>
            <button type="button" class="sp-qty-btn" onclick="changeQty({{ $product->id }},-1)" style="width:32px;height:32px;">−</button>
        </div>
        <button type="button" class="add-cart-btn" id="lAddBtn_{{ $product->id }}"
                onclick="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $price }})"
                {{ $stock <= 0 ? 'disabled' : '' }}
                style="width:90px;font-size:12px;padding:7px 6px;">
            <i class="bi bi-cart-plus"></i> Add
        </button>
    </div>
</div>
@empty
<div class="empty-state"><i class="bi bi-box-seam"></i><p>No products found</p></div>
@endforelse
</div>

{{ $products->withQueryString()->links('vendor.pagination.simple-bootstrap-5') }}
<div style="height:90px;"></div>

<!-- CART BAR -->
<div id="cartBar" style="display:none;">
    <div>
        <div style="font-size:12px;opacity:.85;">Cart</div>
        <div style="font-weight:700;font-size:15px;"><span id="cartCount">0</span> items · ₹<span id="cartTotal">0</span></div>
    </div>
    <button onclick="goToCart()">Checkout <i class="bi bi-arrow-right ms-1"></i></button>
</div>

@push('styles')
<style>
.sp-add-item-tab { background: var(--primary-light); color: var(--primary); border-color: rgba(37,99,235,.35); }
.sp-add-item-tab:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
.sp-add-item-tab i { margin-right: 4px; }

.sp-add-item-modal .modal-header { border-bottom: 1px solid var(--border); }
.sp-add-item-modal .modal-footer { border-top: 1px solid var(--border); padding: 12px 16px; }
.sp-add-item-modal .modal-title { font-weight: 800; }
.sp-save-btn { border-radius: 12px; padding: 12px 14px; font-weight: 800; letter-spacing: .3px; }

.sp-img-grid { display: grid; grid-template-columns: repeat(2, 92px); gap: 10px; }
.sp-img-slot {
    width: 92px; height: 92px; border-radius: 14px;
    border: 1.5px dashed var(--border); background: var(--bg);
    display: flex; align-items: center; justify-content: center;
    color: #94A3B8; font-size: 22px; cursor: pointer;
    transition: all .15s;
}
.sp-img-slot:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.sp-img-slot.has-img { border-style: solid; border-color: var(--border); background-size: cover; background-position: center; }
.sp-img-slot.has-img i { display: none; }

.view-btn {
    width:36px;height:36px;border-radius:8px;border:1.5px solid var(--border);
    background:var(--card-bg);color:var(--muted);font-size:16px;cursor:pointer;
    display:flex;align-items:center;justify-content:center;transition:all .15s;
}
.view-btn.active { background:var(--primary);color:#fff;border-color:var(--primary); }
.view-btn:hover:not(.active) { border-color:var(--primary);color:var(--primary); }

.product-grid { display:grid;grid-template-columns:repeat(2,1fr);gap:12px; }
@media(min-width:640px)  { .product-grid { grid-template-columns:repeat(3,1fr); } }
@media(min-width:1024px) { .product-grid { grid-template-columns:repeat(4,1fr); } }

.sp-product-card { position:relative; }
.sp-product-img { width:100%;height:120px;object-fit:cover;display:block;background:var(--bg); }
.sp-product-img-placeholder { display:flex !important;align-items:center;justify-content:center;color:#CBD5E1;font-size:32px;height:120px; }

.list-card {
    display:flex;align-items:center;gap:14px;
    background:var(--card-bg);border:1px solid var(--border);
    border-radius:var(--radius);padding:14px;margin-bottom:10px;
    box-shadow:var(--shadow);transition:box-shadow .2s;
}
.list-card:hover { box-shadow:var(--shadow-md); }
.list-card.in-cart, .sp-product-card.in-cart { border-color:var(--primary); }

.list-img {
    width:72px;height:72px;border-radius:10px;
    background:var(--bg);flex-shrink:0;overflow:hidden;border:1px solid var(--border);
}

.add-cart-btn {
    width:100%;padding:8px 10px;border-radius:8px;border:1.5px solid var(--primary);
    background:var(--primary-light);color:var(--primary);
    font-size:13px;font-weight:600;cursor:pointer;
    display:flex;align-items:center;justify-content:center;gap:5px;
    transition:all .15s;margin-top:8px;
}
.add-cart-btn:hover { background:var(--primary);color:#fff; }
.add-cart-btn:disabled { opacity:.45;cursor:not-allowed;background:var(--bg);color:var(--muted);border-color:var(--border); }

#cartBar {
    position:fixed;bottom:16px;left:50%;transform:translateX(-50%);
    width:calc(100% - 32px);max-width:448px;
    background:var(--primary);color:#fff;border-radius:14px;padding:14px 18px;
    display:flex;align-items:center;justify-content:space-between;
    z-index:300;box-shadow:0 4px 20px rgba(37,99,235,.4);
}
#cartBar button {
    background:#fff;color:var(--primary);border:none;
    border-radius:8px;padding:8px 18px;font-weight:700;cursor:pointer;font-size:13.5px;
}
</style>
@endpush

@endsection

@push('scripts')
<script>
let cart     = JSON.parse(sessionStorage.getItem('sp_cart') || '{}');
let viewMode = localStorage.getItem('items_view') || 'grid';

/* —— ADD ITEM MODAL —— */
function bindAddItemModal() {
    const modalEl = document.getElementById('addItemModal');
    const fileInp = document.getElementById('addItemImages');
    const slot0   = document.getElementById('addItemImgSlot0');
    const slot1   = document.getElementById('addItemImgSlot1');
    const formEl  = document.getElementById('addItemForm');
    const catSel  = document.getElementById('addItemCategory');

    if (!modalEl || !fileInp || !slot0 || !slot1) return;

    function openPicker() { fileInp.click(); }
    slot0.addEventListener('click', openPicker);
    slot1.addEventListener('click', openPicker);

    function setSlot(slot, file) {
        if (!file) {
            slot.classList.remove('has-img');
            slot.style.backgroundImage = '';
            return;
        }
        const url = URL.createObjectURL(file);
        slot.classList.add('has-img');
        slot.style.backgroundImage = `url('${url}')`;
    }

    fileInp.addEventListener('change', () => {
        const files = Array.from(fileInp.files || []).slice(0, 2);
        setSlot(slot0, files[0]);
        setSlot(slot1, files[1]);
    });

    modalEl.addEventListener('show.bs.modal', () => {
        if (formEl) formEl.reset();
        setSlot(slot0, null);
        setSlot(slot1, null);

        const activeCat = document.querySelector('.cat-tab.active');
        const label = (activeCat?.textContent || '').trim();
        if (catSel && label && label.toLowerCase() !== 'all') {
            catSel.value = label;
        }
    });
}

/* ── VIEW TOGGLE ── */
function setView(mode) {
    viewMode = mode;
    localStorage.setItem('items_view', mode);
    document.getElementById('viewGrid').style.display = mode === 'grid' ? 'grid' : 'none';
    document.getElementById('viewList').style.display = mode === 'list' ? 'block' : 'none';
    document.getElementById('btnGrid').classList.toggle('active', mode === 'grid');
    document.getElementById('btnList').classList.toggle('active', mode === 'list');
}

/* ── CLIENT-SIDE SEARCH ── */
function clientSearch(q) {
    q = q.toLowerCase();
    document.querySelectorAll('.item-card').forEach(card => {
        card.style.display = card.dataset.name.includes(q) ? '' : 'none';
    });
}

/* ── CATEGORY FILTER ── */
function filterCat(cat, el) {
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.item-card').forEach(card => {
        card.style.display = (cat === 'all' || card.dataset.cat === cat.toLowerCase()) ? '' : 'none';
    });
}

/* ── ADD TO CART ── */
function addItem(id, name, price) {
    const cards = document.querySelectorAll(`.item-card[data-id="${id}"]`);
    if (!cards.length) return;
    if (parseInt(cards[0].dataset.stock) <= 0) return;
    cart[id] = { id, name, price, qty: 1 };
    sessionStorage.setItem('sp_cart', JSON.stringify(cart));
    showQtyCtrl(id, 1);
    updateCartBar();
}

/* ── CHANGE QTY ── */
function changeQty(id, delta) {
    let qty = (cart[id]?.qty || 0) + delta;
    if (qty < 0) qty = 0;
    if (qty > 0) {
        cart[id].qty = qty;
        showQtyCtrl(id, qty);
    } else {
        delete cart[id];
        hideQtyCtrl(id);
    }
    sessionStorage.setItem('sp_cart', JSON.stringify(cart));
    updateCartBar();
}

function showQtyCtrl(id, qty) {
    const gCtrl = document.getElementById('gQtyCtrl_' + id);
    const gBtn  = document.getElementById('gAddBtn_' + id);
    const gVal  = document.getElementById('qty_' + id);
    if (gCtrl) gCtrl.style.display = 'flex';
    if (gBtn)  gBtn.style.display  = 'none';
    if (gVal)  gVal.textContent    = qty;

    const lCtrl = document.getElementById('lQtyCtrl_' + id);
    const lBtn  = document.getElementById('lAddBtn_' + id);
    const lVal  = document.getElementById('qty_list_' + id);
    if (lCtrl) lCtrl.style.display = 'flex';
    if (lBtn)  lBtn.style.display  = 'none';
    if (lVal)  lVal.textContent    = qty;

    document.querySelectorAll(`.item-card[data-id="${id}"]`).forEach(c => c.classList.add('in-cart'));
}

function hideQtyCtrl(id) {
    const gCtrl = document.getElementById('gQtyCtrl_' + id);
    const gBtn  = document.getElementById('gAddBtn_' + id);
    if (gCtrl) gCtrl.style.display = 'none';
    if (gBtn)  gBtn.style.display  = 'flex';

    const lCtrl = document.getElementById('lQtyCtrl_' + id);
    const lBtn  = document.getElementById('lAddBtn_' + id);
    if (lCtrl) lCtrl.style.display = 'none';
    if (lBtn)  lBtn.style.display  = 'flex';

    document.querySelectorAll(`.item-card[data-id="${id}"]`).forEach(c => c.classList.remove('in-cart'));
}

/* ── CART BAR ── */
function updateCartBar() {
    const keys  = Object.keys(cart);
    const count = keys.reduce((s, k) => s + cart[k].qty, 0);
    const total = keys.reduce((s, k) => s + cart[k].qty * cart[k].price, 0);
    const bar   = document.getElementById('cartBar');
    const nav   = document.getElementById('cartNavBtn');
    document.getElementById('cartCount').textContent = count;
    document.getElementById('cartTotal').textContent = total.toFixed(2);
    bar.style.display = count > 0 ? 'flex' : 'none';
    nav.style.display = count > 0 ? 'block' : 'none';
    if (count > 0) document.getElementById('cartNavCount').textContent = count;
}

function goToCart() {
    window.location.href = '{{ route("sale.panel.sale.new") }}';
}

/* ── INIT ── */
document.addEventListener('DOMContentLoaded', () => {
    Object.keys(cart).forEach(id => showQtyCtrl(id, cart[id].qty));
    updateCartBar();
    setView(viewMode);
    bindAddItemModal();
});
</script>
@endpush
