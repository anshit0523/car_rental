document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const searchInput = form?.querySelector('input[name="search"]');
    const statusSelect = form?.querySelector('select[name="status_id"]');

    const modal = document.getElementById('editModal');
    const closeModalBtn = document.getElementById('closeModal');
    const statusDropdown = document.getElementById('statusDropdown');
    const updateForm = document.getElementById('updateStatusForm');
    const statusHelpText = document.getElementById('statusHelpText');
    const saveStatusBtn = document.getElementById('saveStatusBtn');

    const adminMessageWrapper = document.getElementById('adminMessageWrapper');
    const adminMessage = document.getElementById('adminMessage');

    const viewModal = document.getElementById('viewModal');
    const closeViewModalBtn = document.getElementById('closeViewModal');

    let typingTimer;

    if (searchInput) {
        searchInput.addEventListener('keyup', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => form.submit(), 500);
        });
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', () => form.submit());
    }

    const allowedTransitions = {
        Pending: ['Cancelled'],
        Confirmed: ['Cancelled'],
        Return: ['Completed', 'Checkup', 'Damage', 'Needs Repair'],
        Active: [],
        Completed: [],
        Cancelled: [],
        Checkup: [],
        Damage: [],
        'Needs Repair': [],
        Failed: [],
    };

    const statusMap = window.bookingStatusMap || {};

    function toggleAdminMessageField() {
        if (!statusDropdown || !adminMessageWrapper) return;

        const selectedText = statusDropdown.options[statusDropdown.selectedIndex]?.text?.trim() || '';
        const showFor = ['Completed', 'Checkup', 'Damage', 'Needs Repair'];

        if (showFor.includes(selectedText)) {
            adminMessageWrapper.classList.remove('hidden');
        } else {
            adminMessageWrapper.classList.add('hidden');
            if (adminMessage) {
                adminMessage.value = '';
            }
        }
    }

    document.querySelectorAll('.editBookingBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const bookingId = btn.getAttribute('data-booking-id');
            const currentStatusName = (btn.getAttribute('data-status-name') || '').trim();

            updateForm.action = `/admin/bookings/${bookingId}/update-status`;

            const allowed = allowedTransitions[currentStatusName] || [];

            statusDropdown.innerHTML = '';

            if (allowed.length > 0) {
                allowed.forEach(statusName => {
                    const statusId = statusMap[statusName];

                    if (statusId) {
                        const option = document.createElement('option');
                        option.value = statusId;
                        option.textContent = statusName;
                        statusDropdown.appendChild(option);
                    }
                });

                statusDropdown.disabled = false;

                if (saveStatusBtn) {
                    saveStatusBtn.disabled = false;
                    saveStatusBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }

                statusHelpText.textContent = '';
                statusHelpText.classList.add('hidden');

                toggleAdminMessageField();
            } else {
                statusDropdown.disabled = true;

                if (saveStatusBtn) {
                    saveStatusBtn.disabled = true;
                    saveStatusBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }

                statusHelpText.textContent = `No manual action available for ${currentStatusName}.`;
                statusHelpText.classList.remove('hidden');

                if (adminMessageWrapper) adminMessageWrapper.classList.add('hidden');
                if (adminMessage) adminMessage.value = '';
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    if (statusDropdown) {
        statusDropdown.addEventListener('change', toggleAdminMessageField);
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        statusDropdown.innerHTML = '';
        statusDropdown.disabled = false;

        if (saveStatusBtn) {
            saveStatusBtn.disabled = false;
            saveStatusBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        statusHelpText.textContent = '';
        statusHelpText.classList.add('hidden');

        if (adminMessageWrapper) adminMessageWrapper.classList.add('hidden');
        if (adminMessage) adminMessage.value = '';
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', e => {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    document.querySelectorAll('.viewBookingBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const bookingId = btn.getAttribute('data-booking-id');
            const serviceLocationWrapper = document.getElementById('viewServiceLocationWrapper');

            viewModal.classList.remove('hidden');
            viewModal.classList.add('flex');

            document.getElementById('viewBookingId').textContent = '';
            document.getElementById('viewUserName').textContent = '';
            document.getElementById('viewUserEmail').textContent = '';
            document.getElementById('viewUserPhone').textContent = '';
            document.getElementById('viewServiceType').textContent = '';
            document.getElementById('viewServiceLocation').textContent = '';

            if (serviceLocationWrapper) {
                serviceLocationWrapper.classList.remove('hidden');
            }

            try {
                const response = await fetch(`/admin/bookings/${bookingId}/json`);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to load booking details.');
                }

                document.getElementById('viewBookingId').textContent = data.id ?? '-';
                document.getElementById('viewUserName').textContent = data.user?.name ?? '-';
                document.getElementById('viewUserEmail').textContent = data.user?.email ?? '-';
                document.getElementById('viewUserPhone').textContent = data.user?.phone ?? '-';

                const serviceType = data.service_type ?? '-';
                document.getElementById('viewServiceType').textContent = serviceType;
                document.getElementById('viewServiceLocation').textContent = data.service_location ?? '-';

                if (serviceLocationWrapper) {
                    if (serviceType.toLowerCase() === 'pickup') {
                        serviceLocationWrapper.classList.add('hidden');
                    } else {
                        serviceLocationWrapper.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error(error);

                document.getElementById('viewBookingId').textContent = 'Error';
                document.getElementById('viewUserName').textContent = '-';
                document.getElementById('viewUserEmail').textContent = '-';
                document.getElementById('viewUserPhone').textContent = '-';
                document.getElementById('viewServiceType').textContent = '-';
                document.getElementById('viewServiceLocation').textContent = '-';

                if (serviceLocationWrapper) {
                    serviceLocationWrapper.classList.remove('hidden');
                }
            }
        });
    });

    function closeViewModal() {
        if (!viewModal) return;
        viewModal.classList.add('hidden');
        viewModal.classList.remove('flex');
    }

    if (closeViewModalBtn) {
        closeViewModalBtn.addEventListener('click', closeViewModal);
    }

    if (viewModal) {
        viewModal.addEventListener('click', e => {
            if (e.target === viewModal) {
                closeViewModal();
            }
        });
    }
});