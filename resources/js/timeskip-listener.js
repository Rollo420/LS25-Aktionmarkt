import Echo from 'laravel-echo';

document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Echo !== 'undefined') {
        window.Echo.channel('timeskip')
            .listen('.timeskip.completed', (e) => {
                console.log('Timeskip completed:', e.message);

                // Instead of full reload, refresh specific data via AJAX for smoother UX
                refreshDashboardData();
                refreshChartData();
                refreshTransactionList();

                // Fallback: full reload after 10 seconds if AJAX fails
                setTimeout(() => {
                    console.log('Fallback: reloading page after timeskip');
                    window.location.reload();
                }, 10000);
            });
    } else {
        console.warn('Echo not available, timeskip refresh disabled');
    }
});

// Function to refresh dashboard data (balances, holdings, etc.)
function refreshDashboardData() {
    // Assuming dashboard has elements with IDs like #user-balance, #holdings-table
    fetch('/dashboard/data', { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(data => {
            // Update balance
            const balanceEl = document.getElementById('user-balance');
            if (balanceEl) balanceEl.textContent = data.balance;

            // Update holdings
            const holdingsEl = document.getElementById('holdings-table');
            if (holdingsEl) holdingsEl.innerHTML = data.holdingsHtml;

            console.log('Dashboard data refreshed');
        })
        .catch(err => console.error('Failed to refresh dashboard:', err));
}

// Function to refresh chart data
function refreshChartData() {
    // Assuming chart component has a refresh method or we can trigger a re-render
    if (window.chartInstance) {
        window.chartInstance.update(); // Assuming chart library has update method
    } else {
        // Fallback: reload chart component
        const chartEl = document.querySelector('[data-chart]');
        if (chartEl) {
            fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newChart = doc.querySelector('[data-chart]');
                    if (newChart) chartEl.innerHTML = newChart.innerHTML;
                });
        }
    }
    console.log('Chart data refreshed');
}

// Function to refresh transaction list
function refreshTransactionList() {
    const transactionEl = document.getElementById('transaction-list');
    if (transactionEl) {
        fetch('/transactions/recent', { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.json())
            .then(data => {
                transactionEl.innerHTML = data.html;
                console.log('Transaction list refreshed');
            })
            .catch(err => console.error('Failed to refresh transactions:', err));
    }
}
