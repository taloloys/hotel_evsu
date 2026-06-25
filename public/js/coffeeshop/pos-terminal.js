(() => {
    const config = window.posConfig;
    let tabs = [...(config.initialTabs || [])];
    let activeTabId = tabs.length ? tabs[0].tab_id : null;
    let activeCategory = 'all';
    let searchTimer = null;
    let checkedInGuests = [];

    const alertBox = document.getElementById('pos-alert');
    const productGrid = document.getElementById('product-grid');
    const searchInput = document.getElementById('product-search');
    const tabSwitcher = document.getElementById('tab-switcher');
    const activeTabPanel = document.getElementById('active-tab-panel');
    const noTabMessage = document.getElementById('no-tab-message');
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const activeTabName = document.getElementById('active-tab-name');
    const activeTabTotal = document.getElementById('active-tab-total');
    const closeTabModal = new bootstrap.Modal(document.getElementById('closeTabModal'));

    function showAlert(message, type = 'success') {
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
        setTimeout(() => alertBox.classList.add('d-none'), 4000);
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

    function renderTabs() {
        tabSwitcher.innerHTML = '';
        tabs.forEach(tab => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `btn btn-sm ${tab.tab_id === activeTabId ? 'btn-primary' : 'btn-outline-secondary'}`;
            btn.textContent = `${tab.tab_name} (${formatMoney(tab.total)})`;
            btn.addEventListener('click', () => {
                activeTabId = tab.tab_id;
                renderTabs();
                renderCart();
            });
            tabSwitcher.appendChild(btn);
        });

        if (!tabs.length) {
            activeTabId = null;
            activeTabPanel.classList.add('d-none');
            noTabMessage.classList.remove('d-none');
            return;
        }

        if (!activeTabId || !tabs.some(tab => tab.tab_id === activeTabId)) {
            activeTabId = tabs[0].tab_id;
        }

        activeTabPanel.classList.remove('d-none');
        noTabMessage.classList.add('d-none');
        renderCart();
    }

    function renderCart() {
        const tab = getActiveTab();
        if (!tab) return;

        activeTabName.textContent = tab.tab_name;
        activeTabTotal.textContent = formatMoney(tab.total);
        cartTotal.textContent = formatMoney(tab.total);
        cartItems.innerHTML = '';

        if (!tab.items.length) {
            cartItems.innerHTML = '<div class="text-muted">No items yet.</div>';
            return;
        }

        tab.items.forEach(item => {
            const row = document.createElement('div');
            row.className = 'd-flex justify-content-between align-items-center mb-2 gap-2';
            row.innerHTML = `
                <div>
                    <div class="fw-semibold">${item.name}</div>
                    <small class="text-muted">${formatMoney(item.unit_price)} each</small>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-outline-secondary btn-sm qty-btn" data-action="decrease" data-item-id="${item.tab_item_id}">-</button>
                    <span>${item.quantity}</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm qty-btn" data-action="increase" data-item-id="${item.tab_item_id}">+</button>
                    <span class="ms-2">${formatMoney(item.line_total)}</span>
                    <button type="button" class="btn btn-link btn-sm text-danger remove-item-btn" data-item-id="${item.tab_item_id}"><i class="fa-solid fa-trash"></i></button>
                </div>
            `;
            cartItems.appendChild(row);
        });

        cartItems.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const itemId = btn.dataset.itemId;
                const item = tab.items.find(i => String(i.tab_item_id) === String(itemId));
                const delta = btn.dataset.action === 'increase' ? 1 : -1;
                const newQty = item.quantity + delta;
                await updateItemQuantity(itemId, newQty);
            });
        });

        cartItems.querySelectorAll('.remove-item-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                await updateItemQuantity(btn.dataset.itemId, 0);
            });
        });
    }

    function renderProducts(products) {
        productGrid.innerHTML = '';
        if (!products.length) {
            productGrid.innerHTML = '<div class="col-12 text-muted">No products found.</div>';
            return;
        }

        products.forEach(product => {
            const col = document.createElement('div');
            col.className = 'col-md-3 col-6 product-tile';
            col.innerHTML = `
                <div class="card border-0 shadow-sm h-100 ${product.stock_quantity <= 0 ? 'opacity-50' : ''}">
                    <div class="card-body text-center">
                        ${product.image_url ? `<img src="${product.image_url}" class="mb-2 rounded" style="max-height:48px;" alt="">` : '<i class="fa-solid fa-mug-hot fa-2x text-warning mb-2"></i>'}
                        <div class="fw-semibold">${product.name}</div>
                        <small class="text-muted d-block">${product.description || ''}</small>
                        <small class="text-muted">${product.category || ''}</small>
                        <div class="fw-bold text-primary mt-2">${formatMoney(product.price)}</div>
                        <small class="${product.is_low_stock ? 'text-danger' : 'text-muted'}">Stock: ${product.stock_quantity}</small>
                        <button type="button" class="btn btn-primary btn-sm w-100 mt-2 add-product-btn" data-product-id="${product.product_id}" ${product.stock_quantity <= 0 ? 'disabled' : ''}>ADD</button>
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
                try {
                    const data = await request(`${config.routes.tabItems}/${activeTabId}/items`, {
                        method: 'POST',
                        body: JSON.stringify({ product_id: btn.dataset.productId, quantity: 1 }),
                    });
                    replaceTab(data.tab);
                    showAlert(data.message);
                    loadProducts();
                } catch (error) {
                    showAlert(error.message, 'danger');
                }
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
        tabs = data.tabs || [];
        renderTabs();
    }

    async function loadGuests() {
        const data = await request(config.routes.guests);
        checkedInGuests = data.guests || [];
        const select = document.getElementById('checked-in-guest');
        select.innerHTML = '<option value="">Select checked-in guest</option>';
        checkedInGuests.forEach(guest => {
            const option = document.createElement('option');
            option.value = JSON.stringify({
                booking_id: guest.booking_id,
                folio_id: guest.folio_id,
            });
            option.textContent = `Room ${guest.room_number} - ${guest.guest_name} (Balance: ${formatMoney(guest.balance)})`;
            select.appendChild(option);
        });
    }

    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.category-btn').forEach(b => {
                b.classList.remove('btn-dark');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-dark');
            activeCategory = btn.dataset.category;
            loadProducts();
        });
    });

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadProducts, 250);
    });

    document.getElementById('open-tab-btn').addEventListener('click', async () => {
        const tabName = document.getElementById('new-tab-name').value.trim();
        if (!tabName) {
            showAlert('Enter a customer or room name.', 'warning');
            return;
        }
        try {
            const data = await request(config.routes.storeTab, {
                method: 'POST',
                body: JSON.stringify({ tab_name: tabName, tab_type: 'walk_in' }),
            });
            tabs.unshift(data.tab);
            activeTabId = data.tab.tab_id;
            document.getElementById('new-tab-name').value = '';
            renderTabs();
            showAlert(data.message);
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    });

    document.getElementById('cancel-tab-btn').addEventListener('click', async () => {
        if (!activeTabId || !confirm('Cancel this tab?')) return;
        try {
            const data = await request(`${config.routes.cancelTab}/${activeTabId}/cancel`, { method: 'POST', body: '{}' });
            tabs = tabs.filter(tab => tab.tab_id !== activeTabId);
            renderTabs();
            showAlert(data.message);
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    });

    document.getElementById('payment-method').addEventListener('change', (event) => {
        const panel = document.getElementById('room-charge-panel');
        if (event.target.value === 'room_charge') {
            panel.classList.remove('d-none');
            loadGuests();
        } else {
            panel.classList.add('d-none');
        }
    });

    document.getElementById('close-tab-btn').addEventListener('click', () => {
        const tab = getActiveTab();
        if (!tab || !tab.items.length) {
            showAlert('Add items before closing the tab.', 'warning');
            return;
        }
        document.getElementById('payment-method').value = 'cash';
        document.getElementById('room-charge-panel').classList.add('d-none');
        closeTabModal.show();
    });

    document.getElementById('confirm-close-tab').addEventListener('click', async () => {
        const paymentMethod = document.getElementById('payment-method').value;
        const payload = { payment_method: paymentMethod };

        if (paymentMethod === 'room_charge') {
            const selected = document.getElementById('checked-in-guest').value;
            if (!selected) {
                showAlert('Select a checked-in guest.', 'warning');
                return;
            }
            const guest = JSON.parse(selected);
            payload.booking_id = guest.booking_id;
            payload.folio_id = guest.folio_id;
        }

        try {
            const data = await request(`${config.routes.closeTab}/${activeTabId}/close`, {
                method: 'POST',
                body: JSON.stringify(payload),
            });
            tabs = tabs.filter(tab => tab.tab_id !== activeTabId);
            renderTabs();
            closeTabModal.hide();
            loadProducts();
            showAlert(`Order ${data.order.order_number} completed.`);
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    });

    bindProductButtons();
    renderTabs();
})();
