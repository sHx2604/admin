<?php
require_once '../core/functions.php';
checkAuth();

$page_title = 'Point of Sale';

// Get all active products
$products = getProducts(['status' => 'active']);
$categories = getCategories(true);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="pos-layout">
        <!-- Products Section -->
        <div>
            <div class="card" style="margin-bottom: 15px;">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" id="searchProduct" class="form-control" placeholder="Cari produk..." style="flex: 1;">
                    <select id="filterCategory" class="form-control" style="width: 200px;">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="products-grid" id="productsGrid">
                <?php foreach ($products as $product): ?>
                <div class="product-card" data-id="<?= $product['id'] ?>" data-category="<?= $product['category_id'] ?>"
                     onclick="addToCart(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $product['price'] ?>, '<?= htmlspecialchars($product['sku']) ?>')">
                    <div style="font-size: 40px; margin-bottom: 10px;">🍽️</div>
                    <h4><?= htmlspecialchars($product['name']) ?></h4>
                    <p class="price"><?= formatCurrency($product['price']) ?></p>
                    <p style="font-size: 12px; color: var(--secondary);">Stok: <?= $product['stock'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Cart Section -->
        <div class="cart-section">
            <h3 style="margin-bottom: 20px;">Keranjang</h3>

            <div class="cart-items" id="cartItems">
                <p class="text-center" style="color: var(--secondary);">Keranjang kosong</p>
            </div>

            <div class="cart-total">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Subtotal:</span>
                    <strong id="subtotal">Rp 0</strong>
                </div>

                <h3 style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <span>Total:</span>
                    <span id="totalAmount">Rp 0</span>
                </h3>

                <div class="form-group">
                    <label class="form-label">Metode Pembayaran</label>
                    <select id="paymentMethod" class="form-control">
                        <option value="cash">Tunai</option>
                        <option value="card">Kartu</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Pelanggan (Opsional)</label>
                    <input type="text" id="customerName" class="form-control">
                </div>

                <div style="display: flex; gap: 10px;">
                    <button class="btn btn-danger" onclick="clearCart()" style="flex: 1;">
                        Batal
                    </button>
                    <button class="btn btn-success" onclick="processCheckout()" style="flex: 2;">
                        Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];

// Add to cart
function addToCart(id, name, price, sku) {
    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: id,
            name: name,
            price: price,
            sku: sku,
            quantity: 1
        });
    }

    updateCart();
}

// Remove from cart
function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCart();
}

// Update quantity
function updateQuantity(id, change) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            updateCart();
        }
    }
}

// Update cart display
function updateCart() {
    const cartItems = $('#cartItems');
    const subtotalEl = $('#subtotal');
    const totalEl = $('#totalAmount');

    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="text-center" style="color: var(--secondary);">Keranjang kosong</p>';
        subtotalEl.textContent = 'Rp 0';
        totalEl.textContent = 'Rp 0';
        return;
    }

    let html = '';
    let total = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        html += `
            <div class="cart-item">
                <div>
                    <strong>${item.name}</strong><br>
                    <small style="color: var(--secondary);">${formatCurrency(item.price)} x ${item.quantity}</small>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="display: flex; gap: 5px;">
                        <button class="btn btn-sm btn-outline" onclick="updateQuantity(${item.id}, -1)">-</button>
                        <span style="padding: 6px 12px;">${item.quantity}</span>
                        <button class="btn btn-sm btn-outline" onclick="updateQuantity(${item.id}, 1)">+</button>
                    </div>
                    <strong>${formatCurrency(itemTotal)}</strong>
                    <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">×</button>
                </div>
            </div>
        `;
    });

    cartItems.innerHTML = html;
    subtotalEl.textContent = formatCurrency(total);
    totalEl.textContent = formatCurrency(total);
}

// Clear cart
function clearCart() {
    if (cart.length === 0 || confirm('Hapus semua item dari keranjang?')) {
        cart = [];
        updateCart();
    }
}

// Process checkout
async function processCheckout() {
    if (cart.length === 0) {
        showAlert('Keranjang masih kosong!', 'danger');
        return;
    }

    const paymentMethod = $('#paymentMethod').value;
    const customerName = $('#customerName').value;

    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    const items = cart.map(item => ({
        product_id: item.id,
        product_name: item.name,
        product_sku: item.sku,
        quantity: item.quantity,
        price: item.price,
        total: item.price * item.quantity
    }));

    try {
        const response = await fetch('../api/process-sale.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                total_amount: total,
                payment_method: paymentMethod,
                customer_name: customerName,
                items: items
            })
        });

        const result = await response.json();

        if (result.success) {
            showAlert('Transaksi berhasil! Invoice: ' + result.invoice, 'success');
            cart = [];
            updateCart();
            $('#customerName').value = '';
        } else {
            showAlert('Gagal memproses transaksi: ' + result.error, 'danger');
        }
    } catch (error) {
        showAlert('Terjadi kesalahan: ' + error.message, 'danger');
    }
}

// Search products
$('#searchProduct').addEventListener('input', function() {
    filterProducts();
});

// Filter by category
$('#filterCategory').addEventListener('change', function() {
    filterProducts();
});

function filterProducts() {
    const searchText = $('#searchProduct').value.toLowerCase();
    const categoryId = $('#filterCategory').value;
    const products = $$('.product-card');

    products.forEach(product => {
        const name = product.querySelector('h4').textContent.toLowerCase();
        const category = product.getAttribute('data-category');

        const matchesSearch = name.includes(searchText);
        const matchesCategory = !categoryId || category === categoryId;

        product.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
    });
}
</script>

<?php include '../includes/footer.php'; ?>
