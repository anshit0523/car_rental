document.addEventListener('DOMContentLoaded', function () {
    const statusFilter = document.getElementById('statusFilter');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const filterBtn = document.getElementById('filterBtn');
    const tableBody = document.querySelector('tbody');
    const paginationContainer = document.querySelector('.p-2.border-t');
    const statsGrid = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-3'); // Stats cards container

    // ── Auto-filter on change ────────────────────────────────────────────────
    function applyFilters() {
        const params = new URLSearchParams();

        if (statusFilter.value) {
            params.append('status', statusFilter.value);
        }
        if (dateFrom.value) {
            params.append('date_from', dateFrom.value);
        }
        if (dateTo.value) {
            params.append('date_to', dateTo.value);
        }

        // Show loading state on table
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p>Loading...</p>
                </td>
            </tr>`;

        // Fetch filtered data
        fetch(`${window.location.pathname}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            }
        })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // ✅ Update stats cards
            const newStatsGrid = doc.querySelector('.grid.grid-cols-1.md\\:grid-cols-3');
            if (newStatsGrid && statsGrid) {
                statsGrid.innerHTML = newStatsGrid.innerHTML;
            }

            // ✅ Update table body
            const newTableBody = doc.querySelector('tbody');
            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }

            // ✅ Update pagination
            const newPagination = doc.querySelector('.p-2.border-t');
            if (newPagination && paginationContainer) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            }

            // Update URL without reload
            const newUrl = params.toString() 
                ? `${window.location.pathname}?${params.toString()}`
                : window.location.pathname;
            history.replaceState(null, '', newUrl);
        })
        .catch(err => {
            console.error('Filter error:', err);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                        <p>Failed to load data. Please try again.</p>
                    </td>
                </tr>`;
        });
    }

    // ── Event listeners ──────────────────────────────────────────────────────
    statusFilter.addEventListener('change', applyFilters);
    dateFrom.addEventListener('change', applyFilters);
    dateTo.addEventListener('change', applyFilters);

    // Optional: Keep the Filter button functional for manual trigger
    if (filterBtn) {
        filterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            applyFilters();
        });
    }
});