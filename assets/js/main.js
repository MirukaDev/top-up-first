/**
 * MirukaStore - Main JavaScript
 * Script utama untuk website
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Product Card Selection
    const productCards = document.querySelectorAll('.product-card');
    const selectedProductInput = document.getElementById('selected_product_id');
    
    productCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected from all
            productCards.forEach(c => c.classList.remove('selected'));
            
            // Add selected to clicked
            this.classList.add('selected');
            
            // Update input
            if (selectedProductInput) {
                selectedProductInput.value = this.dataset.productId;
            }
            
            // Update summary
            updateSummary();
            
            // Enable order button
            checkFormValidity();
        });
    });
    
    // Payment Method Selection
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            checkFormValidity();
        });
    });
    
    // Input listeners
    const userIdInput = document.getElementById('user_id');
    const serverIdInput = document.getElementById('server_id');
    
    if (userIdInput) {
        userIdInput.addEventListener('input', function() {
            updateSummary();
            checkFormValidity();
        });
    }
    
    if (serverIdInput) {
        serverIdInput.addEventListener('input', function() {
            updateSummary();
            checkFormValidity();
        });
    }
    
    // Use Balance Toggle
    const useBalanceCheckbox = document.getElementById('use_balance');
    if (useBalanceCheckbox) {
        useBalanceCheckbox.addEventListener('change', function() {
            updateSummary();
        });
    }
    
    // Order Button
    const btnOrder = document.getElementById('btn-order');
    if (btnOrder) {
        btnOrder.addEventListener('click', processOrder);
    }
});

/**
 * Update order summary
 */
function updateSummary() {
    const selectedCard = document.querySelector('.product-card.selected');
    const userId = document.getElementById('user_id')?.value || '-';
    const serverId = document.getElementById('server_id')?.value || '-';
    
    // Update product name
    const summaryProduct = document.getElementById('summary-product');
    if (summaryProduct && selectedCard) {
        const productName = selectedCard.querySelector('.text-white.font-semibold')?.textContent || '-';
        summaryProduct.textContent = productName;
    }
    
    // Update user ID
    const summaryUserId = document.getElementById('summary-user-id');
    if (summaryUserId) {
        summaryUserId.textContent = userId;
    }
    
    // Update server
    const summaryServer = document.getElementById('summary-server');
    if (summaryServer) {
        summaryServer.textContent = serverId || '-';
    }
    
    // Update price
    updatePriceSummary();
}

/**
 * Update price summary
 */
function updatePriceSummary() {
    const selectedCard = document.querySelector('.product-card.selected');
    const summaryPrice = document.getElementById('summary-price');
    const summaryTotal = document.getElementById('summary-total');
    const balanceDiscountRow = document.getElementById('balance-discount-row');
    const summaryBalanceDiscount = document.getElementById('summary-balance-discount');
    const useBalanceCheckbox = document.getElementById('use_balance');
    
    if (!selectedCard) return;
    
    const price = parseInt(selectedCard.dataset.price) || 0;
    let total = price;
    let balanceUsed = 0;
    
    // Check if using balance
    if (useBalanceCheckbox && useBalanceCheckbox.checked && typeof USER_BALANCE !== 'undefined') {
        balanceUsed = Math.min(USER_BALANCE, price);
        total = price - balanceUsed;
    }
    
    // Update displays
    if (summaryPrice) {
        summaryPrice.textContent = 'Rp ' + formatNumber(price);
    }
    
    if (balanceDiscountRow && summaryBalanceDiscount) {
        if (balanceUsed > 0) {
            balanceDiscountRow.style.display = 'flex';
            summaryBalanceDiscount.textContent = '-Rp ' + formatNumber(balanceUsed);
        } else {
            balanceDiscountRow.style.display = 'none';
        }
    }
    
    if (summaryTotal) {
        summaryTotal.textContent = 'Rp ' + formatNumber(total);
    }
}

/**
 * Check form validity
 */
