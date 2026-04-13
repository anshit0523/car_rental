document.addEventListener('DOMContentLoaded', function () {
    const STORE_KEY  = 'browseCarFilters';
    const filterForm = document.getElementById('filterForm');

    // ── Clear flag — wipe sessionStorage and redirect cleanly ────────────────
    const params = new URLSearchParams(window.location.search);
  if (params.get('clear') === '1') {
    sessionStorage.removeItem(STORE_KEY);
    window.location.replace(window.location.pathname);
    return;
}

    // ── Clear saved filters on logout ─────────────────────────────────────────
    const logoutBtn = document.querySelector('form[action*="logout"] button');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            sessionStorage.removeItem(STORE_KEY);
        });
    }

    // ── Date validation ───────────────────────────────────────────────────────
    const pickupDate = document.querySelector('input[name="pickup_date"]');
    const returnDate = document.querySelector('input[name="return_date"]');
    if (pickupDate && returnDate) {
        const today = new Date().toISOString().split('T')[0];
        pickupDate.min = today;
        returnDate.min = pickupDate.value || today;
        pickupDate.addEventListener('change', function () {
            returnDate.min = this.value || today;
            if (returnDate.value && returnDate.value < returnDate.min) {
                returnDate.value = '';
            }
        });
    }

    // ── Sort dropdown sync ────────────────────────────────────────────────────
    const sortSelect   = document.querySelector('select[name="sort_by"]');
    const hiddenSortBy = document.querySelector('#filterForm input[name="sort_by"]');
    if (sortSelect && hiddenSortBy) {
        sortSelect.addEventListener('change', function () {
            hiddenSortBy.value = this.value;
        });
    }

    // ── Price label live update ───────────────────────────────────────────────
    const priceRange = document.querySelector('input[name="max_price"]');
    if (priceRange) {
        priceRange.addEventListener('input', function () {
            updatePriceLabel(this);
        });
    }

    // ── Check current URL params ──────────────────────────────────────────────
    const hasFilters = ['pickup_date','return_date','time','max_price',
                        'brand_id[]','fuel_type_id[]','transmission_id[]',
                        'car_type_id[]','sort_by']
                        .some(k => params.has(k));

    if (hasFilters) {
        saveFiltersFromParams(params);
        restoreInputsFromParams(params);
        return;
    }

    // ── No URL params — try restoring from sessionStorage ────────────────────
    const saved = sessionStorage.getItem(STORE_KEY);
    if (!saved) return;

    let data;
    try { data = JSON.parse(saved); } catch (e) {
        sessionStorage.removeItem(STORE_KEY);
        return;
    }

    restoreInputsFromData(data);

    if (filterForm && !filterForm.dataset.autoSubmitted) {
        filterForm.dataset.autoSubmitted = '1';
        setTimeout(function () {
            filterForm.submit();
        }, 100);
    }
});

// ── Save filter values from URL params into sessionStorage ────────────────────
function saveFiltersFromParams(params) {
    const data = {};
    params.forEach(function (value, key) {
        if (data[key]) {
            data[key] = [].concat(data[key], value);
        } else {
            data[key] = value;
        }
    });
    sessionStorage.setItem('browseCarFilters', JSON.stringify(data));
}

// ── Restore input visuals from URL params ─────────────────────────────────────
function restoreInputsFromParams(params) {
    const fields = ['pickup_date', 'return_date', 'time', 'max_price'];
    fields.forEach(function (name) {
        const input = document.querySelector(`#filterForm input[name="${name}"]`);
        if (input && params.has(name)) {
            input.value = params.get(name);
            if (input.type === 'range') updatePriceLabel(input);
        }
    });
    const hiddenSort = document.querySelector('#filterForm input[name="sort_by"]');
    if (hiddenSort && params.has('sort_by')) hiddenSort.value = params.get('sort_by');
}

// ── Restore input visuals from saved JSON data ────────────────────────────────
function restoreInputsFromData(data) {
    Object.entries(data).forEach(function ([key, value]) {
        const cleanKey = key.replace('[]', '');
        const checkboxes = document.querySelectorAll(`#filterForm input[name="${cleanKey}[]"]`);
        if (checkboxes.length) {
            const values = [].concat(value).map(String);
            checkboxes.forEach(function (cb) {
                cb.checked = values.includes(cb.value);
            });
            return;
        }

        const input = document.querySelector(`#filterForm input[name="${key}"]`);
        if (input) {
            input.value = value;
            if (input.type === 'range') updatePriceLabel(input);
        }
    });
}

function updatePriceLabel(slider) {
    const labels = slider.closest('.mt_30')?.querySelectorAll('span');
    if (labels && labels.length >= 2) {
        labels[1].textContent = '₱' + Number(slider.value).toLocaleString();
    }
}