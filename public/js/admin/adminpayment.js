let statusFilter;
let dateFrom;
let dateTo;
let tableBody;
let paginationContainer;
let statsGrid;

document.addEventListener('DOMContentLoaded', function () {
    statusFilter = document.getElementById('statusFilter');
    dateFrom = document.getElementById('dateFrom');
    dateTo = document.getElementById('dateTo');
    tableBody = document.querySelector('tbody');
    paginationContainer = document.querySelector('.p-2.border-t');
    statsGrid = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-3');

    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (dateFrom) dateFrom.addEventListener('change', applyFilters);
    if (dateTo) dateTo.addEventListener('change', applyFilters);

    // Receipt modal outside click
    const receiptModal = document.getElementById('receiptModal');
    if (receiptModal) {
        receiptModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeReceiptModal();
            }
        });
    }

    // Approve modal outside click
    const approveModal = document.getElementById('approveModal');
    if (approveModal) {
        approveModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });
    }

    // Reject modal outside click
    const rejectModal = document.getElementById('rejectModal');
    if (rejectModal) {
        rejectModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    }
});

// ───────────────── FILTER FUNCTION ─────────────────
function applyFilters() {
    const params = new URLSearchParams();

    if (statusFilter && statusFilter.value) {
        params.append('status', statusFilter.value);
    }

    if (dateFrom && dateFrom.value) {
        params.append('date_from', dateFrom.value);
    }

    if (dateTo && dateTo.value) {
        params.append('date_to', dateTo.value);
    }

    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p>Loading...</p>
                </td>
            </tr>`;
    }

    fetch(`${window.location.pathname}?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newStats = doc.querySelector('.grid.grid-cols-1.md\\:grid-cols-3');
            if (newStats && statsGrid) {
                statsGrid.innerHTML = newStats.innerHTML;
            }

            const newBody = doc.querySelector('tbody');
            if (newBody && tableBody) {
                tableBody.innerHTML = newBody.innerHTML;
            }

            const newPagination = doc.querySelector('.p-2.border-t');
            if (newPagination && paginationContainer) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            }
        })
        .catch(err => {
            console.error('Filter error:', err);

            if (tableBody) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-red-400">
                            <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                            <p>Failed to load filtered payments.</p>
                        </td>
                    </tr>`;
            }
        });
}

// ───────────────── RECEIPT MODAL ─────────────────
function openReceiptModal(image) {
    if (!image) {
        alert('No receipt uploaded.');
        return;
    }

    const modal = document.getElementById('receiptModal');
    const img = document.getElementById('receiptImage');

    if (img) {
        img.src = image;
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeReceiptModal() {
    const modal = document.getElementById('receiptModal');
    const img = document.getElementById('receiptImage');

    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    if (img) {
        img.src = '';
    }
}

// ───────────────── APPROVE MODAL ─────────────────
function openApproveModal(paymentId, bookingId = '') {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    const bookingLabel = document.getElementById('approveBookingLabel');

    if (form) {
        form.action = `/admin/payments/${paymentId}/approve`;
    }

    if (bookingLabel) {
        bookingLabel.textContent = bookingId ? `#${bookingId}` : '';
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeApproveModal() {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    const bookingLabel = document.getElementById('approveBookingLabel');

    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    if (form) {
        form.action = '';
    }

    if (bookingLabel) {
        bookingLabel.textContent = '';
    }
}

// ───────────────── REJECT MODAL ─────────────────
function openRejectModal(paymentId, bookingId = '') {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const bookingLabel = document.getElementById('rejectBookingLabel');

    if (form) {
        form.action = `/admin/payments/${paymentId}/reject`;
    }

    if (bookingLabel) {
        bookingLabel.textContent = bookingId ? `#${bookingId}` : '';
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const bookingLabel = document.getElementById('rejectBookingLabel');

    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    if (form) {
        form.reset();
        form.action = '';
    }

    if (bookingLabel) {
        bookingLabel.textContent = '';
    }
}