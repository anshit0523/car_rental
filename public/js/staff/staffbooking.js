document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editModal');
    const closeModal = document.getElementById('closeModal');
    const updateStatusForm = document.getElementById('updateStatusForm');
    const statusDropdown = document.getElementById('statusDropdown');
    const adminMessageWrapper = document.getElementById('adminMessageWrapper');
    const adminMessage = document.getElementById('adminMessage');
    const statusHelpText = document.getElementById('statusHelpText');

    const viewModal = document.getElementById('viewModal');
    const closeViewModal = document.getElementById('closeViewModal');

    const bookingStatusMap = window.bookingStatusMap || {};
    const updateUrlTemplate = window.staffBookingUpdateUrlTemplate || '';
    const viewUrlTemplate = window.staffBookingViewUrlTemplate || '';
    const returnIssueCreateUrlTemplate = window.staffReturnIssueCreateUrlTemplate || '';

    const statusOptionsByCurrent = {
        Pending: ['Cancelled'],
        Confirmed: ['Cancelled'],
        Return: ['Checkup', 'Damage', 'Needs Repair', 'Completed'],
        Active: [],
        Completed: [],
        Cancelled: [],
        Checkup: [],
        Damage: [],
        'Needs Repair': [],
        Failed: [],
    };

    function openModal(modal) {
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalFn(modal) {
        if (!modal) return;
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function resetEditModal() {
        if (updateStatusForm) updateStatusForm.reset();
        if (statusDropdown) statusDropdown.innerHTML = '';
        if (adminMessageWrapper) adminMessageWrapper.classList.add('hidden');
        if (adminMessage) adminMessage.value = '';
        if (statusHelpText) {
            statusHelpText.textContent = '';
            statusHelpText.classList.add('hidden');
        }
    }

    function fillStatusOptions(currentStatus) {
        if (!statusDropdown) return;

        statusDropdown.innerHTML = '';

        const allowed = statusOptionsByCurrent[currentStatus] || [];

        if (allowed.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No available status changes';
            statusDropdown.appendChild(option);

            if (statusHelpText) {
                statusHelpText.textContent = `No status changes are allowed from ${currentStatus || 'this status'}.`;
                statusHelpText.classList.remove('hidden');
            }

            if (adminMessageWrapper) adminMessageWrapper.classList.add('hidden');
            return;
        }

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Select a new status';
        placeholder.selected = true;
        placeholder.disabled = true;
        statusDropdown.appendChild(placeholder);

        allowed.forEach(statusName => {
            if (bookingStatusMap[statusName]) {
                const option = document.createElement('option');
                option.value = bookingStatusMap[statusName];
                option.textContent = statusName;
                option.dataset.statusName = statusName;
                statusDropdown.appendChild(option);
            }
        });

        if (statusHelpText) {
            statusHelpText.textContent = `Current status: ${currentStatus}.`;
            statusHelpText.classList.remove('hidden');
        }
    }

    function toggleAdminMessage(selectedStatusName) {
        if (!adminMessageWrapper) return;

        if (selectedStatusName === 'Completed') {
            adminMessageWrapper.classList.remove('hidden');
        } else {
            adminMessageWrapper.classList.add('hidden');
            if (adminMessage) adminMessage.value = '';
        }
    }

    document.querySelectorAll('.editBookingBtn').forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.dataset.bookingId;
            const currentStatus = (this.dataset.statusName || '').trim();

            resetEditModal();
            fillStatusOptions(currentStatus);

            if (updateStatusForm && updateUrlTemplate) {
                updateStatusForm.action = updateUrlTemplate.replace(':id', bookingId);
            }

            openModal(editModal);
        });
    });

    if (statusDropdown) {
        statusDropdown.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const selectedStatusName = selectedOption?.dataset?.statusName || '';

            toggleAdminMessage(selectedStatusName);
        });
    }

    if (updateStatusForm) {
        updateStatusForm.addEventListener('submit', function (e) {
            const selectedOption = statusDropdown?.options[statusDropdown.selectedIndex];
            const selectedStatusName = selectedOption?.dataset?.statusName || '';

            if (
                ['Damage', 'Needs Repair', 'Checkup'].includes(selectedStatusName) &&
                returnIssueCreateUrlTemplate
            ) {
                e.preventDefault();

                const bookingId = updateStatusForm.action.split('/').slice(-2)[0];
                window.location.href = returnIssueCreateUrlTemplate.replace(':id', bookingId);
            }
        });
    }

    if (closeModal) {
        closeModal.addEventListener('click', function () {
            closeModalFn(editModal);
            resetEditModal();
        });
    }

    if (editModal) {
        editModal.addEventListener('click', function (e) {
            if (e.target === editModal) {
                closeModalFn(editModal);
                resetEditModal();
            }
        });
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value ?? '-';
    }

    document.querySelectorAll('.viewBookingBtn').forEach(button => {
        button.addEventListener('click', async function () {
            const bookingId = this.dataset.bookingId;
            if (!viewUrlTemplate) return;

            try {
                const response = await fetch(viewUrlTemplate.replace(':id', bookingId), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to load booking details.');
                }

                setText('viewBookingId', data.id ? `#${data.id}` : '-');
                setText('viewStatus', data.status);
                setText('viewUserName', data.user?.name || 'N/A');
                setText('viewUserEmail', data.user?.email || 'N/A');
                setText('viewUserPhone', data.user?.phone || 'N/A');

                setText('viewCarName', `${data.car?.brand || 'N/A'} ${data.car?.model || ''}`.trim());
                setText('viewPlateNumber', data.car?.plate_number || 'N/A');

                setText('viewPickupAt', data.pickup_at || 'N/A');
                setText('viewReturnAt', data.return_at || 'N/A');
                setText('viewTotalPrice', data.total_price || '0.00');
                setText('viewServiceType', data.service_type || 'N/A');
                setText('viewServiceLocation', data.service_location || '-');

                const locationWrapper = document.getElementById('viewServiceLocationWrapper');
                if (locationWrapper) {
                    if (data.service_location && data.service_location !== 'N/A') {
                        locationWrapper.classList.remove('hidden');
                    } else {
                        locationWrapper.classList.add('hidden');
                    }
                }

                openModal(viewModal);
            } catch (error) {
                alert(error.message || 'Failed to load booking details.');
            }
        });
    });

    if (closeViewModal) {
        closeViewModal.addEventListener('click', function () {
            closeModalFn(viewModal);
        });
    }

    if (viewModal) {
        viewModal.addEventListener('click', function (e) {
            if (e.target === viewModal) {
                closeModalFn(viewModal);
            }
        });
    }
});