const currentPanel = window.location.pathname.startsWith('/manager') ? 'manager' : 'admin';

const CONFIG = window.AdminCalendarConfig || {
    calendarUrl: `/${currentPanel}/calendar`,
    bookingStoreUrl: `/${currentPanel}/bookings`,
    bookingDetailsBaseUrl: `/${currentPanel}/bookings`,
    checkAvailabilityUrl: `/${currentPanel}/bookings/check-availability-exact`,
    searchCustomerUrl: `/${currentPanel}/bookings/search-customer`,
};

function qs(selector, root = document) {
    return root.querySelector(selector);
}

function qsa(selector, root = document) {
    return Array.from(root.querySelectorAll(selector));
}

function getTodayStr() {
    const d = new Date();
    const pad = (n) => String(n).padStart(2, '0');

    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
}

function showLoading() {
    qs('#loadingIndicator')?.classList.remove('hidden');
}

function hideLoading() {
    qs('#loadingIndicator')?.classList.add('hidden');
}

function buildFilterUrl() {
    const form = qs('#filterForm');

    if (!form) {
        return CONFIG.calendarUrl;
    }

    const formData = new FormData(form);
    const newParams = new URLSearchParams(formData);
    const current = new URLSearchParams(window.location.search);

    current.forEach((value, key) => {
        if (!newParams.has(key)) {
            newParams.set(key, value);
        }
    });

    return `${CONFIG.calendarUrl}?${newParams.toString()}`;
}

async function fetchFilteredData() {
    showLoading();

    const url = buildFilterUrl();
    history.replaceState(null, '', url);

    try {
        const res = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
        });

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }

        const html = await res.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newWrapper = doc.querySelector('#calendarScrollWrapper');
        const curWrapper = document.querySelector('#calendarScrollWrapper');

        if (newWrapper && curWrapper) {
            const oldScrollLeft = curWrapper.scrollLeft;
            curWrapper.innerHTML = newWrapper.innerHTML;
            curWrapper.scrollLeft = oldScrollLeft;
        }

        const newNav = doc.querySelector('.min-w-fit');
        const curNav = document.querySelector('.min-w-fit');

        if (newNav && curNav) {
            curNav.textContent = newNav.textContent;
        }
    } catch (err) {
        console.error('Filter fetch failed:', err);
    } finally {
        hideLoading();
    }
}

function toggleCustomerType() {
    const selected = qs('input[name="customer_type"]:checked')?.value;
    const existingSection = qs('#existingCustomerSection');
    const newSection = qs('#newCustomerSection');

    const newName = qs('#new_customer_name');
    const newPhone = qs('#new_customer_phone');
    const newEmail = qs('#new_customer_email');
    const newPassword = qs('#new_customer_password');
    const newPasswordConfirmation = qs('#new_customer_password_confirmation');

    if (selected === 'existing') {
        existingSection?.classList.remove('hidden');
        newSection?.classList.add('hidden');

        newName?.removeAttribute('required');
        newPhone?.removeAttribute('required');
        newEmail?.removeAttribute('required');
        newPassword?.removeAttribute('required');
        newPasswordConfirmation?.removeAttribute('required');
    } else {
        existingSection?.classList.add('hidden');
        newSection?.classList.remove('hidden');

        newName?.setAttribute('required', 'required');
        newPhone?.setAttribute('required', 'required');
        newEmail?.setAttribute('required', 'required');
        newPassword?.setAttribute('required', 'required');
        newPasswordConfirmation?.setAttribute('required', 'required');

        const existingUserId = qs('#existing_user_id');
        const resultBox = qs('#existingCustomerResult');
        const notFoundBox = qs('#existingCustomerNotFound');
        const dropdown = qs('#existingCustomerDropdown');

        if (existingUserId) existingUserId.value = '';

        resultBox?.classList.add('hidden');
        notFoundBox?.classList.add('hidden');
        dropdown?.classList.add('hidden');

        if (dropdown) {
            dropdown.innerHTML = '';
        }
    }
}

