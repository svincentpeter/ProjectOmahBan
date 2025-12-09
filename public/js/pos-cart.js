/**
 * POS Cart Management - Omah Ban
 * Temporary client-side cart before backend integration
 */

// Cart state in localStorage
const CART_STORAGE_KEY = 'omah_ban_pos_cart';

// Get cart from storage
function getCart() {
    const cart = localStorage.getItem(CART_STORAGE_KEY);
    return cart ? JSON.parse(cart) : {
        items: [],
        subtotal: 0,
        discount: 0,
        total: 0
    };
}

// Save cart to storage
function saveCart(cart) {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
}

// Add to cart
function addToCart(productId, productName, price, type = 'product') {
    const cart = getCart();
    
    // Check if item exists
    const existingIndex = cart.items.findIndex(item => 
        item.product_id === productId && item.type === type
    );
    
    if (existingIndex >= 0) {
        // Increment quantity
        cart.items[existingIndex].qty += 1;
    } else {
        // Add new item
        cart.items.push({
            id: Date.now().toString(), // Temporary ID
            product_id: productId,
            name: productName,
            price: price,
            qty: 1,
            type: type,
            price_original: price,
            price_adjusted: price
        });
    }
    
    // Recalculate
    recalculateCart(cart);
    saveCart(cart);
    
    // Update UI
    renderCart();
    
    // Show feedback
    showToast(`✓ ${productName} ditambahkan ke keranjang`);
}

// Recalculate cart totals
function recalculateCart(cart) {
    cart.subtotal = cart.items.reduce((sum, item) => {
        return sum + ((item.price_adjusted || item.price) * item.qty);
    }, 0);
    
    cart.total = cart.subtotal - cart.discount;
}

// Render cart UI
function renderCart() {
    const cart = getCart();
    
    // Update count
    const countEl = document.getElementById('cart-count');
    if (countEl) {
        countEl.textContent = cart.items.length + ' item';
    }
    
    // Update totals
    const subtotalEl = document.getElementById('subtotal-display');
    if (subtotalEl) {
        subtotalEl.textContent = formatCurrency(cart.subtotal);
    }
    
    const totalEl = document.getElementById('total-display');
    if (totalEl) {
        totalEl.textContent = formatCurrency(cart.total);
    }
    
    // TODO: Render cart items list
}

// Format currency
function formatCurrency(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID');
}

// Show toast notification
function showToast(message) {
    // Simple alert for now, replace with proper toast later
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 z-50 bg-emerald-600 text-white px-4 py-2 rounded-lg shadow-lg';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 2000);
}

// Init on load
document.addEventListener('DOMContentLoaded', function() {
    renderCart();
});
