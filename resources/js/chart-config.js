$(document).ready(function () {

    // =========================
    // 1. Sales vs Purchases (7 hari)
    // =========================
    let salesPurchasesBar = document.getElementById('salesPurchasesChart');

    if (salesPurchasesBar) {
        $.get('/sales-purchases/chart-data', function (response) {
            // Dari controller: { sales: [...], purchases: [...], days: [...] }
            if (!response || !Array.isArray(response.days)) {
                console.error('Response sales-purchases tidak sesuai:', response);
                return;
            }

            let ctx = salesPurchasesBar.getContext('2d');

            let salesPurchasesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: response.days,          // ✅ BUKAN response.sales.original.days
                    datasets: [
                        {
                            label: 'Sales',
                            data: response.sales || [],   // ✅ BUKAN response.sales.original.data
                            backgroundColor: '#6366F1',
                            borderColor: '#6366F1',
                            borderWidth: 1
                        },
                        {
                            label: 'Purchases',
                            data: response.purchases || [], // ✅ BUKAN response.purchases.original.data
                            backgroundColor: '#A5B4FC',
                            borderColor: '#A5B4FC',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }).fail(function (xhr) {
            console.error('Gagal load /sales-purchases/chart-data:', xhr.responseText);
        });
    }

    // =========================
    // 2. Doughnut: Ringkasan Bulan Berjalan
    // =========================
    let overviewChart = document.getElementById('currentMonthChart');

    if (overviewChart) {
        $.get('/current-month/chart-data', function (response) {
            // Dari controller: { sales: xxx, purchases: xxx, expenses: xxx }
            if (!response) {
                console.error('Response current-month kosong');
                return;
            }

            let ctx = overviewChart.getContext('2d');

            let currentMonthChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sales', 'Purchases', 'Expenses'],
                    datasets: [{
                        data: [
                            Number(response.sales || 0),
                            Number(response.purchases || 0),
                            Number(response.expenses || 0)
                        ],
                        backgroundColor: [
                            '#F59E0B',
                            '#0284C7',
                            '#EF4444',
                        ],
                        hoverBackgroundColor: [
                            '#F59E0B',
                            '#0284C7',
                            '#EF4444',
                        ],
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
        }).fail(function (xhr) {
            console.error('Gagal load /current-month/chart-data:', xhr.responseText);
        });
    }

    // =========================
    // 3. Line: Cashflow (paymentChart)
    // =========================
    let paymentChart = document.getElementById('paymentChart');

    if (paymentChart) {
        $.get('/payment-flow/chart-data', function (response) {
            // Dari controller: { payment_sent: [...], payment_received: [...], months: [...] }
            if (!response || !Array.isArray(response.months)) {
                console.error('Response payment-flow tidak sesuai:', response);
                return;
            }

            let ctx = paymentChart.getContext('2d');

            let cashflowChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: response.months,
                    datasets: [
                        {
                            label: 'Payment Sent',
                            data: response.payment_sent || [],
                            fill: false,
                            borderColor: '#EA580C',
                            tension: 0
                        },
                        {
                            label: 'Payment Received',
                            data: response.payment_received || [],
                            fill: false,
                            borderColor: '#2563EB',
                            tension: 0
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }).fail(function (xhr) {
            console.error('Gagal load /payment-flow/chart-data:', xhr.responseText);
        });
    }

});
