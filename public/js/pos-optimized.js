/* ========================================
   OPTIMIZED POS CART FUNCTIONS
   Performance-focused implementation
   ======================================== */

// ⚡ PERFORMANCE CONFIG
const DEBOUNCE_DELAY = 300; // ms
const ANIMATION_DURATION = 200; // ms
const TOAST_DURATION = 3000; // ms

// 🎯 Debounce Helper (Prevent excessive calls)
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ⚡ Optimized Add to Cart (with request batching prevention)
let pendingRequests = new Set();

async function addToCartOptimized(productId, productName, price, type = 'product', qty = 1, image = null) {
    // Prevent duplicate requests
    const requestKey = `${type}-${productId}`;
    if (pendingRequests.has(requestKey)) {
        console.log('Request already pending, skipping duplicate');
        return;
    }
    
    pendingRequests.add(requestKey);
    
    try {
        // Optimistic UI update (instant feedback)
        showOptimisticUpdate(productName, qty);
        
        const response = await fetch('/pos/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                name: productName,
                price: price,
                qty: qty,
                type: type,
                image: image
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            updateCartUI(data.cart);
            showToast(`✓ ${productName} ditambahkan`, 'success');
        } else {
            // Rollback optimistic update
            showToast('✗ Gagal menambahkan ke keranjang', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('✗ Terjadi kesalahan', 'error');
    } finally {
        pendingRequests.delete(requestKey);
    }
}

// 🎨 Optimistic UI Update (Instant feedback before server responds)
function showOptimisticUpdate(itemName, qty) {
    const cartIcon = document.getElementById('cart-icon-animated');
    if (cartIcon) {
        // Bounce animation (hardware accelerated)
        cartIcon.style.transform = 'translateZ(0) scale(1.2)';
        setTimeout(() => {
            cartIcon.style.transform = 'translateZ(0) scale(1)';
        }, ANIMATION_DURATION);
    }
    
    // Optional: Show temporary item in cart UI
    // This can be implemented for even faster perceived performance
}

// ⚡ Debounced Search
const debouncedSearch = debounce((query) => {
    if (query.length < 2) return; // Don't search for 1 character
    
    fetchFilteredProducts({
        search: query
    });
}, DEBOUNCE_DELAY);

// 🎯 Virtual Scroll Helper (for large product lists)
function initVirtualScroll(container, items, renderItem) {
    const ITEM_HEIGHT = 200; // Approximate item height
    const BUFFER_SIZE = 3; // Items to render outside viewport
    
    let scrollTop = 0;
    let viewportHeight = container.clientHeight;
    
    const visibleRange = () => {
        const start = Math.max(0, Math.floor(scrollTop / ITEM_HEIGHT) - BUFFER_SIZE);
        const end = Math.min(
            items.length,
            Math.ceil((scrollTop + viewportHeight) / ITEM_HEIGHT) + BUFFER_SIZE
        );
        return { start, end };
    };
    
    const render = () => {
        const { start, end } = visibleRange();
        const fragment = document.createDocumentFragment();
        
        for (let i = start; i < end; i++) {
            fragment.appendChild(renderItem(items[i], i));
        }
        
        container.innerHTML = '';
        container.appendChild(fragment);
    };
    
    container.addEventListener('scroll', () => {
        scrollTop = container.scrollTop;
        requestAnimationFrame(render);
    });
    
    render();
}

// 🎨 Smooth Toast with Animation Queue
const toastQueue = [];
let currentToast = null;

function showToast(message, type = 'info') {
    const toast = {
        message,
        type,
        id: Date.now()
    };
    
    toastQueue.push(toast);
    
    if (!currentToast) {
        processToastQueue();
    }
}

function processToastQueue() {
    if (toastQueue.length === 0) {
        currentToast = null;
        return;
    }
    
    const toast = toastQueue.shift();
    currentToast = toast;
    
    const bgColors = {
        success: 'bg-emerald-600',
        error: 'bg-red-600',
        info: 'bg-indigo-600'
    };
    
    const toastEl = document.createElement('div');
    toastEl.id = `toast-${toast.id}`;
    toastEl.className = `fixed top-4 right-4 z-50 ${bgColors[toast.type]} text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium transform transition-all duration-300 translate-x-0`;
    toastEl.textContent = toast.message;
    
    // Entry animation
    toastEl.style.opacity = '0';
    toastEl.style.transform = 'translateX(100%)';
    document.body.appendChild(toastEl);
    
    requestAnimationFrame(() => {
        toastEl.style.opacity = '1';
        toastEl.style.transform = 'translateX(0)';
    });
    
    // Exit animation
    setTimeout(() => {
        toastEl.style.opacity = '0';
        toastEl.style.transform = 'translateX(100%)';
        
        setTimeout(() => {
            toastEl.remove();
            processToastQueue(); // Process next toast
        }, ANIMATION_DURATION);
    }, TOAST_DURATION);
}

// 🔧 Intersection Observer for Lazy Loading
function initLazyLoading() {
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px' // Start loading 50px before entering viewport
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
}

// 🎯 Batch Update Cart Items
function updateCartUI(cart) {
    // Update count immediately
    const countEl = document.getElementById('cart-count');
    if (countEl) countEl.textContent = `${cart.count} item`;
    
    // Update prices immediately
    const subtotalEl = document.getElementById('subtotal-display');
    if (subtotalEl) subtotalEl.textContent = formatCurrency(cart.subtotal);

    const discountEl = document.getElementById('discount-display');
    if (discountEl) discountEl.textContent = `- ${formatCurrency(cart.discount)}`;

    const totalEl = document.getElementById('total-display');
    if (totalEl) totalEl.textContent = formatCurrency(cart.total);
    
    // Trigger Livewire to refresh the complicated cart list
    if (window.Livewire) {
        window.Livewire.dispatch('cartUpdated');
    }
}

// Stub for renderCartItems if needed by other calls, but mainly handled by Livewire now
function renderCartItems(items) {
    // We defer to Livewire for rendering the list to keep logic (edit/delete) consistent
    if (window.Livewire) {
        window.Livewire.dispatch('cartUpdated');
    }
}

// ⚡ Memoized Format Currency (Cache results)
const currencyCache = new Map();

function formatCurrency(amount) {
    if (currencyCache.has(amount)) {
        return currencyCache.get(amount);
    }
    
    const formatted = 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    currencyCache.set(amount, formatted);
    
    // Clear cache if it gets too large
    if (currencyCache.size > 100) {
        currencyCache.clear();
    }
    
    return formatted;
}

// 🎨 Request Animation Frame for Smooth Updates
function smoothUpdateElement(element, property, value) {
    requestAnimationFrame(() => {
        element[property] = value;
    });
}

// 🔧 Performance Monitor (Dev only)
if (window.location.hostname === 'localhost') {
    const perfObserver = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            console.log(`${entry.name}: ${entry.duration.toFixed(2)}ms`);
        }
    });
    
    perfObserver.observe({ entryTypes: ['measure', 'navigation'] });
}

// ⚡ Export optimized functions
window.POS = {
    addToCart: addToCartOptimized,
    search: debouncedSearch,
    showToast,
    updateCartUI,
    formatCurrency,
    initLazyLoading
};

// 🔧 Global Assignments for Blade Inline Events (Backward Compatibility)
window.addToCart = addToCartOptimized;
window.formatCurrency = formatCurrency;
window.showToast = showToast;

// 🚀 Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('⚡ POS Optimizations loaded');
    initLazyLoading();
});
