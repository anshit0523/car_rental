document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('addCarModal');
    const addCarBtn = document.getElementById('addCarBtn');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const addCarForm = document.getElementById('addCarForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtnText = document.getElementById('submitBtnText');

    const deleteModal = document.getElementById('deleteModal');
    const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
    const deleteCancelBtn = document.getElementById('deleteCancelBtn');
    let deleteForm = null;

    const carId = document.getElementById('carId');
    const methodField = document.getElementById('methodField');
    const brandId = document.getElementById('brandId');
    const carTypeId = document.getElementById('carTypeId');
    const modelInput = document.getElementById('modelInput');
    const transmissionId = document.getElementById('transmissionId');
    const fuelTypeId = document.getElementById('fuelTypeId');
    const seatsInput = document.getElementById('seatsInput');
    const priceInput = document.getElementById('priceInput');
    const descriptionInput = document.getElementById('descriptionInput');
    const trackerId = document.getElementById('trackerId');
    const activeCheckbox = document.getElementById('activeCheckbox');

    const storeRoute = addCarForm?.getAttribute('data-store-route') || '/admin/cars';

    if (addCarBtn) {
        addCarBtn.addEventListener('click', () => {
            resetForm();
            modal.classList.remove('hidden');
            modalTitle.textContent = 'Add New Car';
            submitBtnText.textContent = 'Add Car';
        });
    }

    document.querySelectorAll('.editCarBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const car = {
                id: this.dataset.id || '',
                brand_id: this.dataset.brandId || '',
                car_type_id: this.dataset.carTypeId || '',
                model: this.dataset.model || '',
                transmission_id: this.dataset.transmissionId || '',
                fuel_type_id: this.dataset.fuelTypeId || '',
                seats: this.dataset.seats || 4,
                price_per_day: this.dataset.pricePerDay || '',
                description: this.dataset.description || '',
                active: this.dataset.active || '0',
                tracker_id: this.dataset.trackerId || ''
            };

            populateForm(car);
            modal.classList.remove('hidden');
            modalTitle.textContent = 'Edit Car';
            submitBtnText.textContent = 'Update Car';
        });
    });

    if (closeModal) {
        closeModal.addEventListener('click', closeMainModal);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeMainModal);
    }

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeMainModal();
        }

        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });

    function closeMainModal() {
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function closeDeleteModal() {
        if (deleteModal) {
            deleteModal.classList.add('hidden');
        }
        deleteForm = null;
    }

    document.querySelectorAll('.deleteCarBtn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const formId = this.getAttribute('data-form-id');
            deleteForm = document.getElementById(formId);

            if (deleteModal) {
                deleteModal.classList.remove('hidden');
            }
        });
    });

    if (deleteConfirmBtn) {
        deleteConfirmBtn.addEventListener('click', function () {
            if (deleteForm) {
                deleteForm.submit();
            }
        });
    }

    if (deleteCancelBtn) {
        deleteCancelBtn.addEventListener('click', function () {
            closeDeleteModal();
        });
    }

    function populateForm(car) {
        carId.value = car.id || '';
        methodField.value = 'PUT';

        brandId.value = car.brand_id || '';
        carTypeId.value = car.car_type_id || '';
        modelInput.value = car.model || '';
        transmissionId.value = car.transmission_id || '';
        fuelTypeId.value = car.fuel_type_id || '';
        seatsInput.value = car.seats || 4;
        priceInput.value = car.price_per_day || '';
        descriptionInput.value = car.description || '';
        trackerId.value = car.tracker_id || '';

        activeCheckbox.checked =
            Number(car.active) === 1 ||
            car.active === true ||
            car.active === '1';

        addCarForm.action = `/admin/cars/${car.id}`;
    }

    function resetForm() {
        addCarForm.reset();
        carId.value = '';
        methodField.value = 'POST';
        addCarForm.action = storeRoute;

        brandId.value = '';
        carTypeId.value = '';
        transmissionId.value = '';
        fuelTypeId.value = '';
        modelInput.value = '';
        seatsInput.value = 4;
        priceInput.value = '';
        descriptionInput.value = '';
        trackerId.value = '';
        activeCheckbox.checked = true;
    }
});