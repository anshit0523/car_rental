window.addEventListener('load', function() {
    const chartElement = document.getElementById('revenueChart');
    
    if (!chartElement) return;
    
    // Get data from data attributes
    const labels = JSON.parse(chartElement.getAttribute('data-labels'));
    const values = JSON.parse(chartElement.getAttribute('data-values'));
    
    const revenueCtx = chartElement.getContext('2d');
    
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Monthly Revenue',
                data: values,
                backgroundColor: '#667eea',
                borderRadius: 8,
                hoverBackgroundColor: '#764ba2'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        callback: function(value) { 
                            return '₱' + value.toLocaleString(); 
                        } 
                    }
                }
            }
        }
    });
});