document.addEventListener('DOMContentLoaded', function () {
    const userForm = document.getElementById('userForm');
    const formMethod = document.getElementById('formMethod');

    const filterForm = document.getElementById('filterForm');
    const userSearch = document.getElementById('userSearch');
    const roleFilter = document.getElementById('roleFilter');

    let searchTimeout;

    function submitFilterForm() {
        if (filterForm) {
            filterForm.submit();
        }
    }

    if (userSearch) {
        userSearch.addEventListener('input', function () {
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                submitFilterForm();
            }, 400);
        });
    }

    if (roleFilter) {
        roleFilter.addEventListener('change', function () {
            submitFilterForm();
        });
    }

    window.openAddModal = function () {
        document.getElementById('modalTitle').innerText = 'Add User';
        userForm.action = userForm.dataset.storeRoute;
        formMethod.value = 'POST';

        userForm.reset();
        document.getElementById('name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('password').value = '';
        document.getElementById('password_confirmation').value = '';
        document.getElementById('role').value = '';

        showModal();
    };

    window.resetAndOpenAddModal = function () {
        document.getElementById('modalTitle').innerText = 'Add User';
        userForm.action = userForm.dataset.storeRoute;
        formMethod.value = 'POST';

        userForm.reset();
        document.getElementById('name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('password').value = '';
        document.getElementById('password_confirmation').value = '';
        document.getElementById('role').value = '';

        showModal();
    };

    window.openEditModal = function (user) {
        document.getElementById('modalTitle').innerText = 'Edit User';

        userForm.action = userForm.dataset.updateRoute.replace(':id', user.id);
        formMethod.value = 'PUT';

        document.getElementById('name').value = user.name ?? '';
        document.getElementById('email').value = user.email ?? '';
        document.getElementById('password').value = '';
        document.getElementById('password_confirmation').value = '';
        document.getElementById('role').value = user.role_id ?? '';

        showModal();
    };

    function showModal() {
        const modal = document.getElementById('userModal');
        const modalContent = document.getElementById('modalContent');

        modal.classList.remove('hidden');

        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-90');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    window.closeModal = function () {
        const modal = document.getElementById('userModal');
        const modalContent = document.getElementById('modalContent');

        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-90');
        modalContent.classList.remove('scale-100');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    };

    window.showSuccess = function (message) {
        const modal = document.getElementById('successModal');
        const content = document.getElementById('successContent');
        const msg = document.getElementById('successMessage');

        msg.innerText = message;
        modal.classList.remove('hidden');

        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-90');
            content.classList.add('scale-100');
        }, 10);

        setTimeout(() => {
            closeSuccess();
        }, 2500);
    };

    window.closeSuccess = function () {
        const modal = document.getElementById('successModal');
        const content = document.getElementById('successContent');

        modal.classList.add('opacity-0');
        content.classList.add('scale-90');
        content.classList.remove('scale-100');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    };

    window.openDeleteModal = function (id) {
        const modal = document.getElementById('deleteModal');
        const content = document.getElementById('deleteContent');
        const deleteForm = document.getElementById('deleteForm');

        deleteForm.action = `/admin/users/${id}`;
        modal.classList.remove('hidden');

        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-90');
            content.classList.add('scale-100');
        }, 10);
    };

    window.closeDeleteModal = function () {
        const modal = document.getElementById('deleteModal');
        const content = document.getElementById('deleteContent');

        modal.classList.add('opacity-0');
        content.classList.add('scale-90');
        content.classList.remove('scale-100');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    };

    window.showError = function (message) {
        const modal = document.getElementById('errorModal');
        const content = document.getElementById('errorContent');
        const msg = document.getElementById('errorMessage');

        msg.innerText = message;
        modal.classList.remove('hidden');

        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-90');
            content.classList.add('scale-100');
        }, 10);

        setTimeout(() => {
            closeError();
        }, 2500);
    };

    window.closeError = function () {
        const modal = document.getElementById('errorModal');
        const content = document.getElementById('errorContent');

        modal.classList.add('opacity-0');
        content.classList.add('scale-90');
        content.classList.remove('scale-100');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    };
});