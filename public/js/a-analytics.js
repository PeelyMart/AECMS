// Chart instance
let chartInstance = null;

document.addEventListener("DOMContentLoaded", async () => {
    // Fetch only analytics data
    await loadAnalytics();
});

async function loadAnalytics() {
    try {
        const res = await fetch("../endpoint/a-analytics.php", {
            method: "GET",
            credentials: "include",
            headers: { "Content-Type": "application/json" }
        });
        if (!res.ok) throw new Error("Failed to fetch analytics: " + res.status);
        
        const data = await res.json();

        // Update Top Packer
        if (data.topPacker && data.topPacker.firstName) {
            document.getElementById("topPackerName").textContent = 
                data.topPacker.firstName + " " + data.topPacker.lastName;
            document.getElementById("topPackerCount").textContent = 
                data.topPacker.packed_count.toLocaleString('en-PH') + " orders packed";
        }

        // Update Top Product
        if (data.topProduct && data.topProduct.name) {
            document.getElementById("topProductName").textContent = data.topProduct.name;
            document.getElementById("topProductQty").textContent = 
                data.topProduct.total_qty.toLocaleString('en-PH') + " units sold";
        }

        // Update Month Total Worth
        if (data.monthTotal) {
            document.getElementById("monthTotalWorth").textContent = 
                "₱" + parseFloat(data.monthTotal.total_worth || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById("monthTotalOrders").textContent = 
                data.monthTotal.order_count.toLocaleString('en-PH') + " orders";
        }

        // Update 6 Months Chart and Table
        if (data.sixMonthData && data.sixMonthData.length > 0) {
            renderSixMonthChart(data.sixMonthData);
            renderSixMonthTable(data.sixMonthData);
        }
    } catch (error) {
        console.error("Error loading analytics:", error);
    }
}

function renderSixMonthChart(data) {
    const ctx = document.getElementById("sixMonthChart");
    if (!ctx) return;

    const labels = data.map(d => d.month);
    const values = data.map(d => parseFloat(d.total_worth || 0));

    // Destroy existing chart if it exists
    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "Total Worth (₱)",
                data: values,
                borderColor: "#4CAF50",
                backgroundColor: "rgba(76, 175, 80, 0.1)",
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: "#4CAF50",
                pointBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: "rgba(255,255,255,0.8)",
                        font: { size: 12 }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: "rgba(255,255,255,0.7)",
                        callback: function(value) {
                            return "₱" + value.toLocaleString("en-PH");
                        }
                    },
                    grid: {
                        color: "rgba(255,255,255,0.1)"
                    }
                },
                x: {
                    ticks: {
                        color: "rgba(255,255,255,0.7)"
                    },
                    grid: {
                        color: "rgba(255,255,255,0.1)"
                    }
                }
            }
        }
    });
}

function renderSixMonthTable(data) {
    const tbody = document.getElementById("sixMonthTableBody");
    tbody.innerHTML = "";

    data.forEach(month => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td><strong>${month.month}</strong></td>
            <td>₱${parseFloat(month.total_worth || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            <td>${month.order_count.toLocaleString('en-PH')} orders</td>
        `;
        tbody.appendChild(row);
    });
}
