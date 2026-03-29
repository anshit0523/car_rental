document.addEventListener("DOMContentLoaded", () => {
    if (!window.dashboardData) {
        console.error("dashboardData is missing");
        return;
    }

    const { months, bookingsData, revenueData, statusLabels, statusData } = window.dashboardData;

    console.log("months:", months);
    console.log("statusLabels:", statusLabels);
    console.log("statusData:", statusData);

    // LINE CHART
    const trendCanvas = document.getElementById("trendChart");
    if (trendCanvas) {
        const existingChart = Chart.getChart(trendCanvas);
        if (existingChart) existingChart.destroy();

        new Chart(trendCanvas, {
            type: "line",
            data: {
                labels: months,
                datasets: [
                    {
                        label: "Bookings",
                        data: bookingsData,
                        borderColor: "#3b82f6",
                        backgroundColor: "rgba(59,130,246,0.1)",
                        borderWidth: 2,
                        tension: 0.4,
                        yAxisID: "y",
                    },
                    {
                        label: "Revenue (₱)",
                        data: revenueData,
                        borderColor: "#10b981",
                        backgroundColor: "rgba(16,185,129,0.1)",
                        borderWidth: 2,
                        tension: 0.4,
                        yAxisID: "y1",
                    },
                ],
            },
            options: {
                responsive: true,
                interaction: { mode: "index", intersect: false },
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                        },
                    },
                    y: {
                        type: "linear",
                        display: true,
                        position: "left",
                        title: { display: true, text: "Bookings" },
                    },
                    y1: {
                        type: "linear",
                        display: true,
                        position: "right",
                        title: { display: true, text: "Revenue" },
                        grid: { drawOnChartArea: false },
                    },
                },
            },
        });
    }

    // DOUGHNUT CHART
    const statusCanvas = document.getElementById("statusChart");
    if (
        statusCanvas &&
        Array.isArray(statusLabels) &&
        Array.isArray(statusData) &&
        statusLabels.length > 0 &&
        statusData.length > 0
    ) {
        const statusCanvas = document.getElementById("statusChart");

if (
    statusCanvas &&
    Array.isArray(statusLabels) &&
    Array.isArray(statusData) &&
    statusLabels.length > 0 &&
    statusData.length > 0
) {
    const existingStatusChart = Chart.getChart(statusCanvas);
    if (existingStatusChart) existingStatusChart.destroy();

    new Chart(statusCanvas, {
        type: "doughnut",
        data: {
            labels: statusLabels,
            datasets: [
                {
                    data: statusData,
                    backgroundColor: [
                        "#3B82F6",
                        "#EF4444",
                        "#10B981",
                        "#22C55E",
                        "#FACC15",
                        "#6366F1",
                    ],
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        generateLabels(chart) {
                            const data = chart.data;
                            const dataset = data.datasets[0];

                            return data.labels.map((label, i) => ({
                                text: `${label} (${dataset.data[i] ?? 0})`,
                                fillStyle: Array.isArray(dataset.backgroundColor)
                                    ? dataset.backgroundColor[i]
                                    : dataset.backgroundColor,
                                strokeStyle: Array.isArray(dataset.backgroundColor)
                                    ? dataset.backgroundColor[i]
                                    : dataset.backgroundColor,
                                lineWidth: 0,
                                hidden: false,
                                index: i,
                            }));
                        },
                        padding: 16,
                        boxWidth: 12,
                        boxHeight: 12,
                        font: { size: 12 },
                    },
                },
            },
        },
    });
}
}});