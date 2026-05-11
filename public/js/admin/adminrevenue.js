window.addEventListener("load", function () {
    const chartElement = document.getElementById("revenueChart");
    if (!chartElement) return;

    // Controller sends: Dec 2025 → Jan 2026 → ... → May 2026
    const labels = JSON.parse(chartElement.getAttribute("data-labels") || "[]");
    const values = JSON.parse(chartElement.getAttribute("data-values") || "[]");

    const maxValue = Math.max(...values, 0);

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

    function niceMax(val) {
        if (val === 0) return 10;

        const magnitude = Math.pow(10, Math.floor(Math.log10(val)));
        return Math.ceil(val / magnitude) * magnitude;
    }

    const suggestedMax = niceMax(maxValue * 1.2);
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
            maintainAspectRatio: false,
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
                    position: "right",
                    beginAtZero: true,
                    max: suggestedMax,
                    ticks: {
                        stepSize: stepSize,
                        callback: function (value) {
                            if (value === 0) return "₱0";

                            const display = value / divisor;
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