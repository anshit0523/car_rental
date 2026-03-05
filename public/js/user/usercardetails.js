// public/js/user/usercardetails.js
(function () {
  const cfg = window.BOOKING_CFG || {};
  const dailyRate = Number(cfg.dailyRate || 0);
  const insuranceRate = Number(cfg.insuranceRate || 0); // you set 0
  const taxRate = Number(cfg.taxRate || 0);             // you set 0
  const discountRate = Number(cfg.discountRate || 0);   // keep if you want (0.10)
  const unavailableUrl = cfg.unavailableUrl || "";
  const availablePoints = Number(cfg.availablePoints || 0);

  let unavailableSet = new Set();

  // ===== Points state =====
  // ₱ value of points discount applied (e.g. 500 pts => ₱50 if 10pts=₱1)
  let pointsDiscountPeso = 0;
  const POINTS_PER_PESO = 10; // change to 1 if 1 point = ₱1

  // ===== Helpers =====
  function ymd(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  }

  function money(n) {
    return "₱" + Number(n).toFixed(2);
  }

  function clampPoints(val) {
    const n = Number(val);
    if (isNaN(n) || n < 0) return 0;
    if (n > availablePoints) return availablePoints;
    return Math.floor(n);
  }

  // ===== Price calc (base total from dates, then apply points) =====
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
    const baseTotal = subtotalBeforeDiscount - discount;

    // update summary UI
    const elDays = document.getElementById("rental-days");
    const elSubtotal = document.getElementById("subtotal");
    const elDiscount = document.getElementById("discount");

    if (elDays) elDays.textContent = days;
    if (elSubtotal) elSubtotal.textContent = money(subtotal);
    if (elDiscount) elDiscount.textContent = "-"+money(discount);

    return baseTotal;
  }

function renderTotals() {
  const baseTotal = calculateBaseTotal();
  if (baseTotal === null) return;

  const finalTotal = Math.max(0, baseTotal - pointsDiscountPeso);

  const elTotal = document.getElementById("total-price");
  const elTotalInput = document.getElementById("total-price-input");

  // ✅ UI shows discounted
  if (elTotal) elTotal.textContent = money(finalTotal);

  // ✅ hidden input sends BASE total (important!)
  if (elTotalInput) elTotalInput.value = baseTotal.toFixed(2);

  // ✅ points discount display
  const pointsDiscountDisplay = document.getElementById("points-discount-display");
  if (pointsDiscountDisplay) {
    pointsDiscountDisplay.textContent = "-" + money(pointsDiscountPeso);
  }

  // optional hidden discount amount
  const pointsDiscountHidden = document.getElementById("points_discount_amount");
  if (pointsDiscountHidden) {
    pointsDiscountHidden.value = pointsDiscountPeso.toFixed(2);
  }
}

  // ===== Flatpickr =====
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
        const dot = document.createElement("span");
        dot.className = unavailableSet.has(key) ? "red-dot" : "green-dot";
        dayElem.appendChild(dot);
      },

      onChange: () => {
        // recalc base + keep points discount applied
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

  // ===== Terms checkbox =====
  function initAgreeTerms() {
    const checkbox = document.getElementById("agree_terms");
    const btn = document.getElementById("confirmBtn");
    const err = document.getElementById("agreeError");
    if (!checkbox || !btn) return;

    const sync = () => {
      const ok = checkbox.checked;
      btn.disabled = !ok;
      if (err) err.classList.toggle("hidden", ok);
    };

    checkbox.addEventListener("change", sync);
    sync();
  }

  // ===== Service type toggle =====
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

  // ===== Booking submit validation (date range vs unavailable) =====
  function handleBookingSubmit(e) {
    e.preventDefault();

    const agree = document.getElementById("agree_terms");
    const err = document.getElementById("agreeError");

    if (agree && !agree.checked) {
      if (err) err.classList.remove("hidden");
      alert("Please agree to the Rental Terms & Conditions to continue.");
      return;
    } else {
      if (err) err.classList.add("hidden");
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

    document.getElementById("bookingForm")?.submit();
  }

  function initBookingFormSubmit() {
    const form = document.getElementById("bookingForm");
    if (!form) return;
    form.addEventListener("submit", handleBookingSubmit);
  }

  // ===== Points Redeem UI =====
  function initPointsRedeem() {
    const pointsInput = document.getElementById("points_to_use");
    const applyBtn = document.getElementById("applyPointsBtn");
    const err = document.getElementById("pointsError");

    const pointsValueText = document.getElementById("pointsValueText");
    const remainingPointsText = document.getElementById("remainingPointsText");

    if (!pointsInput || !applyBtn) return;

    function setError(message) {
      if (!err) return;
      err.textContent = message;
      err.classList.toggle("hidden", !message);
    }

    function applyPoints() {
      setError("");

      const pts = clampPoints(pointsInput.value);
      pointsInput.value = pts;

      const discountPeso = pts / POINTS_PER_PESO;
      const remaining = availablePoints - pts;

      // update points state
      pointsDiscountPeso = discountPeso;

      // update points text display
      if (pointsValueText) {
        pointsValueText.classList.toggle("hidden", pts === 0);
        pointsValueText.textContent = `${pts} points = ${money(discountPeso)} discount`;
      }

      if (remainingPointsText) {
        remainingPointsText.classList.toggle("hidden", pts === 0);
        remainingPointsText.textContent = `Remaining Points: ${remaining}`;
      }

      // re-render totals while keeping points applied
      renderTotals();
    }

    applyBtn.addEventListener("click", applyPoints);
    pointsInput.addEventListener("change", applyPoints);
  }

  // ===== Sidebar toggle (optional) =====
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

  // ===== Init =====
  document.addEventListener("DOMContentLoaded", () => {
    initSidebarToggle();
    initAgreeTerms();
    initServiceTypeToggle();
    initBookingFormSubmit();
    initPointsRedeem();

    loadUnavailableDates();
    renderTotals(); // initial render
  });
})();