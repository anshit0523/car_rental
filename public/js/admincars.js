document.addEventListener('DOMContentLoaded', function() {
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
    const deleteCloseBtn = document.getElementById('deleteCloseBtn');
    let deleteForm = null;

    // Get routes from data attributes or define them
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
        btn.addEventListener('click', function() {
            const car = JSON.parse(this.dataset.car);
            populateForm(car);
            modal.classList.remove('hidden');
            modalTitle.textContent = 'Edit Car';
            submitBtnText.textContent = 'Update Car';
        });
    });

    // Close Add/Edit Modal
    closeModal.addEventListener('click', () => modal.classList.add('hidden'));
    cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
    window.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
    });

    
    // Delete functionality
document.querySelectorAll('.deleteCarBtn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const formId = this.getAttribute('data-form-id');
        deleteForm = document.getElementById(formId);
        deleteModal.classList.remove('hidden');
    });
});

// Confirm delete
deleteConfirmBtn.addEventListener('click', function() {
    if (deleteForm) {
        deleteForm.submit();
    }
});

// Cancel delete
deleteCancelBtn.addEventListener('click', function() {
    deleteModal.classList.add('hidden');
});

// Close when clicking outside modal
window.addEventListener('click', (e) => {
    if (e.target === deleteModal) {
        deleteModal.classList.add('hidden');
    }
});


    // Populate form for editing
    function populateForm(car) {
        document.getElementById('carId').value = car.id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('brandId').value = car.brand_id;
        document.getElementById('modelInput').value = car.model;
        document.getElementById('transmissionId').value = car.transmission_id;
        document.getElementById('fuelTypeId').value = car.fuel_type_id;
        document.getElementById('seatsInput').value = car.seats;
        document.getElementById('priceInput').value = car.price_per_day;
        document.getElementById('descriptionInput').value = car.description || '';
        document.getElementById('activeCheckbox').checked = car.active;
        
        // Set form action to update route with car ID
        addCarForm.action = updateRoute.replace(':id', car.id);
    }

    // Reset form for adding
    function resetForm() {
        addCarForm.reset();
        document.getElementById('carId').value = '';
        document.getElementById('methodField').value = 'POST';
        addCarForm.action = storeRoute;
        document.getElementById('seatsInput').value = 4;
        document.getElementById('activeCheckbox').checked = true;
    }
});