function checkFormValidity() {
    const selectedProduct = document.getElementById('selected_product_id')?.value;
    const userId = document.getElementById('user_id')?.value;
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    const btnOrder = document.getElementById('btn-order');
    
    if (btnOrder) {
        btnOrder.disabled = !(selectedProduct && userId && paymentMethod);
    }
}

/**
 * Process order
 */
function processOrder() {
    const selectedProduct = document.getElementById('selected_product_id')?.value;
    const userId = document.getElementById('user_id')?.value;
    const serverId = document.getElementById('server_id')?.value;
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
    const useBalance = document.getElementById('use_balance')?.checked || false;
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
    
    if (!selectedProduct || !userId || !paymentMethod) {
        showToast('Lengkapi semua data terlebih dahulu', 'error');
        return;
    }
    
    // Show loading
    const loadingModal = document.getElementById('loading-modal');
    if (loadingModal) {
        loadingModal.classList.remove('hidden');
        loadingModal.classList.add('flex');
    }
    
    // Prepare data
    const formData = new FormData();
    formData.append('game_id', GAME_ID);
    formData.append('product_id', selectedProduct);
    formData.append('user_id', userId);
    formData.append('server_id', serverId);
    formData.append('payment_method', paymentMethod);
    formData.append('use_balance', useBalance ? 1 : 0);
    formData.append('csrf_token', csrfToken);
    
    // Send request
    fetch('/api/create-order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading
        if (loadingModal) {
            loadingModal.classList.add('hidden');
            loadingModal.classList.remove('flex');
        }
        
        if (data.success) {
            if (data.snap_token) {
                // Open Midtrans Snap
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        window.location.href = '/order/success?order_id=' + data.order_id;
                    },
                    onPending: function(result) {
                        window.location.href = '/order/success?order_id=' + data.order_id;
                    },
                    onError: function(result) {
                        showToast('Pembayaran gagal', 'error');
                    },
                    onClose: function() {
                        showToast('Pembayaran dibatalkan', 'info');
                    }
                });
            } else if (data.redirect) {
                // Full balance payment
                window.location.href = data.redirect;
            }
        } else {
            showToast(data.message || 'Gagal membuat pesanan', 'error');
        }
    })
    .catch(error => {
        // Hide loading
        if (loadingModal) {
            loadingModal.classList.add('hidden');
            loadingModal.classList.remove('flex');
        }
        
        console.error('Error:', error);
        showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
    });
}

/**
 * Format number to Indonesian format
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Show toast notification
 */
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toast-icon');
    const toastMessage = document.getElementById('toast-message');
    
    if (!toast || !toastIcon || !toastMessage) return;
    
    // Set icon and color based on type
    toastIcon.className = 'fas';
    if (type === 'success') {
        toastIcon.classList.add('fa-check-circle', 'text-green-500');
    } else if (type === 'error') {
        toastIcon.classList.add('fa-exclamation-circle', 'text-red-500');
    } else {
        toastIcon.classList.add('fa-info-circle', 'text-blue-500');
    }
    
    toastMessage.textContent = message;
    
    // Show toast
    toast.classList.remove('translate-y-full', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');
    
    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-y-full', 'opacity-0');
        toast.classList.remove('translate-y-0', 'opacity-100');
    }, 3000);
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Berhasil disalin ke clipboard', 'success');
    }).catch(() => {
        showToast('Gagal menyalin', 'error');
    });
}

/**
 * Confirm action
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// AJAX Helper
const AJAX = {
    get: function(url, callback) {
        fetch(url)
            .then(response => response.json())
            .then(data => callback(null, data))
            .catch(error => callback(error, null));
    },
    
    post: function(url, data, callback) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(data => callback(null, data))
        .catch(error => callback(error, null));
    }
};

// Export functions for global access
window.MirukaStore = {
    showToast,
    formatNumber,
    copyToClipboard,
    confirmAction,
    AJAX
};
