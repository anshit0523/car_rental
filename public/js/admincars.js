document.addEventListener("DOMContentLoaded", () => {

const addCarBtn = document.getElementById('addCarBtn');
    const addCarModal = document.getElementById('addCarModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');

    // Open modal
    addCarBtn.addEventListener('click', () => {
        addCarModal.classList.remove('hidden');
    });

    // Close modal
    closeModal.addEventListener('click', () => {
        addCarModal.classList.add('hidden');
    });

    cancelBtn.addEventListener('click', () => {
        addCarModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    addCarModal.addEventListener('click', (e) => {
        if (e.target === addCarModal) {
            addCarModal.classList.add('hidden');
        }
    });
});