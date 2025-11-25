// Main JavaScript for POS & CMS System
document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initPOS();
});

// Sidebar functionality
function initSidebar() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            } else {
                sidebar.classList.toggle('show');
            }
        });
    }
}

// POS System functionality
function initPOS() {
    window.cart = [];

    window.addToCart = function(productId, name, price) {
        const existingItem = cart.find(item => item.id === productId);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: productId,
                name: name,
                price: parseFloat(price),
                quantity: 1
            });
        }

        updateCartDisplay();
    };

    window.removeFromCart = function(productId) {
        cart = cart.filter(item => item.id !== productId);
        updateCartDisplay();
    };

    window.updateQuantity = function(productId, quantity) {
        const item = cart.find(item => item.id === productId);
        if (item) {
            item.quantity = parseInt(quantity);
            if (item.quantity <= 0) {
                removeFromCart(productId);
            } else {
                updateCartDisplay();
            }
        }
    };

    function updateCartDisplay() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');

    if (!cartItemsContainer) return;

    cartItemsContainer.innerHTML = '';
    let total = 0;

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="text-center text-muted">Keranjang kosong</p>';
        if (checkoutBtn) checkoutBtn.disabled = true;
    } else {
        cart.forEach(item => {
            total += item.price * item.quantity;

            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item d-flex justify-content-between align-items-center mb-2';
            cartItem.innerHTML = `
                <div>
                    <strong>${item.name}</strong><br>
                    <small class="text-muted">${formatCurrency(item.price)} x ${item.quantity}</small>
                </div>
                <div>
                    <input type="number" value="${item.quantity}" min="1" style="width: 60px;"
                           onchange="updateQuantity(${item.id}, this.value)">
                    <button onclick="removeFromCart(${item.id})">×</button>
                </div>
            `;
            cartItemsContainer.appendChild(cartItem);
        });

        if (checkoutBtn) checkoutBtn.disabled = false;
    }

    if (cartTotalElement) {
        cartTotalElement.textContent = formatCurrency(total);
    }
}


    window.clearCart = function() {
        cart = [];
        updateCartDisplay();
    };
}

function formatCurrency(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

function searchProducts(searchTerm) {
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        const productName = card.textContent.toLowerCase();
        card.style.display = productName.includes(searchTerm.toLowerCase()) ? 'block' : 'none';
    });
}