function toggleServiceLocation() {
    const serviceTypeSelect = qs('#service_type_id');
    const wrapper = qs('#serviceLocationWrapper');
    const locationInput = qs('#service_location');

    if (!serviceTypeSelect || !wrapper || !locationInput) return;

    const selectedOption = serviceTypeSelect.options[serviceTypeSelect.selectedIndex];
    const serviceName = (selectedOption?.dataset?.name || '').toLowerCase();
    const isDelivery = serviceName.includes('deliver');

    if (isDelivery) {
        wrapper.classList.remove('hidden');
        locationInput.setAttribute('required', 'required');
    } else {
        wrapper.classList.add('hidden');
        locationInput.removeAttribute('required');
        locationInput.value = '';
    }
}

function syncReturnMinDate() {
    const pickupDateInput = qs('#modal_pickup_date');
    const returnDateInput = qs('#modal_return_date');

    if (!pickupDateInput || !returnDateInput) return;

    const todayStr = getTodayStr();

    pickupDateInput.min = todayStr;

    if (!pickupDateInput.value || pickupDateInput.value < todayStr) {
        pickupDateInput.value = todayStr;
    }

    const pickupDate = pickupDateInput.value || todayStr;

    returnDateInput.min = pickupDate;

    if (!returnDateInput.value || returnDateInput.value < pickupDate) {
        returnDateInput.value = pickupDate;
    }
}

function syncReturnToPickup() {
    const pickupDateInput = qs('#modal_pickup_date');
    const pickupTimeInput = qs('input[name="pickup_time"]');
    const returnDateInput = qs('#modal_return_date');
    const returnTimeInput = qs('input[name="return_time"]');

    if (!pickupDateInput || !pickupTimeInput || !returnDateInput || !returnTimeInput) {
        return;
    }

    if (pickupDateInput.value) {
        returnDateInput.value = pickupDateInput.value;
    }

    if (pickupTimeInput.value) {
        returnTimeInput.value = pickupTimeInput.value;
    }
}

function getPickupFromReturn(returnIso) {
    const dt = new Date(returnIso);
    const pad = (num) => String(num).padStart(2, '0');

    return {
        pickupDate: `${dt.getFullYear()}-${pad(dt.getMonth() + 1)}-${pad(dt.getDate())}`,
        pickupTime: `${pad(dt.getHours())}:${pad(dt.getMinutes())}`,
    };
}

