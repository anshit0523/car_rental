(function () {
  const cfg = window.BOOKING_CFG || {};
  const dailyRate = Number(cfg.dailyRate || 0);
  const insuranceRate = Number(cfg.insuranceRate || 0);
  const taxRate = Number(cfg.taxRate || 0);
  const discountRate = Number(cfg.discountRate || 0);
  const unavailableUrl = cfg.unavailableUrl || "";

  let unavailableSet = new Set();
  let isSubmitting = false;

  function ymd(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  }

  function money(n) {
    return "₱" + Number(n).toFixed(2);
  }

  function calculateBaseTotal() {
    const pickupVal = document.querySelector('input[name="pickup_date"]')?.value;
    const returnVal = document.querySelector('input[name="return_date"]')?.value;
    if (!pickupVal || !returnVal) return null;

    const pickupDate = new Date(pickupVal + "T00:00:00");
    const returnDate = new Date(returnVal + "T00:00:00");
    if (returnDate <= pickupDate) return null;

    const days = Math.ceil((returnDate - pickupDate) / (1000 * 60 * 60 * 24));
    const subtotal = dailyRate * days;
    const subtotalWithInsurance = subtotal + insuranceRate;
    const taxes = subtotalWithInsurance * taxRate;
    const subtotalBeforeDiscount = subtotalWithInsurance + taxes;
    const discount = subtotalBeforeDiscount * discountRate;
    const total = subtotalBeforeDiscount - discount;

    const elDays = document.getElementById("rental-days");
    const elSubtotal = document.getElementById("subtotal");

    if (elDays) elDays.textContent = days;
    if (elSubtotal) elSubtotal.textContent = money(subtotal);

    return total;
  }

  function renderTotals() {
    const total = calculateBaseTotal();
    if (total === null) return;

    const elTotal = document.getElementById("total-price");
    const elTotalInput = document.getElementById("total-price-input");

    if (elTotal) elTotal.textContent = money(total);
    if (elTotalInput) elTotalInput.value = total.toFixed(2);
  }

  function initDatePicker() {
    const input = document.getElementById("pickup_date");
    if (!input || typeof flatpickr === "undefined") return;

    if (input._flatpickr) input._flatpickr.destroy();

    flatpickr("#pickup_date", {
      dateFormat: "Y-m-d",
      minDate: "today",
      disableMobile: true,
      showMonths: 1,
      appendTo: document.body,
      plugins: [new rangePlugin({ input: "#return_date" })],
      disable: [(date) => unavailableSet.has(ymd(date))],

      onReady: (selectedDates, dateStr, fp) => {
        const legend = document.createElement("div");
        legend.className = "fp-legend";
        legend.innerHTML = `
          <div class="item"><span class="dot red"></span> Unavailable</div>
          <div class="item"><span class="dot green"></span> Available</div>
        `;
        fp.calendarContainer.prepend(legend);
        fp.calendarContainer.classList.add("fp-center");
      },

      onOpen: (selectedDates, dateStr, fp) => {
        fp.calendarContainer.classList.add("fp-center");
        if (!document.querySelector(".fp-overlay")) {
          const overlay = document.createElement("div");
          overlay.className = "fp-overlay";
          overlay.addEventListener("click", () => fp.close());
          document.body.appendChild(overlay);
        }
      },

      onClose: () => {
        const overlay = document.querySelector(".fp-overlay");
        if (overlay) overlay.remove();
      },

      onDayCreate: (dObj, dStr, fp, dayElem) => {
        const key = ymd(dayElem.dateObj);

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const cellDate = new Date(dayElem.dateObj);
        cellDate.setHours(0, 0, 0, 0);

        if (cellDate < today) return;

        const dot = document.createElement("span");
        dot.className = unavailableSet.has(key) ? "red-dot" : "green-dot";
        dayElem.appendChild(dot);
      },

      onChange: () => {
        renderTotals();
      },
    });
  }

  function loadUnavailableDates() {
    if (!unavailableUrl) {
      unavailableSet = new Set();
      initDatePicker();
      return;
    }

    fetch(unavailableUrl)
      .then((res) => res.json())
      .then((data) => {
        unavailableSet = new Set(data || []);
      })
      .catch(() => {
        unavailableSet = new Set();
      })
      .finally(() => {
        initDatePicker();
      });
  }

  function initAgreeTerms() {
    const checkbox = document.getElementById("agree_terms");
    const btn = document.getElementById("confirmBtn");
    const err = document.getElementById("agreeError");
    if (!checkbox || !btn) return;

    const sync = () => {
      const ok = checkbox.checked;
      btn.disabled = isSubmitting || !ok;
      if (err) err.style.display = ok ? "none" : "block";
    };

    checkbox.addEventListener("change", sync);
    sync();
  }

  function initServiceTypeToggle() {
    const type = document.getElementById("service_type_id");
    const wrap = document.getElementById("locationWrap");
    const loc = document.getElementById("service_location");
    if (!type || !wrap || !loc) return;

    const isDelivery = () => {
      const selectedText = type.options[type.selectedIndex]?.text?.toLowerCase() || "";
      return selectedText.includes("deliver");
    };

    const sync = () => {
      const delivery = isDelivery();
      wrap.classList.toggle("hidden", !delivery);
      loc.required = delivery;
      if (!delivery) loc.value = "";
    };

    type.addEventListener("change", sync);
    sync();
  }

  function handleBookingSubmit(e) {
    e.preventDefault();

    if (isSubmitting) return;

    const form = document.getElementById("bookingForm");
    const submitBtn = document.getElementById("confirmBtn");
    const agree = document.getElementById("agree_terms");
    const err = document.getElementById("agreeError");

    if (agree && !agree.checked) {
      if (err) err.style.display = "block";
      alert("Please agree to the Rental Terms & Conditions to continue.");
      return;
    } else {
      if (err) err.style.display = "none";
    }

    const pickup = document.getElementById("pickup_date")?.value;
    const ret = document.getElementById("return_date")?.value;

    if (!pickup || !ret) {
      alert("Please select both pickup and return dates.");
      return;
    }

    let start = new Date(pickup + "T00:00:00");
    const end = new Date(ret + "T00:00:00");

    while (start <= end) {
      if (unavailableSet.has(ymd(start))) {
        alert("Selected dates include unavailable days. Please choose different dates.");
        return;
      }
      start.setDate(start.getDate() + 1);
    }

    isSubmitting = true;

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Processing...";
    }

    form.submit();
  }

  function initBookingFormSubmit() {
    const form = document.getElementById("bookingForm");
    if (!form) return;
    form.addEventListener("submit", handleBookingSubmit);
  }

  function initSidebarToggle() {
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    if (!toggleBtn || !sidebar) return;

    const open = () => {
      sidebar.classList.remove("-translate-x-full");
      sidebar.classList.add("translate-x-0");
      if (overlay) overlay.classList.remove("hidden");
    };

    const close = () => {
      sidebar.classList.add("-translate-x-full");
      sidebar.classList.remove("translate-x-0");
      if (overlay) overlay.classList.add("hidden");
    };

    const isOpen = () => !sidebar.classList.contains("-translate-x-full");

    toggleBtn.addEventListener("click", () => {
      isOpen() ? close() : open();
    });

    if (overlay) overlay.addEventListener("click", close);
  }

  document.addEventListener("DOMContentLoaded", () => {
    initSidebarToggle();
    initAgreeTerms();
    initServiceTypeToggle();
    initBookingFormSubmit();

    loadUnavailableDates();
    renderTotals();
  });
})();