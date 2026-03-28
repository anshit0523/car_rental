document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('addCarModal');
    const addCarBtn = document.getElementById('addCarBtn');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const addCarForm = document.getElementById('addCarForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtnText = document.getElementById('submitBtnText');

    // Delete Modal
    const deleteModal = document.getElementById('deleteModal');
    const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
    const deleteCancelBtn = document.getElementById('deleteCancelBtn');
    let deleteForm = null;

    // Form fields
    const carId = document.getElementById('carId');
    const methodField = document.getElementById('methodField');
    const brandId = document.getElementById('brandId');
    const modelInput = document.getElementById('modelInput');
    const transmissionId = document.getElementById('transmissionId');
    const fuelTypeId = document.getElementById('fuelTypeId');
    const seatsInput = document.getElementById('seatsInput');
    const priceInput = document.getElementById('priceInput');
    const descriptionInput = document.getElementById('descriptionInput');
    const trackerId = document.getElementById('trackerId');
    const activeCheckbox = document.getElementById('activeCheckbox');

    // Routes
    const storeRoute = addCarForm.getAttribute('data-store-route') || '/admin/cars';
    const updateRoute = addCarForm.getAttribute('data-update-route') || '/admin/cars/:id';

    // Open Add Car Modal
    addCarBtn.addEventListener('click', () => {
        resetForm();
        modal.classList.remove('hidden');
        modalTitle.textContent = 'Add New Car';
        submitBtnText.textContent = 'Add Car';
    });

    // Open Edit Car Modal
    document.querySelectorAll('.editCarBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const car = JSON.parse(this.dataset.car);
            populateForm(car);
            modal.classList.remove('hidden');
            modalTitle.textContent = 'Edit Car';
            submitBtnText.textContent = 'Update Car';
        });
    });

    // Close Add/Edit Modal
    closeModal.addEventListener('click', closeMainModal);
    cancelBtn.addEventListener('click', closeMainModal);

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeMainModal();
        }

        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });

    function closeMainModal() {
        modal.classList.add('hidden');
    }

    function closeDeleteModal() {
        deleteModal.classList.add('hidden');
        deleteForm = null;
    }

    // Delete functionality
    document.querySelectorAll('.deleteCarBtn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const formId = this.getAttribute('data-form-id');
            deleteForm = document.getElementById(formId);
            deleteModal.classList.remove('hidden');
        });
    });

    // Confirm delete
    deleteConfirmBtn.addEventListener('click', function () {
        if (deleteForm) {
            deleteForm.submit();
        }
    });

    // Cancel delete
    deleteCancelBtn.addEventListener('click', function () {
        closeDeleteModal();
    });

    // Populate form for editing
    function populateForm(car) {
        carId.value = car.id || '';
        methodField.value = 'PUT';
        brandId.value = car.brand_id || '';
        modelInput.value = car.model || '';
        transmissionId.value = car.transmission_id || '';
        fuelTypeId.value = car.fuel_type_id || '';
        seatsInput.value = car.seats || 4;
        priceInput.value = car.price_per_day || '';
        descriptionInput.value = car.description || '';
        trackerId.value = car.tracker_id || '';
        activeCheckbox.checked = Number(car.active) === 1 || car.active === true;

        addCarForm.action = updateRoute.replace(':id', car.id);
    }

    // Reset form for adding
    function resetForm() {
        addCarForm.reset();
        carId.value = '';
        methodField.value = 'POST';
        addCarForm.action = storeRoute;

        seatsInput.value = 4;
        trackerId.value = '';
        activeCheckbox.checked = true;
    }
});