async function checkExactAvailability() {
    const carId = qs('#modal_car_id')?.value;
    const pickupDate = qs('#modal_pickup_date')?.value;
    const returnDate = qs('#modal_return_date')?.value;
    const pickupTime = qs('input[name="pickup_time"]')?.value;
    const returnTime = qs('input[name="return_time"]')?.value;

    const box = qs('#liveAvailabilityBox');
    const saveBtn = qs('#saveBookingBtn');
    const token = qs('#createBookingForm input[name="_token"]')?.value;

    if (!box || !saveBtn) return;

    if (!carId || !pickupDate || !returnDate || !pickupTime || !returnTime) {
        box.classList.add('hidden');
        saveBtn.disabled = false;
        saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        return;
    }

    const pickupDateTime = new Date(`${pickupDate}T${pickupTime}:00`);
    const returnDateTime = new Date(`${returnDate}T${returnTime}:00`);

    box.classList.remove(
        'hidden',
        'bg-red-50', 'border-red-200', 'text-red-700',
        'bg-emerald-50', 'border-emerald-200', 'text-emerald-700'
    );

    if (returnDateTime <= pickupDateTime) {
        box.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
        box.textContent = 'Return date/time must be after pickup date/time.';
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
        return;
    }

    try {
        const res = await fetch(CONFIG.checkAvailabilityUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                car_id: carId,
                pickup_date: pickupDate,
                pickup_time: pickupTime,
                return_date: returnDate,
                return_time: returnTime,
            }),
        });

        const data = await res.json();

        box.classList.remove(
            'bg-red-50', 'border-red-200', 'text-red-700',
            'bg-emerald-50', 'border-emerald-200', 'text-emerald-700'
        );

        if (!res.ok || !data.available) {
            box.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
            box.textContent = data.message || 'This car is not available for the selected date and time.';
            saveBtn.disabled = true;
            saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
            return;
        }

        box.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
        box.textContent = data.message || 'Car is available for the selected date and time.';
        saveBtn.disabled = false;
        saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    } catch (error) {
        console.error('Availability check failed:', error);

        box.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
        box.textContent = 'Failed to check exact availability.';
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

function renderExistingCustomerDropdown(users) {
    const dropdown = qs('#existingCustomerDropdown');

    if (!dropdown) return;

    if (!users.length) {
        dropdown.innerHTML = '';
        dropdown.classList.add('hidden');
        return;
    }

    dropdown.innerHTML = '';

    users.forEach((user) => {
        const btn = document.createElement('button');

        btn.type = 'button';
        btn.className = 'w-full text-left px-4 py-3 hover:bg-slate-50 border-b last:border-b-0 border-gray-100';

        btn.innerHTML = `
            <div class="font-semibold text-gray-800">${user.name ?? ''}</div>
            <div class="text-xs text-gray-500">${user.email ?? 'No email'}</div>
            <div class="text-xs text-gray-500">${user.phone ?? 'No phone'}</div>
        `;

        btn.addEventListener('click', () => {
            selectExistingCustomer(user);
        });

        dropdown.appendChild(btn);
    });

    dropdown.classList.remove('hidden');
}

function selectExistingCustomer(user) {
    const existingUserId = qs('#existing_user_id');
    const nameEl = qs('#existing_customer_name');
    const phoneEl = qs('#existing_customer_phone');
    const emailEl = qs('#existing_customer_email');
    const searchInput = qs('#existing_customer_search');
    const resultBox = qs('#existingCustomerResult');
    const notFoundBox = qs('#existingCustomerNotFound');
    const dropdown = qs('#existingCustomerDropdown');

    if (existingUserId) existingUserId.value = user.id ?? '';
    if (nameEl) nameEl.textContent = user.name ?? '';
    if (phoneEl) phoneEl.textContent = user.phone ?? '';
    if (emailEl) emailEl.textContent = user.email ?? '';
    if (searchInput) searchInput.value = user.name ?? '';

    resultBox?.classList.remove('hidden');
    notFoundBox?.classList.add('hidden');
    dropdown?.classList.add('hidden');
}

async function searchExistingCustomers(keyword) {
    const resultBox = qs('#existingCustomerResult');
    const notFoundBox = qs('#existingCustomerNotFound');
    const dropdown = qs('#existingCustomerDropdown');
    const existingUserId = qs('#existing_user_id');

    if (existingUserId) existingUserId.value = '';

    resultBox?.classList.add('hidden');
    notFoundBox?.classList.add('hidden');

    if (!keyword) {
        if (dropdown) {
            dropdown.innerHTML = '';
            dropdown.classList.add('hidden');
        }

        return;
    }

    try {
        const response = await fetch(`${CONFIG.searchCustomerUrl}?keyword=${encodeURIComponent(keyword)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (!response.ok || !data.success || !data.users?.length) {
            if (dropdown) {
                dropdown.innerHTML = '';
                dropdown.classList.add('hidden');
            }

            if (notFoundBox) {
                notFoundBox.textContent = data.message || 'Customer not found.';
                notFoundBox.classList.remove('hidden');
            }

            return;
        }

        renderExistingCustomerDropdown(data.users);
    } catch (error) {
        console.error('Customer search failed:', error);

        if (dropdown) {
            dropdown.innerHTML = '';
            dropdown.classList.add('hidden');
        }

        if (notFoundBox) {
            notFoundBox.textContent = 'Failed to search customer.';
            notFoundBox.classList.remove('hidden');
        }
    }
}

function openBookingModal(bookingId) {
    const modal = qs('#bookingModal');
    const content = qs('#bookingModalContent');
    const addBtn = qs('#openAddBookingFromDetailsBtn');

    modal?.classList.remove('hidden');

    if (addBtn) {
        addBtn.classList.add('hidden');
        addBtn.dataset.carId = '';
        addBtn.dataset.carName = '';
        addBtn.dataset.pickupDate = '';
        addBtn.dataset.pickupTime = '';
        addBtn.dataset.returnDate = '';
        addBtn.dataset.returnTime = '';
    }

    if (content) {
        content.innerHTML = `
            <div class="text-center text-gray-400 py-4">
                <i class="fas fa-spinner fa-spin mr-2"></i>Loading booking details...
            </div>
        `;
    }

    fetch(`${CONFIG.bookingDetailsBaseUrl}/${bookingId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            return res.json();
        })
        .then(data => {
            const isConfirmed = ['confirmed', 'approved', 'active'].includes((data.status || '').toLowerCase());
            const statusBadge = isConfirmed ? 'bg-red-500' : 'bg-amber-500';

            if (content) {
                content.innerHTML = `
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status</span>
                            <span class="px-3 py-1 rounded-full text-white ${statusBadge}">
                                ${data.status ?? 'N/A'}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Car</span>
                            <span class="font-semibold">${data.car?.name ?? 'N/A'}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Customer</span>
                            <span>${data.user?.name ?? 'N/A'}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Email</span>
                            <span>${data.user?.email ?? 'N/A'}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Phone</span>
                            <span>${data.user?.phone ?? 'N/A'}</span>
                        </div>

                        <div class="border-t pt-3">
                            <p><strong>Pickup:</strong> ${data.pickup_at ?? 'N/A'}</p>
                            <p><strong>Return:</strong> ${data.return_at ?? 'N/A'}</p>
                            <p><strong>Service Type:</strong> ${data.service_type ?? 'N/A'}</p>
                            <p><strong>Location:</strong> ${data.service_location ?? 'N/A'}</p>
                        </div>

                        <div class="border-t pt-3 text-right font-bold text-lg">
                            ₱${data.total_price ?? '0.00'}
                        </div>
                    </div>
                `;
            }

            if (addBtn && data.car?.id && data.return_at_iso) {
                const available = getPickupFromReturn(data.return_at_iso);

                addBtn.dataset.carId = data.car.id;
                addBtn.dataset.carName = data.car.name ?? 'N/A';
                addBtn.dataset.pickupDate = available.pickupDate;
                addBtn.dataset.pickupTime = available.pickupTime;
                addBtn.dataset.returnDate = available.pickupDate;
                addBtn.dataset.returnTime = available.pickupTime;

                addBtn.classList.remove('hidden');
            }
        })
        .catch((error) => {
            console.error('Booking modal load failed:', error);

            if (content) {
                content.innerHTML = `
                    <div class="text-red-500 text-center py-4">
                        <i class="fas fa-exclamation-circle mr-2"></i>Failed to load booking details.
                    </div>
                `;
            }
        });
}

function closeBookingModal() {
    qs('#bookingModal')?.classList.add('hidden');
}

function openCreateFromBookingModal() {
    const addBtn = qs('#openAddBookingFromDetailsBtn');

    if (!addBtn) return;

    closeBookingModal();

    openCreateBookingModal(
        addBtn.dataset.carId,
        addBtn.dataset.pickupDate,
        addBtn.dataset.carName,
        addBtn.dataset.pickupTime,
        addBtn.dataset.returnDate,
        addBtn.dataset.returnTime
    );
}

function openCreateBookingModal(
    carId,
    pickupDate,
    carName,
    pickupTime = '09:00',
    returnDate = null,
    returnTime = null
) {
    const carIdInput = qs('#modal_car_id');
    const carNameInput = qs('#modal_car_name');
    const pickupDateInput = qs('#modal_pickup_date');
    const pickupTimeInput = qs('input[name="pickup_time"]');
    const returnDateInput = qs('#modal_return_date');
    const returnTimeInput = qs('input[name="return_time"]');

    const todayStr = getTodayStr();
    const safePickupDate = pickupDate && pickupDate >= todayStr ? pickupDate : todayStr;
    const safeReturnDate = returnDate && returnDate >= safePickupDate ? returnDate : safePickupDate;

    if (carIdInput) carIdInput.value = carId;
    if (carNameInput) carNameInput.value = carName;
    if (pickupDateInput) pickupDateInput.value = safePickupDate;
    if (pickupTimeInput) pickupTimeInput.value = pickupTime;
    if (returnDateInput) returnDateInput.value = safeReturnDate;
    if (returnTimeInput) returnTimeInput.value = returnTime ?? pickupTime;

    syncReturnMinDate();
    syncReturnToPickup();
    toggleServiceLocation();

    qs('#createBookingError')?.classList.add('hidden');
    qs('#createBookingSuccess')?.classList.add('hidden');
    qs('#liveAvailabilityBox')?.classList.add('hidden');
    qs('#createBookingModal')?.classList.remove('hidden');

    const defaultRadio = qs('input[name="customer_type"][value="new"]');

    if (defaultRadio) {
        defaultRadio.checked = true;
    }

    toggleCustomerType();

    setTimeout(checkExactAvailability, 100);
}

function closeCreateBookingModal() {
    const form = qs('#createBookingForm');
    const saveBtn = qs('#saveBookingBtn');
    const liveBox = qs('#liveAvailabilityBox');
    const dropdown = qs('#existingCustomerDropdown');

    form?.reset();

    qs('#createBookingModal')?.classList.add('hidden');
    qs('#createBookingError')?.classList.add('hidden');
    qs('#createBookingSuccess')?.classList.add('hidden');
    qs('#existingCustomerResult')?.classList.add('hidden');
    qs('#existingCustomerNotFound')?.classList.add('hidden');

    const existingUserId = qs('#existing_user_id');

    if (existingUserId) {
        existingUserId.value = '';
    }

    if (dropdown) {
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
    }

    if (liveBox) {
        liveBox.classList.add('hidden');
        liveBox.classList.remove(
            'bg-red-50', 'border-red-200', 'text-red-700',
            'bg-emerald-50', 'border-emerald-200', 'text-emerald-700'
        );
        liveBox.textContent = '';
    }

    if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    const defaultRadio = qs('input[name="customer_type"][value="new"]');

    if (defaultRadio) {
        defaultRadio.checked = true;
    }

    syncReturnMinDate();
    toggleCustomerType();
    toggleServiceLocation();
}

async function submitCreateBookingForm(e) {
    e.preventDefault();

    const form = e.target;
    const errorBox = qs('#createBookingError');
    const successBox = qs('#createBookingSuccess');

    errorBox?.classList.add('hidden');
    successBox?.classList.add('hidden');

    const formData = new FormData(form);

    try {
        const res = await fetch(CONFIG.bookingStoreUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            if (errorBox) {
                errorBox.textContent = data.message || 'Failed to create booking.';
                errorBox.classList.remove('hidden');
            }

            return;
        }

        if (successBox) {
            successBox.textContent = data.message || 'Booking created successfully.';
            successBox.classList.remove('hidden');
        }

        setTimeout(() => {
            closeCreateBookingModal();
            fetchFilteredData();
        }, 900);
    } catch (error) {
        console.error('Create booking failed:', error);

        if (errorBox) {
            errorBox.textContent = 'Something went wrong while creating the booking.';
            errorBox.classList.remove('hidden');
        }
    }
}

function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');

    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';

        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type = 'password';

        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

function bindCalendarScrollButtons() {
    const calendarScrollWrapper = document.getElementById('calendarScrollWrapper');
    const scrollLeftBtn = document.getElementById('calendarScrollLeft');
    const scrollRightBtn = document.getElementById('calendarScrollRight');

    if (!calendarScrollWrapper || !scrollLeftBtn || !scrollRightBtn) {
        return;
    }

    scrollLeftBtn.addEventListener('click', function () {
        calendarScrollWrapper.scrollBy({
            left: -360,
            behavior: 'smooth'
        });
    });

    scrollRightBtn.addEventListener('click', function () {
        calendarScrollWrapper.scrollBy({
            left: 360,
            behavior: 'smooth'
        });
    });
}

function bindStaticEvents() {
    const form = qs('#filterForm');
    const searchInput = form?.querySelector('input[name="search"]');
    const brandSelect = form?.querySelector('select[name="brand_id"]');
    const modal = qs('#bookingModal');
    const createModal = qs('#createBookingModal');
    const createBookingForm = qs('#createBookingForm');
    const existingCustomerSearchInput = qs('#existing_customer_search');
    const existingCustomerDropdown = qs('#existingCustomerDropdown');
    const serviceTypeSelect = qs('#service_type_id');

    brandSelect?.addEventListener('change', fetchFilteredData);

    qsa('input[type="radio"][name="view"]', form || document).forEach((radio) => {
        radio.addEventListener('change', fetchFilteredData);
    });

    let debounceTimer;

    searchInput?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchFilteredData, 400);
    });

    qs('#modal_pickup_date')?.addEventListener('change', () => {
        syncReturnMinDate();
        syncReturnToPickup();
        checkExactAvailability();
    });

    qs('input[name="pickup_time"]')?.addEventListener('change', () => {
        syncReturnToPickup();
        checkExactAvailability();
    });

    qs('#modal_return_date')?.addEventListener('change', checkExactAvailability);
    qs('input[name="return_time"]')?.addEventListener('change', checkExactAvailability);

    serviceTypeSelect?.addEventListener('change', toggleServiceLocation);

    modal?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeBookingModal();
        }
    });

    createModal?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeCreateBookingModal();
        }
    });

    let existingCustomerDebounce;

    existingCustomerSearchInput?.addEventListener('input', function () {
        const keyword = this.value.trim();

        clearTimeout(existingCustomerDebounce);

        if (keyword.length < 1) {
            if (existingCustomerDropdown) {
                existingCustomerDropdown.classList.add('hidden');
                existingCustomerDropdown.innerHTML = '';
            }

            qs('#existingCustomerResult')?.classList.add('hidden');
            qs('#existingCustomerNotFound')?.classList.add('hidden');

            if (qs('#existing_user_id')) {
                qs('#existing_user_id').value = '';
            }

            return;
        }

        existingCustomerDebounce = setTimeout(() => {
            searchExistingCustomers(keyword);
        }, 250);
    });

    document.addEventListener('click', function (e) {
        if (
            existingCustomerSearchInput &&
            existingCustomerDropdown &&
            !existingCustomerSearchInput.contains(e.target) &&
            !existingCustomerDropdown.contains(e.target)
        ) {
            existingCustomerDropdown.classList.add('hidden');
        }
    });

    createBookingForm?.addEventListener('submit', submitCreateBookingForm);

    syncReturnMinDate();
    toggleCustomerType();
    toggleServiceLocation();
}

window.openBookingModal = openBookingModal;
window.closeBookingModal = closeBookingModal;
window.openCreateBookingModal = openCreateBookingModal;
window.closeCreateBookingModal = closeCreateBookingModal;
window.openCreateFromBookingModal = openCreateFromBookingModal;
window.toggleCustomerType = toggleCustomerType;
window.togglePassword = togglePassword;

document.addEventListener('DOMContentLoaded', () => {
    bindStaticEvents();
    bindCalendarScrollButtons();
    setInterval(fetchFilteredData, 30000);
});