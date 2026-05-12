let statusFilter;
let paymentTypeFilter;
let dateFrom;
let dateTo;
let tableBody;
let paginationContainer;
let statsGrid;

document.addEventListener('DOMContentLoaded', function () {
    statusFilter = document.getElementById('statusFilter');
    paymentTypeFilter = document.getElementById('paymentTypeFilter');
    dateFrom = document.getElementById('dateFrom');
    dateTo = document.getElementById('dateTo');
    tableBody = document.querySelector('tbody');
    paginationContainer = document.querySelector('.p-2.border-t');
    statsGrid = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-3');

    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (paymentTypeFilter) paymentTypeFilter.addEventListener('change', applyFilters);
    if (dateFrom) dateFrom.addEventListener('change', applyFilters);
    if (dateTo) dateTo.addEventListener('change', applyFilters);

    const receiptModal = document.getElementById('receiptModal');
    if (receiptModal) {
        receiptModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeReceiptModal();
            }
        });
    }

    const approveModal = document.getElementById('approveModal');
    if (approveModal) {
        approveModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });
    }

    const rejectModal = document.getElementById('rejectModal');
    if (rejectModal) {
        rejectModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    }
});

function paymentTypeLabel(type) {
    return type === 'issue' ? 'Issue Payment' : 'Booking Payment';
}

function paymentActionText(type) {
    return type === 'issue' ? 'issue payment' : 'booking payment';
}

// ───────────────── FILTER FUNCTION ─────────────────
function applyFilters() {
    const params = new URLSearchParams();

    if (statusFilter && statusFilter.value) {
        params.append('status', statusFilter.value);
    }

    if (paymentTypeFilter && paymentTypeFilter.value) {
        params.append('payment_type', paymentTypeFilter.value);
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
                <td colspan="9" class="px-6 py-8 text-center text-slate-400">
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
                        <td colspan="9" class="px-6 py-8 text-center text-red-400">
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
function openApproveModal(
    paymentId,
    bookingId = '',
    paymentType = 'booking',
    issueTitle = '',
    approveUrl = ''
) {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    const bookingLabel = document.getElementById('approveBookingLabel');

    const title = document.getElementById('approveModalTitle');
    const subtitle = document.getElementById('approveModalSubtitle');
    const typeBadge = document.getElementById('approveTypeLabel');
    const typeHint = document.getElementById('approveTypeHint');
    const issueText = document.getElementById('approveIssueText');

    if (form) {
        form.action = approveUrl || `/admin/payments/${paymentId}/approve`;
    }

    if (bookingLabel) {
        bookingLabel.textContent = bookingId ? `#${bookingId}` : '';
    }

    if (title) {
        title.textContent = `Approve ${paymentTypeLabel(paymentType)}`;
    }

    if (subtitle) {
        subtitle.textContent = `Are you sure you want to approve this ${paymentActionText(paymentType)}?`;
    }

    if (typeBadge) {
        typeBadge.textContent = paymentTypeLabel(paymentType);
        typeBadge.className = paymentType === 'issue'
            ? 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800'
            : 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800';
    }

    if (typeHint) {
        typeHint.textContent = paymentType === 'issue'
            ? 'This will mark the payment as completed and resolve the related return issue.'
            : 'This will mark the payment as completed and confirm the booking.';
    }

    if (issueText) {
        if (paymentType === 'issue' && issueTitle) {
            issueText.textContent = `Issue: ${issueTitle}`;
            issueText.classList.remove('hidden');
        } else {
            issueText.textContent = '';
            issueText.classList.add('hidden');
        }
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
    const issueText = document.getElementById('approveIssueText');

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

    if (issueText) {
        issueText.textContent = '';
        issueText.classList.add('hidden');
    }
}

// ───────────────── REJECT MODAL ─────────────────
function openRejectModal(
    paymentId,
    bookingId = '',
    paymentType = 'booking',
    issueTitle = '',
    rejectUrl = ''
) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const bookingLabel = document.getElementById('rejectBookingLabel');

    const title = document.getElementById('rejectModalTitle');
    const subtitle = document.getElementById('rejectModalSubtitle');
    const typeBadge = document.getElementById('rejectTypeLabel');
    const typeHint = document.getElementById('rejectTypeHint');
    const issueText = document.getElementById('rejectIssueText');

    if (form) {
        form.action = rejectUrl || `/admin/payments/${paymentId}/reject`;
    }

    if (bookingLabel) {
        bookingLabel.textContent = bookingId ? `#${bookingId}` : '';
    }

    if (title) {
        title.textContent = `Reject ${paymentTypeLabel(paymentType)}`;
    }

    if (subtitle) {
        subtitle.textContent = `Are you sure you want to reject this ${paymentActionText(paymentType)}?`;
    }

    if (typeBadge) {
        typeBadge.textContent = paymentTypeLabel(paymentType);
        typeBadge.className = paymentType === 'issue'
            ? 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800'
            : 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800';
    }

    if (typeHint) {
        typeHint.textContent = paymentType === 'issue'
            ? 'Please provide the reason for rejecting this issue payment before continuing.'
            : 'Please provide the reason for rejecting this booking payment before continuing.';
    }

    if (issueText) {
        if (paymentType === 'issue' && issueTitle) {
            issueText.textContent = `Issue: ${issueTitle}`;
            issueText.classList.remove('hidden');
        } else {
            issueText.textContent = '';
            issueText.classList.add('hidden');
        }
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
    const issueText = document.getElementById('rejectIssueText');

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

    if (issueText) {
        issueText.textContent = '';
        issueText.classList.add('hidden');
    }
}