document.addEventListener("DOMContentLoaded", () => {
    // ✅ Charts Section
    if (window.dashboardData) {
        const { months, bookingsData, revenueData, statusLabels, statusData } =
            window.dashboardData;

        // 📊 Trend Chart (Bookings & Revenue)
        const trendCtx = document.getElementById("trendChart");
        if (trendCtx) {
            new Chart(trendCtx, {
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

        // 🍩 Status Chart (Doughnut)
        const statusCtx = document.getElementById("statusChart");
        if (statusCtx) {
            new Chart(statusCtx, {
                type: "doughnut",
                data: {
                    labels: statusLabels,
                    datasets: [
                        {
                            data: statusData,
                            backgroundColor: [
                                "#3B82F6", // active
                                "#EF4444", // canceled
                                "#10B981", // completed
                                "#22C55E", // confirmed
                                "#FACC15", // pending
                                "#6366F1", // other
                            ],
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                // Append the count number after each label
                                generateLabels: function (chart) {
                                    const data = chart.data;
                                    return data.labels.map((label, i) => {
                                        const count = data.datasets[0].data[i];
                                        const bg =
                                            data.datasets[0].backgroundColor[i];
                                        return {
                                            text: `${label}  (${count})`,
                                            fillStyle: bg,
                                            strokeStyle: bg,
                                            lineWidth: 0,
                                            hidden: false,
                                            index: i,
                                        };
                                    });
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
    }
});
