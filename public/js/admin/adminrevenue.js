window.addEventListener("load", function () {
    const chartElement = document.getElementById("revenueChart");
    if (!chartElement) return;

    // Reverse so newest month appears on the right
    const labels = JSON.parse(
        chartElement.getAttribute("data-labels"),
    ).reverse();
    const values = JSON.parse(
        chartElement.getAttribute("data-values"),
    ).reverse();

    // ── Dynamic scale calculation ──────────────────────────────────────────
    const maxValue = Math.max(...values);
    const minValue = Math.min(...values.filter((v) => v > 0)); // ignore zeros

    // Determine the best unit to display based on the max value
    let divisor, unit;
    if (maxValue >= 1_000_000) {
        divisor = 1_000_000;
        unit = "M";
    } else if (maxValue >= 1_000) {
        divisor = 1_000;
        unit = "K";
    } else {
        divisor = 1;
        unit = "";
    }

    // Round up max to a clean ceiling (e.g. 87,400 → 100,000)
    function niceMax(val) {
        if (val === 0) return 10;
        const magnitude = Math.pow(10, Math.floor(Math.log10(val)));
        return Math.ceil(val / magnitude) * magnitude;
    }

    // Set a 20% headroom above the highest bar
    const suggestedMax = niceMax(maxValue * 1.2);

    // Smart step size — aim for ~5 ticks
    const rawStep = suggestedMax / 5;
    const stepMag = Math.pow(10, Math.floor(Math.log10(rawStep)));
    const stepSize = Math.ceil(rawStep / stepMag) * stepMag;

    const revenueCtx = chartElement.getContext("2d");

    new Chart(revenueCtx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Monthly Revenue",
                    data: values,
                    backgroundColor: "#667eea",
                    borderRadius: 8,
                    hoverBackgroundColor: "#764ba2",
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const val = context.parsed.y;
                            return (
                                " ₱" +
                                val.toLocaleString("en-PH", {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2,
                                })
                            );
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: suggestedMax,
                    ticks: {
                        stepSize: stepSize,
                        // Auto label: shows ₱1.2M / ₱850K / ₱500 depending on data range
                        callback: function (value) {
                            if (value === 0) return "₱0";
                            const display = value / divisor;
                            // Show decimals only if needed (e.g. ₱1.5M not ₱1.0M)
                            const formatted =
                                display % 1 === 0
                                    ? display.toLocaleString()
                                    : display.toFixed(1);
                            return "₱" + formatted + unit;
                        },
                    },
                    grid: {
                        color: "rgba(0, 0, 0, 0.05)",
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                },
            },
        },
    });
});
