(function ($) {
    "use strict";

    // Chart instances
    let dailySalesChart, reservationChart, productsChart, revenueChart;

    // Chart configurations
    const chartConfig = {
        responsive: true,
        maintainAspectRatio: false,
        resizeDelay: 0,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        if (this.chart.canvas.id === 'daily-sales-chart' || this.chart.canvas.id === 'revenue-chart') {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                        return value;
                    }
                }
            }
        },
        onResize: function(chart, size) {
            // Prevent height changes during resize
            chart.canvas.style.height = '400px';
        }
    };

    // Initialize chart containers - height now controlled by CSS
    function initChartContainers() {
        // Chart containers are now styled via CSS
        // This function can be used for additional initialization if needed
        console.log('Chart containers initialized');
    }

    // Initialize charts
    function initCharts() {
        // Initialize container heights first
        initChartContainers();

        // Daily Sales Chart
        const dailySalesCtx = document.getElementById('daily-sales-chart');
        if (dailySalesCtx) {
            dailySalesChart = new Chart(dailySalesCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: [],
                        borderColor: 'rgba(0, 156, 255, 1)',
                        backgroundColor: 'rgba(0, 156, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: chartConfig
            });
        }

        // Reservation Chart
        const reservationCtx = document.getElementById('reservation-chart');
        if (reservationCtx) {
            reservationChart = new Chart(reservationCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Jumlah Reservasi',
                        data: [],
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: chartConfig
            });
        }

        // Top Products Chart
        const productsCtx = document.getElementById('products-chart');
        if (productsCtx) {
            productsChart = new Chart(productsCtx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Produk Terjual',
                        data: [],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 205, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 205, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Monthly Revenue Chart
        const revenueCtx = document.getElementById('revenue-chart');
        if (revenueCtx) {
            revenueChart = new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: [],
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    }]
                },
                options: chartConfig
            });
        }

        
    }

    // Fetch chart data via AJAX
    function fetchChartData(chartType, callback) {
        $.ajax({
            url: 'chart_data.php',
            method: 'GET',
            data: { type: chartType },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    callback(response.data);
                } else {
                    console.error('Error fetching chart data:', response.error);
                    showNotification('Error loading chart data: ' + response.error, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                showNotification('Failed to load chart data. Please try again.', 'error');
            }
        });
    }

    // Update chart data
    function updateChart(chart, data) {
        if (chart && data) {
            chart.data.labels = data.labels;
            chart.data.datasets[0].data = data.data;
            chart.update('none'); // Update without animation for smoother realtime updates
        }
    }

    // Load all chart data
    function loadAllCharts() {
        // Load daily sales data
        fetchChartData('daily_sales', function(data) {
            updateChart(dailySalesChart, data);
        });

        // Load reservation data
        fetchChartData('weekly_reservation', function(data) {
            updateChart(reservationChart, data);
        });

        // Load top products data
        fetchChartData('top_products', function(data) {
            updateChart(productsChart, data);
        });

        // Load monthly revenue data
        fetchChartData('monthly_revenue', function(data) {
            updateChart(revenueChart, data);
        });
    }

    // Show notification (optional - you can implement this for better UX)
    function showNotification(message, type = 'info') {
        // Simple alert for now - you can replace with better notification system
        if (type === 'error') {
            console.error(message);
        } else {
            console.log(message);
        }
    }

    // Auto refresh data
    function startAutoRefresh() {
        // Refresh every 30 seconds
        setInterval(function() {
            loadAllCharts();
        }, 30000);
    }

    // Initialize when document is ready
    $(document).ready(function() {
        // Wait for spinner to hide before initializing charts
        setTimeout(function() {
            initCharts();
            loadAllCharts();
            startAutoRefresh();
        }, 100);
    });

})(jQuery);

// ============ PDF EXPORT FUNCTIONS ============

// Export Daily Report
function exportDailyReport() {
    const date = document.getElementById('daily_date').value;
    if (!date) {
        alert('Silakan pilih tanggal terlebih dahulu');
        return;
    }

    // Show loading
    showExportLoading('daily');

    // Create download URL
    const url = `export_pdf_simple.php?type=daily&date=${date}`;

    // Download file
    downloadFile(url, () => {
        hideExportLoading('daily');
    });
}

// Export Weekly Report
function exportWeeklyReport() {
    const weekStart = document.getElementById('weekly_date').value;
    if (!weekStart) {
        alert('Silakan pilih tanggal mulai minggu terlebih dahulu');
        return;
    }

    // Show loading
    showExportLoading('weekly');

    // Create download URL
    const url = `export_pdf_simple.php?type=weekly&week_start=${weekStart}`;

    // Download file
    downloadFile(url, () => {
        hideExportLoading('weekly');
    });
}

// Export Monthly Report
function exportMonthlyReport() {
    const month = document.getElementById('monthly_month').value;
    const year = document.getElementById('monthly_year').value;

    if (!month || !year) {
        alert('Silakan pilih bulan dan tahun terlebih dahulu');
        return;
    }

    // Show loading
    showExportLoading('monthly');

    // Create download URL
    const url = `export_pdf.php?type=monthly&month=${month}&year=${year}`;

    // Download file
    downloadFile(url, () => {
        hideExportLoading('monthly');
    });
}

// Helper function to download file
function downloadFile(url, callback) {
    // Create invisible link and trigger download
    const link = document.createElement('a');
    link.href = url;
    link.download = '';
    link.style.display = 'none';
    document.body.appendChild(link);

    // Handle download completion
    link.onclick = function() {
        setTimeout(callback, 1000); // Delay to ensure download starts
    };

    // Trigger download
    link.click();

    // Clean up
    setTimeout(() => {
        document.body.removeChild(link);
    }, 100);
}

// Show loading state for export buttons
function showExportLoading(type) {
    const button = getExportButton(type);
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Generating PDF...';
    }
}

// Hide loading state for export buttons
function hideExportLoading(type) {
    const button = getExportButton(type);
    if (button) {
        button.disabled = false;
        const originalTexts = {
            'daily': '<i class="fa fa-download me-2"></i>Download PDF',
            'weekly': '<i class="fa fa-download me-2"></i>Download PDF',
            'monthly': '<i class="fa fa-download me-2"></i>Download PDF'
        };
        button.innerHTML = originalTexts[type];
    }
}

// Get export button by type
function getExportButton(type) {
    const buttons = {
        'daily': document.querySelector('[onclick="exportDailyReport()"]'),
        'weekly': document.querySelector('[onclick="exportWeeklyReport()"]'),
        'monthly': document.querySelector('[onclick="exportMonthlyReport()"]')
    };
    return buttons[type];
}

// Helper function to show notifications
function showNotificationPDF(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}
