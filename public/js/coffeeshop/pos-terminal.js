(() => {
    const openTabBtn = document.getElementById('open-tab-btn');
    if (!openTabBtn || openTabBtn.dataset.initialized) {
        return;
    }
    openTabBtn.dataset.initialized = 'true';

    const config = window.posConfig;
    let tabs = [...(config.initialTabs || [])];
    const requestedTabId = Number.parseInt(new URLSearchParams(window.location.search).get('tab_id') || '', 10);
    let activeTabId = Number.isFinite(requestedTabId) && tabs.some(tab => tab.tab_id === requestedTabId)
        ? requestedTabId
        : (tabs.length ? tabs[0].tab_id : null);
    let activeCategory = 'all';
    let searchTimer = null;
    let checkedInGuests = [];
    let previouslyPendingCancelTabIds = tabs.map(t => ({
        tab_id: t.tab_id,
        tab_name: t.tab_name,
        pending_cancel_request: t.pending_cancel_request
    }));
    let currentProducts = [];

    const alertBox = document.getElementById('pos-alert');
    const productGrid = document.getElementById('product-grid');
    const searchInput = document.getElementById('product-search');
    const tabSwitcher = document.getElementById('tab-switcher');
    const newTabFormContainer = document.getElementById('new-tab-form-container');
    const activeTabPanel = document.getElementById('active-tab-panel');
    const noTabMessage = document.getElementById('no-tab-message');
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const activeTabName = document.getElementById('active-tab-name');
    const activeTabTotal = document.getElementById('active-tab-total');
    const activeTabBadge = document.getElementById('active-tab-badge');
    const activeTabPendingAlert = document.getElementById('active-tab-pending-alert');
    const itemNotesInput = document.getElementById('item-notes');

    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const paymentModalAmount = document.getElementById('payment-modal-amount');

    const cancelTabModal = new bootstrap.Modal(document.getElementById('cancelTabModal'));
    const cancelTabWarningText = document.getElementById('cancel-tab-warning-text');
    const cancelReasonContainer = document.getElementById('cancel-reason-container');
    const cancelReasonInput = document.getElementById('cancel-reason');

    const confirmRoomChargeModal = new bootstrap.Modal(document.getElementById('confirmRoomChargeModal'));
    const posAlertModal = new bootstrap.Modal(document.getElementById('posAlertModal'));
    const posAlertModalMessage = document.getElementById('pos-alert-modal-message');

    function showPosAlert(message) {
        posAlertModalMessage.textContent = message;
        posAlertModal.show();
    }

    async function confirmItemRemoval() {
        if (window.Swal) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to remove this item from the cart?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                focusCancel: true,
                reverseButtons: true,
                customClass: {
                    actions: 'gap-2',
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });
            return result.isConfirmed;
        }
        return confirm('Are you sure you want to remove this item from the cart?');
    }

    // Tab Type inputs
    const walkinPanel = document.getElementById('new-tab-walkin-panel');
    const roomPanel = document.getElementById('new-tab-room-panel');
    const tabGuestSelect = document.getElementById('new-tab-guest');

    function showAlert(message, type = 'success') {
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
        setTimeout(() => alertBox.classList.add('d-none'), 4000);
    }

    function showSuccess(message) {
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                background: '#ffffff',
            });
            return;
        }

        showAlert(message, 'success');
    }

    async function request(url, options = {}) {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.csrfToken,
                ...(options.headers || {}),
            },
            ...options,
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || 'Request failed.');
        }
        return data;
    }

    function formatMoney(value) {
        return `₱${Number(value).toFixed(2)}`;
    }

    function getActiveTab() {
        return tabs.find(tab => tab.tab_id === activeTabId) || null;
    }

    function updateTabTypeStyles() {
        document.querySelectorAll('input[name="new-tab-type"]').forEach(radio => {
            const label = document.querySelector(`label[for="${radio.id}"]`);
            if (!label) return;
            if (radio.checked) {
                label.style.backgroundColor = '#334c42';
                label.style.borderColor = '#334c42';
                label.style.color = '#ffffff';
            } else {
                label.style.backgroundColor = 'transparent';
                label.style.borderColor = '#c2a889';
                label.style.color = '#827567';
            }
        });
    }

    document.querySelectorAll('input[name="new-tab-type"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            updateTabTypeStyles();
            const val = e.target.value;
            walkinPanel.classList.add('d-none');
            roomPanel.classList.add('d-none');
            document.getElementById('new-tab-account-panel').classList.add('d-none');

            if (val === 'room') {
                roomPanel.classList.remove('d-none');
                loadGuests();
            } else if (val === 'account') {
                document.getElementById('new-tab-account-panel').classList.remove('d-none');
            } else {
                walkinPanel.classList.remove('d-none');
            }
        });
    });

    function renderTabs() {
        if (activeTabId !== null && !tabs.some(tab => tab.tab_id === activeTabId)) {
            activeTabId = tabs.length > 0 ? tabs[0].tab_id : null;
        }

        tabSwitcher.innerHTML = '';

        // "+ New Customer" button
        const newBtn = document.createElement('button');
        newBtn.type = 'button';
        newBtn.className = 'btn btn-sm w-100 py-2 font-brand fw-bold shadow-sm';
        newBtn.style.cssText = activeTabId === null
            ? "background: #334c42; color: #ffffff; border: 1px solid #334c42; font-family: 'Lucida Fax', 'Georgia', serif;"
            : "border: 1px solid #334c42; color: #334c42; background: transparent; font-family: 'Lucida Fax', 'Georgia', serif;";
        newBtn.innerHTML = '<i class="fa-solid fa-plus me-1"></i>New Customer';
        newBtn.addEventListener('click', () => {
            activeTabId = null;
            renderTabs();
        });
        tabSwitcher.appendChild(newBtn);

        tabs.forEach(tab => {
            const btn = document.createElement('button');
            btn.type = 'button';

            const isActive = tab.tab_id === activeTabId;
            let styleStr = "font-family: 'Lucida Fax', 'Georgia', serif; font-size: 0.88rem; width: 100%; text-align: center;";
            if (tab.tab_type === 'room') {
                styleStr += isActive ? ' background: #627e71; color: #ffffff; border: 1px solid #627e71;' : ' border: 1px solid #c2a889; color: #504538; background: transparent;';
            } else if (tab.tab_type === 'account') {
                styleStr += isActive ? ' background: #504538; color: #ffffff; border: 1px solid #504538;' : ' border: 1px solid #c2a889; color: #504538; background: transparent;';
            } else {
                styleStr += isActive ? ' background: #504538; color: #ffffff; border: 1px solid #504538;' : ' border: 1px solid #c2a889; color: #504538; background: transparent;';
            }

            btn.className = 'btn btn-sm py-2 font-brand fw-semibold shadow-sm';
            btn.style.cssText = styleStr;
            const prefix = (tab.tab_type === 'account' && !tab.tab_name.includes('👑')) ? '👑 ' : '';
            btn.textContent = `${prefix}${tab.tab_name} (${formatMoney(tab.total)})`;
            btn.addEventListener('click', () => {
                activeTabId = tab.tab_id;
                renderTabs();
                renderCart();
            });
            tabSwitcher.appendChild(btn);
        });

        const footerSummary = document.getElementById('active-tab-footer-summary');
        const noTabFooterSummary = document.getElementById('no-tab-footer-summary');

        if (activeTabId === null) {
            activeTabPanel.classList.add('d-none');
            activeTabPanel.classList.remove('d-flex');
            newTabFormContainer.classList.remove('d-none');
            noTabMessage.classList.add('d-none');
            if (footerSummary) footerSummary.classList.add('d-none');
            if (noTabFooterSummary) noTabFooterSummary.classList.remove('d-none');
        } else {
            newTabFormContainer.classList.add('d-none');
            activeTabPanel.classList.remove('d-none');
            activeTabPanel.classList.add('d-flex');
            noTabMessage.classList.add('d-none');
            if (footerSummary) footerSummary.classList.remove('d-none');
            if (noTabFooterSummary) noTabFooterSummary.classList.add('d-none');
            renderCart();
        }

        const openTabsCounter = document.getElementById('open-tabs-counter');
        if (openTabsCounter) {
            openTabsCounter.textContent = tabs.length;
        }
    }

    function renderCart() {
        const tab = getActiveTab();
        if (!tab) return;

        activeTabName.textContent = tab.tab_name;
        if (activeTabTotal) {
            activeTabTotal.textContent = formatMoney(tab.total);
        }
        cartTotal.textContent = formatMoney(tab.total);
        cartItems.innerHTML = '';

        const walkinActions = document.getElementById('walkin-checkout-actions');
        const roomActions = document.getElementById('room-checkout-actions');
        const accountActions = document.getElementById('account-checkout-actions');
        const pendingAlert = document.getElementById('active-tab-pending-alert');
        const allButtons = document.querySelectorAll('.checkout-action-btn');

        walkinActions.classList.add('d-none');
        roomActions.classList.add('d-none');
        accountActions.classList.add('d-none');

        const chargeRoomBtn = document.getElementById('charge-room-btn');
        const chargeAccountBtn = document.getElementById('charge-account-btn');

        if (tab.tab_type === 'room') {
            roomActions.classList.remove('d-none');
            const roomLabel = tab.room_number ? `Room ${tab.room_number}` : 'Room';
            const guestLabel = tab.guest_name ? `Charge to ${tab.guest_name}` : `Charge to ${roomLabel}`;
            if (chargeRoomBtn) {
                chargeRoomBtn.innerHTML = `<i class="fa-solid fa-user me-1"></i> ${guestLabel}`;
            }
            activeTabBadge.innerHTML = `<span class="badge px-2.5 py-1 fs-7 fw-semibold" style="background: rgba(98, 126, 113, 0.12); color: #334c42; border: 1px solid #627e71; font-family: 'Lucida Fax', serif;">Room Charge (${roomLabel})</span>`;
        } else if (tab.tab_type === 'account') {
            accountActions.classList.remove('d-none');
            if (chargeAccountBtn) {
                chargeAccountBtn.innerHTML = `<i class="fa-solid fa-crown me-1" style="color: #c2a889;"></i> Charge to VIP Account (${tab.credit_account_name || 'Account'})`;
            }
            activeTabBadge.innerHTML = `<span class="badge px-2.5 py-1 fs-7 fw-bold shadow-sm" style="background: #504538; color: #ffffff; border: 1px solid #504538; font-family: 'Lucida Fax', serif;"><i class="fa-solid fa-crown me-1" style="color: #c2a889;"></i> VIP (${tab.credit_account_name || 'N/A'})</span>`;
            const creditAlert = document.getElementById('active-tab-credit-alert');
            if (creditAlert) creditAlert.remove();
        } else {
            walkinActions.classList.remove('d-none');
            activeTabBadge.innerHTML = `<span class="badge px-2.5 py-1 fs-7 fw-semibold" style="background: #504538; color: #ffffff; font-family: 'Lucida Fax', serif;">Walk-in</span>`;
            const creditAlert = document.getElementById('active-tab-credit-alert');
            if (creditAlert) creditAlert.remove();
        }

        const discountBadge = document.getElementById('active-tab-discount-badge');
        if (tab.discount_amount > 0) {
            discountBadge.classList.remove('d-none');
            const dStr = tab.is_discount_percentage ? `${tab.discount_amount}%` : formatMoney(tab.discount_amount);
            discountBadge.textContent = `${tab.discount_type} Discount: -${dStr}`;
        } else {
            discountBadge.classList.add('d-none');
        }

        document.getElementById('cart-subtotal').textContent = formatMoney(tab.subtotal);
        if (tab.discount_amount > 0) {
            document.getElementById('cart-discount').textContent = '-' + formatMoney(tab.subtotal - tab.total);
        } else {
            document.getElementById('cart-discount').textContent = '-₱0.00';
        }

        if (itemNotesInput) {
            itemNotesInput.value = tab.notes || '';
            itemNotesInput.disabled = !!tab.pending_cancel_request;
        }

        // Handle pending cancellation state
        if (tab.pending_cancel_request) {
            pendingAlert.classList.remove('d-none');
            allButtons.forEach(btn => btn.disabled = true);
        } else {
            pendingAlert.classList.add('d-none');
            allButtons.forEach(btn => btn.disabled = false);
        }

        if (!tab.items.length) {
            cartItems.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fa-solid fa-cart-shopping fa-lg mb-2"></i>
                    <div>No items yet</div>
                </div>
            `;
            return;
        }

        tab.items.forEach(item => {
            const row = document.createElement('div');
            row.className = 'cart-row mb-3 py-1';

            row.innerHTML = `
                <!-- Col 1: Item details -->
                <div class="item-details pe-1">
                    <div class="fw-semibold text-dark text-truncate" style="font-size: 1.02rem; line-height: 1.25;" title="${item.name}">${item.name}</div>
                    <div class="text-muted" style="font-size: 0.84rem;">
                        ${formatMoney(item.unit_price)} each
                    </div>
                </div>

                <!-- Col 2: Quantity controls (Perfectly Straight Column) -->
                <div class="d-flex align-items-center gap-1 justify-content-center">
                    <button type="button"
                        class="btn btn-outline-secondary btn-sm qty-btn rounded-3 d-inline-flex align-items-center justify-content-center p-0 fw-bold"
                        style="width: 32px; height: 32px; font-size: 0.98rem;"
                        data-action="decrease"
                        data-item-id="${item.tab_item_id}"
                        ${tab.pending_cancel_request ? 'disabled' : ''}>
                        -
                    </button>

                    <input type="number"
                        class="form-control form-control-sm text-center qty-input px-1 fw-bold"
                        style="width: 48px; height: 32px; font-size: 0.95rem;"
                        value="${item.quantity}"
                        min="1"
                        data-item-id="${item.tab_item_id}"
                        ${tab.pending_cancel_request ? 'disabled' : ''} />

                    <button type="button"
                        class="btn btn-outline-secondary btn-sm qty-btn rounded-3 d-inline-flex align-items-center justify-content-center p-0 fw-bold"
                        style="width: 32px; height: 32px; font-size: 0.98rem;"
                        data-action="increase"
                        data-item-id="${item.tab_item_id}"
                        ${tab.pending_cancel_request ? 'disabled' : ''}>
                        +
                    </button>
                </div>

                <!-- Col 3: Line total price -->
                <div class="text-end fw-bold text-brown" style="font-size: 1.02rem; min-width: 70px;">
                    ${formatMoney(item.line_total)}
                </div>

                <!-- Col 4: Delete button -->
                <div class="text-end">
                    <button type="button"
                        class="btn btn-link btn-sm text-danger p-1 remove-item-btn"
                        data-item-id="${item.tab_item_id}"
                        ${tab.pending_cancel_request ? 'disabled' : ''}>
                        <i class="fa-solid fa-trash" style="font-size: 0.98rem;"></i>
                    </button>
                </div>
            `;

            cartItems.appendChild(row);
        });

        async function updateQty(itemId, delta = 0, directValue = null) {
            const item = tab.items.find(i => String(i.tab_item_id) === String(itemId));
            if (!item) return;

            let newQty;

            if (directValue !== null) {
                newQty = parseInt(directValue);
            } else {
                newQty = item.quantity + delta;
            }

            if (isNaN(newQty) || newQty < 1) newQty = 1;

            await updateItemQuantity(itemId, newQty);
        }

        cartItems.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const itemId = btn.dataset.itemId;
                const delta = btn.dataset.action === 'increase' ? 1 : -1;

                await updateQty(itemId, delta);
            });
        });
        cartItems.querySelectorAll('.qty-input').forEach(input => {

            const apply = async () => {
                const itemId = input.dataset.itemId;
                await updateQty(itemId, 0, input.value);
            };

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    apply();
                }
            });

            input.addEventListener('blur', () => {
                apply();
            });
        });


        cartItems.querySelectorAll('.remove-item-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (await confirmItemRemoval()) {
                    await updateItemQuantity(btn.dataset.itemId, 0);
                }
            });
        });
    }

    function renderProducts(products) {
        currentProducts = products;
        productGrid.innerHTML = '';
        if (!products.length) {
            productGrid.innerHTML = '<div class="col-12 text-muted">No products found.</div>';
            return;
        }

        products.forEach(product => {
            const col = document.createElement('div');
            col.className = 'col-md-3 col-6 product-tile';
            const isOutOfStock = product.is_stockable && product.stock_quantity <= 0;
            col.innerHTML = `
            <div class="coffeeshop-card h-100 ${isOutOfStock ? 'opacity-50' : ''}">
                <div class="card-body d-flex flex-column text-center">

                    ${product.image_url
                    ? `<div class="mb-3 rounded w-100 overflow-hidden" style="height: 120px;"><img src="${product.image_url}" class="w-100 h-100" style="object-fit: cover;" alt="${product.name}"></div>`
                    : `<div class="mb-3 rounded w-100 bg-light d-flex align-items-center justify-content-center" style="height: 120px;"><i class="fa-solid fa-image fa-2x text-secondary opacity-50"></i></div>`}

                    <div class="fw-semibold">${product.name}</div>

                    <small class="text-muted d-block" style="min-height:48px;">
                        ${product.description || ''}
                    </small>

                    <small class="text-muted mb-2">
                        ${product.category || ''}
                    </small>

                    <div class="fw-bold" style="color: #504538; font-family: 'Lucida Fax', 'Georgia', serif; font-size: 1.05rem;">
                        ${formatMoney(product.price)}
                    </div>

                    <small class="${!product.is_stockable ? 'text-success' : (isOutOfStock ? 'text-danger' : '')}" style="${!product.is_stockable || isOutOfStock ? '' : 'color: #827567;'} font-family: 'Lucida Fax', serif;">
                        ${!product.is_stockable ? 'Available' : (isOutOfStock ? 'Sold out' : `Stock: ${product.stock_quantity}`)}
                    </small>

                    <button
                        type="button"
                        class="btn text-white w-100 mt-auto add-product-btn fw-bold"
                        style="background-color: #334c42; border: none; font-family: 'Lucida Fax', 'Georgia', serif;"
                        data-product-id="${product.product_id}"
                        ${isOutOfStock ? 'disabled' : ''}>
                        ADD
                    </button>

                </div>
            </div>
            `;
            productGrid.appendChild(col);
        });

        bindProductButtons();
    }


    async function loadProducts() {
        const params = new URLSearchParams();
        const query = searchInput.value.trim();
        if (query) params.set('q', query);
        if (activeCategory !== 'all') params.set('category_id', activeCategory);

        const data = await request(`${config.routes.search}?${params.toString()}`);
        renderProducts(data.products || []);
    }

    function bindProductButtons() {
        document.querySelectorAll('.add-product-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!activeTabId) {
                    showAlert('Open a tab first.', 'warning');
                    return;
                }
                const tab = getActiveTab();
                if (tab && tab.pending_cancel_request) {
                    showAlert('Cannot add items to a tab with pending cancellation.', 'danger');
                    return;
                }
                try {
                    const data = await request(`${config.routes.tabItems}/${activeTabId}/items`, {
                        method: 'POST',
                        body: JSON.stringify({ product_id: btn.dataset.productId, quantity: 1 }),
                    });
                    replaceTab(data.tab);
                    // Notification removed as per user request to keep UI clean
                    loadProducts();
                } catch (error) {
                    showAlert(error.message, 'danger');
                }
            });
        });
    }
    function bindPairingButtons() {

        document.querySelectorAll('.pairing-btn').forEach(btn => {
            btn.addEventListener('click', async () => {

                if (!activeTabId) {
                    showAlert('Open a tab first.', 'warning');
                    return;
                }

                const tab = getActiveTab();
                if (tab && tab.pending_cancel_request) {
                    showAlert('Cannot add items to a tab with pending cancellation.', 'danger');
                    return;
                }

                // ✅ USE DATA ATTRIBUTE (NOT TEXT)
                const items = (btn.dataset.items || btn.textContent)
                    .split(',')
                    .map(i => i.trim().toLowerCase());

                const productMap = new Map(
                    currentProducts.map(p => [p.name.trim().toLowerCase(), p])
                );

                let added = [];
                let failed = [];

                for (const itemName of items) {

                    const product = currentProducts.find(p =>
                        p.name.trim().toLowerCase().includes(itemName)
                    );

                    if (!product) {
                        failed.push(`${itemName} (not found)`);
                        continue;
                    }

                    try {
                        const data = await request(
                            `${config.routes.tabItems}/${activeTabId}/items`,
                            {
                                method: 'POST',
                                body: JSON.stringify({
                                    product_id: product.product_id,
                                    quantity: 1
                                }),
                            }
                        );

                        replaceTab(data.tab);
                        added.push(product.name);

                    } catch (error) {
                        failed.push(`${product.name} (error)`);
                    }
                }

                loadProducts();

            });
        });
    }
    function replaceTab(updatedTab) {
        const index = tabs.findIndex(tab => tab.tab_id === updatedTab.tab_id);
        if (index >= 0) {
            tabs[index] = updatedTab;
        } else {
            tabs.unshift(updatedTab);
            activeTabId = updatedTab.tab_id;
        }
        renderTabs();
    }
    function showReceipt(data) {
        const receiptEl = document.getElementById('receipt-content');

        const order = data.order || {};
        const items = order.items || [];

        const itemsHtml = items.length
            ? items.map(item => `
                <tr>
                    <td>${item.product_name || item.name || 'Item'}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-end">₱${parseFloat(item.price).toFixed(2)}</td>
                </tr>
            `).join('')
            : `<tr><td colspan="3" class="text-center text-muted">No items</td></tr>`;

        receiptEl.dataset.orderNumber = order.order_number || '';
        receiptEl.innerHTML = `
        <div class="receipt">

            <!-- STORE HEADER -->
            <div class="text-center mb-2">
                <div style="font-size:18px; font-weight:bold; letter-spacing:1px;">
                    COFFEE SHOP
                </div>
                <div style="font-size:12px;">POS RECEIPT</div>
                <div style="font-size:11px; margin-top:4px;">
                    ${(window.currentServerTime || new Date()).toLocaleString()}
                </div>
                <div style="font-size:11px; margin-top:4px;">
                    Order #${order.order_number || ''}
                </div>
            </div>

            <div style="border-top:1px dashed #000; margin:8px 0;"></div>

            <!-- CUSTOMER -->
            <div style="font-size:12px;">
                <strong>Customer:</strong> ${order.customer_name || 'Walk-in'}
            </div>

            <div style="border-top:1px dashed #000; margin:8px 0;"></div>

            <!-- ITEMS -->
            <div style="font-size:12px;">
                ${items.map(item => `
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <div style="width:55%;">
                            ${item.product_name || item.name || 'Item'}
                        </div>
                        <div style="width:15%; text-align:center;">
                            x${item.quantity}
                        </div>
                        <div style="width:30%; text-align:right;">
                            ₱${parseFloat(item.line_total || (item.quantity * item.price)).toFixed(2)}
                        </div>
                    </div>
                `).join('')}
            </div>

            <div style="border-top:1px dashed #000; margin:8px 0;"></div>
            
            ${order.discount_amount > 0 ? `
            <div style="display:flex; justify-content:space-between; font-size:12px; color: #555;">
                <div>Discount</div>
                <div>-₱${parseFloat(order.discount_amount).toFixed(2)}</div>
            </div>
            ` : ''}

            <!-- TOTAL -->
            <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:14px;">
                <div>TOTAL</div>
                <div>₱${parseFloat(order.total || 0).toFixed(2)}</div>
            </div>

            <div style="border-top:1px dashed #000; margin:8px 0;"></div>

            <!-- FOOTER -->
            <div class="text-center" style="font-size:11px;">
                Thank you for your purchase!<br>
                Please come again ☕
            </div>

        </div>
        `;

        const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
        modal.show();
    }

    async function updateItemQuantity(itemId, quantity) {
        try {
            const data = await request(`${config.routes.tabItems}/${activeTabId}/items/${itemId}`, {
                method: 'PATCH',
                body: JSON.stringify({ quantity }),
            });
            replaceTab(data.tab);
            loadProducts();
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    }

    async function refreshTabs() {
        const data = await request(config.routes.tabs);
        const newTabs = data.tabs || [];

        // Track status changes
        previouslyPendingCancelTabIds.forEach(oldTab => {
            const match = newTabs.find(t => t.tab_id === oldTab.tab_id);
            if (!match) {
                if (oldTab.pending_cancel_request) {
                    showAlert(`Cancellation request for "${oldTab.tab_name}" was APPROVED by the Admin.`, 'success');
                    loadProducts();
                }
            } else if (oldTab.pending_cancel_request && !match.pending_cancel_request) {
                showAlert(`Cancellation request for "${oldTab.tab_name}" was REJECTED by the Admin.`, 'danger');
            }
        });

        previouslyPendingCancelTabIds = newTabs.map(t => ({
            tab_id: t.tab_id,
            tab_name: t.tab_name,
            pending_cancel_request: t.pending_cancel_request
        }));

        tabs = newTabs;
        renderTabs();
    }

    async function loadGuests() {
        tabGuestSelect.innerHTML = '<option value="">Loading guests...</option>';
        try {
            const data = await request(config.routes.guests);
            checkedInGuests = data.guests || [];
            tabGuestSelect.innerHTML = '<option value="">Select occupied room...</option>';
            checkedInGuests.forEach(guest => {
                const option = document.createElement('option');
                option.value = JSON.stringify({
                    booking_id: guest.booking_id,
                    folio_id: guest.folio_id,
                    room_id: guest.room_id,
                    guest_id: guest.guest_id,
                    room_number: guest.room_number,
                    guest_name: guest.guest_name,
                });
                option.textContent = `Room ${guest.room_number} - ${guest.guest_name} (Folio Bal: ${formatMoney(guest.balance)})`;
                tabGuestSelect.appendChild(option);
            });
        } catch (error) {
            tabGuestSelect.innerHTML = '<option value="">Error loading guests</option>';
        }
    }

    const categoryFiltersContainer = document.getElementById('category-filters');
    const updateCategoryButtonsStyle = (selectedBtn) => {
        document.querySelectorAll('.category-btn').forEach(b => {
            b.classList.remove('active');
            b.style.backgroundColor = 'transparent';
            b.style.borderColor = '#c2a889';
            b.style.color = '#827567';
        });
        if (selectedBtn) {
            selectedBtn.classList.add('active');
            selectedBtn.style.backgroundColor = '#334c42';
            selectedBtn.style.borderColor = '#334c42';
            selectedBtn.style.color = '#ffffff';
        }
    };

    if (categoryFiltersContainer) {
        categoryFiltersContainer.addEventListener('click', (e) => {
            const btn = e.target.closest('.category-btn');
            if (btn) {
                updateCategoryButtonsStyle(btn);
                activeCategory = btn.dataset.category || 'all';
                loadProducts();
            }
        });
    } else {
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                updateCategoryButtonsStyle(btn);
                activeCategory = btn.dataset.category || 'all';
                loadProducts();
            });
        });
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadProducts, 250);
    });

    let notesSaveTimer = null;
    if (itemNotesInput) {
        itemNotesInput.addEventListener('input', () => {
            const tab = getActiveTab();
            if (!tab || tab.pending_cancel_request) return;
            const newNotes = itemNotesInput.value;
            tab.notes = newNotes;

            if (notesSaveTimer) clearTimeout(notesSaveTimer);
            notesSaveTimer = setTimeout(async () => {
                try {
                    await request(`/coffeeshop/api/tabs/${tab.tab_id}/notes`, {
                        method: 'PUT',
                        body: JSON.stringify({ notes: newNotes }),
                    });
                } catch (err) {
                    console.error('Error auto-saving item notes:', err);
                }
            }, 400);
        });
    }

    document.getElementById('open-tab-btn').addEventListener('click', async () => {
        const btn = document.getElementById('open-tab-btn');
        if (btn.disabled) return;

        const tabType = document.querySelector('input[name="new-tab-type"]:checked').value;
        const payload = { tab_type: tabType };

        if (tabType === 'room') {
            const selected = tabGuestSelect.value;
            if (!selected) {
                showAlert('Please select an occupied room.', 'warning');
                return;
            }
            const guest = JSON.parse(selected);
            payload.booking_id = guest.booking_id;
            payload.folio_id = guest.folio_id;
            payload.room_id = guest.room_id;
            payload.guest_id = guest.guest_id;
            payload.tab_name = `Room ${guest.room_number} - ${guest.guest_name}`;
        } else if (tabType === 'account') {
            const accSelect = document.getElementById('new-tab-account');
            const accountId = accSelect.value;
            if (!accountId) {
                showAlert('Please select a credit account.', 'warning');
                return;
            }
            payload.credit_account_id = accountId;
            payload.tab_name = accSelect.options[accSelect.selectedIndex].text.split(' (')[0];
        } else {
            const tabName = document.getElementById('new-tab-name').value.trim();
            if (!tabName) {
                showAlert('Enter a customer name.', 'warning');
                return;
            }
            payload.tab_name = tabName;
        }

        try {
            btn.disabled = true;
            const data = await request(config.routes.storeTab, {
                method: 'POST',
                body: JSON.stringify(payload),
            });

            tabs.unshift(data.tab);
            activeTabId = data.tab.tab_id;
            document.getElementById('new-tab-name').value = '';
            renderTabs();
            showAlert(data.message);
        } catch (error) {
            showAlert(error.message, 'danger');
        } finally {
            btn.disabled = false;
        }
    });

    // Opening the Cancellation Modal instead of alerts/prompts
    document.getElementById('cancel-tab-btn').addEventListener('click', () => {
        const tab = getActiveTab();
        if (!tab) return;

        cancelReasonInput.value = '';
        const isEmpty = tab.items.length === 0;
        const isAdmin = config.isAdmin;

        if (isEmpty) {
            cancelTabWarningText.textContent = `Are you sure you want to cancel the empty tab "${tab.tab_name}"?`;
            cancelReasonContainer.classList.add('d-none');
        } else if (isAdmin) {
            cancelTabWarningText.textContent = `Are you sure you want to cancel the tab "${tab.tab_name}"? All added items will be returned to stock. (Manager Override)`;
            cancelReasonContainer.classList.add('d-none');
        } else {
            cancelTabWarningText.textContent = `Tab "${tab.tab_name}" contains items. Cancelling it requires Admin Authorization. Please state the reason for this request below:`;
            cancelReasonContainer.classList.remove('d-none');
        }

        cancelTabModal.show();
    });

    // Confirm Cancellation from Modal
    document.getElementById('confirm-cancel-tab-btn').addEventListener('click', async () => {
        const btn = document.getElementById('confirm-cancel-tab-btn');
        if (btn.disabled) return;

        const tab = getActiveTab();
        if (!tab) return;

        const isEmpty = tab.items.length === 0;
        const isAdmin = config.isAdmin;
        const payload = {};

        if (!isEmpty && !isAdmin) {
            const reason = cancelReasonInput.value.trim();
            if (!reason) {
                showPosAlert('A reason is required to submit the cancellation request.');
                return;
            }
            payload.reason = reason;
        }

        try {
            btn.disabled = true;
            cancelTabModal.hide();
            const data = await request(`${config.routes.cancelTab}/${activeTabId}/cancel`, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            showAlert(data.message);

            if (isEmpty || isAdmin) {
                tabs = tabs.filter(t => t.tab_id !== activeTabId);
                renderTabs();
                loadProducts();
            } else {
                await refreshTabs();
            }
        } catch (error) {
            showAlert(error.message, 'danger');
        } finally {
            btn.disabled = false;
        }
    });

    // Close API Execution
    async function executeClose(payload) {
        try {
            const data = await request(`${config.routes.closeTab}/${activeTabId}/close`, {
                method: 'POST',
                body: JSON.stringify(payload),
            });

            showReceipt(data);


            tabs = tabs.filter(t => t.tab_id !== activeTabId);
            renderTabs();
            loadProducts();

            showSuccess(`Order ${data.order.order_number} completed.`);
            return true;

        } catch (error) {
            showAlert(error.message, 'danger');
            return false;
        }
    }

    // Pay Walk-in or Pay Directly (Opens Payment Modal)
    function triggerPaymentModal() {
        const tab = getActiveTab();
        if (!tab) return;
        paymentModalAmount.textContent = formatMoney(tab.total);
        paymentModal.show();
    }

    document.getElementById('pay-walkin-btn').addEventListener('click', triggerPaymentModal);
    document.getElementById('pay-direct-btn').addEventListener('click', triggerPaymentModal);
    const payDirectAccountBtn = document.getElementById('pay-direct-account-btn');
    if (payDirectAccountBtn) payDirectAccountBtn.addEventListener('click', triggerPaymentModal);

    // Modal Option Clicks
    document.querySelectorAll('.payment-method-opt').forEach(btn => {
        btn.addEventListener('click', async () => {

            const method = btn.dataset.method;

            // CASH
            if (method === 'cash') {
                document.getElementById('cash-calculator')
                    .classList.remove('d-none');

                return;
            }

            // Hide calculator for Card & GCash
            document.getElementById('cash-calculator')
                .classList.add('d-none');

            paymentModal.hide();

            const result = await executeClose({
                payment_method: method
            });

        });
    });
    const cashReceived = document.getElementById('cash-received');
    const cashChange = document.getElementById('cash-change');

    cashReceived.addEventListener('input', () => {

        const tab = getActiveTab();

        if (!tab) return;

        const received = Math.round((parseFloat(cashReceived.value) || 0) * 100) / 100;
        const total = Math.round(tab.total * 100) / 100;
        const change = received - total;

        cashChange.textContent = formatMoney(
            Math.max(change, 0)
        );

    });

    // Charge to Room Click (Direct Post via Custom Modal)

    document.getElementById('confirm-cash-payment')
        .addEventListener('click', async () => {

            const tab = getActiveTab();

            if (!tab) return;

            const received = Math.round((parseFloat(
                document.getElementById('cash-received').value
            ) || 0) * 100) / 100;
            const total = Math.round(tab.total * 100) / 100;

            if (received < total) {

                showAlert('Insufficient cash.', 'danger');

                return;
            }

            paymentModal.hide();

            document.getElementById('cash-calculator')
                .classList.add('d-none');

            document.getElementById('cash-received').value = '';

            document.getElementById('cash-change').textContent = '₱0.00';

            await executeClose({
                payment_method: 'cash'
            });

        });
    let roomChargeCallback = null;
    let selectedGuestData = null;

    document.getElementById('charge-room-btn').addEventListener('click', async () => {
        const btn = document.getElementById('charge-room-btn');
        const originalLabel = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Processing...';

        try {
            const tab = getActiveTab();
            if (!tab) {
                return;
            }

            if (tab.tab_type === 'room' && tab.booking_id && tab.folio_id) {
                await executeClose({
                    payment_method: 'room_charge',
                    booking_id: tab.booking_id,
                    folio_id: tab.folio_id
                });
                return;
            }

            // Fetch checked-in guests when room details are missing
            try {
                const response = await fetch('/coffeeshop/guests/checked-in', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                const guestSelect = document.getElementById('charge-guest-select');
                guestSelect.innerHTML = '<option value="">Select a guest...</option>';

                if (data.guests && data.guests.length > 0) {
                    data.guests.forEach(guest => {
                        const option = document.createElement('option');
                        option.value = JSON.stringify({
                            folio_id: guest.folio_id,
                            booking_id: guest.booking_id,
                            guest_name: guest.guest_name,
                            room_number: guest.room_number
                        });
                        option.textContent = `${guest.guest_name} - Room ${guest.room_number}`;
                        guestSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No checked-in guests available';
                    option.disabled = true;
                    guestSelect.appendChild(option);
                }
            } catch (error) {
                console.error('Error fetching guests:', error);
                alert('Failed to load checked-in guests');
                return;
            }

            document.getElementById('room-charge-warning-text').textContent = `Total to charge: ${formatMoney(tab.total)}`;
            roomChargeCallback = async () => {
                if (!selectedGuestData) {
                    alert('Please select a guest');
                    return;
                }
                await executeClose({
                    payment_method: 'room_charge',
                    booking_id: selectedGuestData.booking_id,
                    folio_id: selectedGuestData.folio_id
                });
            };

            confirmRoomChargeModal.show();
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalLabel;
        }
    });

    document.getElementById('charge-guest-select').addEventListener('change', (e) => {
        if (e.target.value) {
            selectedGuestData = JSON.parse(e.target.value);
        } else {
            selectedGuestData = null;
        }
    });

    document.getElementById('confirm-room-charge-submit-btn').addEventListener('click', async () => {
        const btn = document.getElementById('confirm-room-charge-submit-btn');
        if (btn.disabled) return;

        try {
            btn.disabled = true;
            confirmRoomChargeModal.hide();
            if (roomChargeCallback) {
                const cb = roomChargeCallback;
                roomChargeCallback = null;
                await cb();
            }
        } finally {
            btn.disabled = false;
        }
    });

    const confirmAccountChargeModal = new bootstrap.Modal(document.getElementById('confirmAccountChargeModal'));
    let accountChargeCallback = null;
    const chargeAccountBtn = document.getElementById('charge-account-btn');
    if (chargeAccountBtn) {
        chargeAccountBtn.addEventListener('click', () => {
            const tab = getActiveTab();
            if (!tab) return;

            const headerEl = document.getElementById('account-charge-modal-header');
            const titleEl = document.getElementById('account-charge-modal-title');
            const alertBanner = document.getElementById('account-charge-alert-banner');
            const warningText = document.getElementById('account-charge-warning-text');
            const breakdownBox = document.getElementById('account-credit-breakdown-box');
            const submitBtn = document.getElementById('confirm-account-charge-submit-btn');
            const exceededRow = document.getElementById('acc-modal-exceeded-row');

            const available = tab.credit_account_available !== null && tab.credit_account_available !== undefined ? tab.credit_account_available : 0;
            const limit = tab.credit_account_limit !== null && tab.credit_account_limit !== undefined ? tab.credit_account_limit : 0;
            const balance = tab.credit_account_balance !== null && tab.credit_account_balance !== undefined ? tab.credit_account_balance : 0;
            const total = tab.total || 0;
            const exceeded = total - available;

            document.getElementById('acc-modal-name').textContent = tab.credit_account_name || 'N/A';
            document.getElementById('acc-modal-limit').textContent = formatMoney(limit);
            document.getElementById('acc-modal-balance').textContent = formatMoney(balance);
            document.getElementById('acc-modal-available').textContent = formatMoney(available);
            document.getElementById('acc-modal-total').textContent = formatMoney(total);
            breakdownBox.classList.remove('d-none');

            if (exceeded > 0) {
                // CREDIT LIMIT EXCEEDED WARNING ALERT STATE
                headerEl.style.backgroundColor = '#dc3545';
                titleEl.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-2"></i>VIP Credit Limit Exceeded!`;
                alertBanner.className = 'alert alert-danger border-danger fw-bold small mb-3';
                alertBanner.innerHTML = `<i class="fa-solid fa-circle-exclamation me-1"></i> <strong>ALERT:</strong> Order total exceeds available VIP credit limit by <strong>${formatMoney(exceeded)}</strong>! Account charge cannot be completed.`;
                alertBanner.classList.remove('d-none');
                warningText.textContent = `This transaction exceeds the account's allowed credit limit (${formatMoney(limit)}). Please select another payment method (Cash, Card, GCash, Maya) or settle outstanding account balance.`;

                exceededRow.classList.remove('d-none');
                document.getElementById('acc-modal-exceeded').textContent = `+${formatMoney(exceeded)}`;

                submitBtn.classList.add('d-none');
                accountChargeCallback = null;
            } else {
                // NORMAL CONFIRMATION STATE
                headerEl.style.backgroundColor = '#504538';
                titleEl.innerHTML = `<i class="fa-solid fa-file-invoice-dollar me-2" style="color: #c2a889;"></i>Charge to Account`;
                alertBanner.classList.add('d-none');
                warningText.textContent = `Are you sure you want to charge ${formatMoney(total)} to ${tab.credit_account_name}?`;

                exceededRow.classList.add('d-none');

                submitBtn.classList.remove('d-none');
                submitBtn.disabled = false;
                submitBtn.className = 'btn btn-sm text-white font-brand';
                submitBtn.style.backgroundColor = '#334c42';
                submitBtn.textContent = 'Confirm Charge';

                accountChargeCallback = async () => {
                    await executeClose({
                        payment_method: 'account_charge',
                        credit_account_id: tab.credit_account_id
                    });
                };
            }

            confirmAccountChargeModal.show();
        });
    }
    const confirmAccountChargeSubmitBtn = document.getElementById('confirm-account-charge-submit-btn');
    if (confirmAccountChargeSubmitBtn) {
        confirmAccountChargeSubmitBtn.addEventListener('click', async () => {
            const btn = confirmAccountChargeSubmitBtn;
            if (btn.disabled) return;
            try {
                btn.disabled = true;
                confirmAccountChargeModal.hide();
                if (accountChargeCallback) {
                    const cb = accountChargeCallback;
                    accountChargeCallback = null;
                    await cb();
                }
            } finally {
                btn.disabled = false;
            }
        });
    }

    // Transfer Tab
    const transferTabModal = new bootstrap.Modal(document.getElementById('transferTabModal'));
    const transferTypeSelect = document.getElementById('transfer-tab-type');
    const transferRoomPanel = document.getElementById('transfer-room-panel');
    const transferAccountPanel = document.getElementById('transfer-account-panel');

    document.getElementById('transfer-tab-btn').addEventListener('click', () => {
        if (!activeTabId) return;
        const currentType = getActiveTab().tab_type;
        transferTypeSelect.value = currentType === 'walk_in' ? 'room' : currentType;
        transferTypeSelect.dispatchEvent(new Event('change'));
        transferTabModal.show();
    });

    transferTypeSelect.addEventListener('change', async (e) => {
        transferRoomPanel.classList.add('d-none');
        transferAccountPanel.classList.add('d-none');

        if (e.target.value === 'room') {
            transferRoomPanel.classList.remove('d-none');
            const transferGuestSelect = document.getElementById('transfer-guest');

            if (checkedInGuests.length === 0) {
                await loadGuests(); // ensure guests loaded before cloning
            }

            // Clone options from original guest select after load completes
            transferGuestSelect.innerHTML = document.getElementById('new-tab-guest').innerHTML;
        } else if (e.target.value === 'account') {
            transferAccountPanel.classList.remove('d-none');
        }
    });

    document.getElementById('confirm-transfer-tab-btn').addEventListener('click', async () => {
        const type = transferTypeSelect.value;
        const payload = { tab_type: type };

        if (type === 'room') {
            const guestVal = document.getElementById('transfer-guest').value;
            if (!guestVal) return showAlert('Select a room.', 'warning');
            const guestData = JSON.parse(guestVal);
            payload.folio_id = guestData.folio_id;
            payload.booking_id = guestData.booking_id;
            payload.room_id = guestData.room_id;
            payload.guest_id = guestData.guest_id;
        } else if (type === 'account') {
            const accVal = document.getElementById('transfer-account').value;
            if (!accVal) return showAlert('Select an account.', 'warning');
            payload.credit_account_id = accVal;
        }

        try {
            transferTabModal.hide();
            const data = await request(`${config.routes.tabItems}/${activeTabId}/transfer`, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            replaceTab(data.tab);
            showSuccess('Tab transferred successfully.');
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    });

    // Discount Tab
    const discountTabModal = new bootstrap.Modal(document.getElementById('discountTabModal'));

    function validateDiscountInput() {
        const tab = getActiveTab();
        if (!tab) return true;

        const amountInput = document.getElementById('discount-amount');
        const isPercentage = document.getElementById('discount-is-percentage').value === '1';
        const errorMsg = document.getElementById('discount-error-message');
        const amount = parseFloat(amountInput.value) || 0;
        const discountType = document.getElementById('discount-type').value;
        const isFixedDiscount = (discountType === 'Senior Citizen' || discountType === 'PWD');
        
        let isValid = true;
        let errorMessageText = '';

        if (!isFixedDiscount) {
            if (isPercentage && amount > 100) {
                isValid = false;
                errorMessageText = 'Discount percentage cannot exceed 100%.';
            } else if (!isPercentage && amount > tab.subtotal) {
                isValid = false;
                errorMessageText = 'Discount amount cannot exceed the subtotal.';
            } else if (amount < 0) {
                isValid = false;
                errorMessageText = 'Discount amount cannot be negative.';
            }
        }

        if (!isValid) {
            amountInput.classList.add('is-invalid');
            errorMsg.textContent = errorMessageText;
            errorMsg.classList.remove('d-none');
            return false;
        } else {
            amountInput.classList.remove('is-invalid');
            errorMsg.classList.add('d-none');
            return true;
        }
    }

    document.getElementById('discount-amount').addEventListener('input', validateDiscountInput);

    function applyDiscountTypeDefaults() {
        const tab = getActiveTab();
        const discountType = document.getElementById('discount-type').value;
        const isPercentageSelect = document.getElementById('discount-is-percentage');
        const amountInput = document.getElementById('discount-amount');
        const isFixedDiscount = (discountType === 'Senior Citizen' || discountType === 'PWD');

        if (isFixedDiscount) {
            // Lock to 20%; update amount based on current unit (% or ₱)
            const subtotal = tab ? tab.subtotal : 0;
            if (isPercentageSelect.value === '1') {
                amountInput.value = 20;
            } else {
                amountInput.value = (subtotal * 0.20).toFixed(2);
            }
            amountInput.readOnly = true;
            amountInput.classList.add('bg-light');
        } else {
            amountInput.readOnly = false;
            amountInput.classList.remove('bg-light');
        }
    }

    document.getElementById('discount-type').addEventListener('change', applyDiscountTypeDefaults);

    document.getElementById('discount-is-percentage').addEventListener('change', () => {
        const discountType = document.getElementById('discount-type').value;
        const isFixedDiscount = (discountType === 'Senior Citizen' || discountType === 'PWD');
        if (isFixedDiscount) {
            applyDiscountTypeDefaults();
        }
        validateDiscountInput();
    });

    document.getElementById('discount-tab-btn').addEventListener('click', () => {
        if (!activeTabId) return;
        const tab = getActiveTab();
        if (tab.discount_amount > 0) {
            document.getElementById('discount-type').value = tab.discount_type;
            document.getElementById('discount-is-percentage').value = tab.is_discount_percentage ? '1' : '0';
            document.getElementById('discount-amount').value = tab.discount_amount;
        } else {
            // Reset to defaults
            document.getElementById('discount-type').value = 'Senior Citizen';
            document.getElementById('discount-is-percentage').value = '1';
            document.getElementById('discount-amount').value = 20;
        }
        applyDiscountTypeDefaults();
        validateDiscountInput();
        discountTabModal.show();
    });

    document.getElementById('confirm-discount-btn').addEventListener('click', async () => {
        const tab = getActiveTab();
        if (!tab) return;

        const isPercentage = document.getElementById('discount-is-percentage').value === '1';
        let amount = parseFloat(document.getElementById('discount-amount').value) || 0;
        const discountType = document.getElementById('discount-type').value;
        const isFixedDiscount = (discountType === 'Senior Citizen' || discountType === 'PWD');

        // Enforce 20% for Senior/PWD
        if (isFixedDiscount) {
            if (isPercentage) {
                amount = 20;
            } else {
                amount = parseFloat((tab.subtotal * 0.20).toFixed(2));
            }
        }

        if (!validateDiscountInput()) {
            return;
        }

        if (amount <= 0) {
            showAlert('Please enter a valid discount amount.', 'danger');
            return;
        }

        try {
            discountTabModal.hide();
            const data = await request(`${config.routes.tabItems}/${activeTabId}/discount`, {
                method: 'POST',
                body: JSON.stringify({
                    discount_type: discountType,
                    discount_amount: amount,
                    is_discount_percentage: isPercentage
                })
            });
            replaceTab(data.tab);
            showAlert('Discount applied.');
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    });

    document.getElementById('remove-discount-btn').addEventListener('click', async () => {
        try {
            discountTabModal.hide();
            const data = await request(`${config.routes.tabItems}/${activeTabId}/discount`, {
                method: 'DELETE'
            });
            replaceTab(data.tab);
            showAlert('Discount removed.');
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    });

    // Auto-polling for live-sync (every 5 seconds)
    if (window.posPollInterval) {
        clearInterval(window.posPollInterval);
    }
    window.posPollInterval = setInterval(refreshTabs, 5000);

    // Clean up interval when navigating away via Turbo
    document.addEventListener('turbo:before-visit', function cleanup() {
        if (window.posPollInterval) {
            clearInterval(window.posPollInterval);
            window.posPollInterval = null;
        }
        document.removeEventListener('turbo:before-visit', cleanup);
    });

    window.printReceipt = function () {
        const receiptEl = document.getElementById('receipt-content');
        const content   = receiptEl.innerHTML;
        const orderNum  = receiptEl.dataset.orderNumber || '';
        const fileName  = orderNum ? `Receipt-${orderNum}` : `Receipt-${new Date().toISOString().slice(0,10)}`;

        const win = window.open('', '', 'width=400,height=600');

        win.document.write(`
            <html>
            <head>
                <title>${fileName}</title>
                <style>
                    body {
                        font-family: Arial;
                        padding: 10px;
                        font-size: 12px;
                        width: 280px;
                    }
                    .receipt {
                        width: 100%;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 12px;
                    }
                    td, th {
                        padding: 4px 0;
                    }
                    .text-center {
                        text-align: center;
                    }
                    .text-end {
                        text-align: right;
                    }
                </style>
            </head>
            <body>
                ${content}
                <script>
                    window.onload = function() {
                        window.print();
                        window.onafterprint = window.close;
                    }
                <\/script>
            </body>
            </html>
        `);

        win.document.close();
        win.focus();
    };

    bindProductButtons();
    bindPairingButtons();
    renderTabs();
})();
