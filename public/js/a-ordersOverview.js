// All orders data for searching
let allOrders = [];

document.addEventListener("DOMContentLoaded", async () => {
    // Fetch all orders data
    await loadAllOrders();
});

async function loadAllOrders() {
    try {
        const res = await fetch("../endpoint/a-adminOrders.php", {
            method: "GET",
            credentials: "include",
            headers: { "Content-Type": "application/json" }
        });
        if (!res.ok) throw new Error("Failed to fetch orders: " + res.status);
        
        allOrders = await res.json();
        renderOrdersTable(allOrders);
        updateStats(allOrders);
    } catch (error) {
        console.error("Error loading orders:", error);
        document.getElementById("ordersTableBody").innerHTML = 
            `<tr><td colspan="7" style="color: red;">Error loading orders</td></tr>`;
    }
}

function renderOrdersTable(orders) {
    const tbody = document.getElementById("ordersTableBody");
    tbody.innerHTML = "";

    if (orders.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: rgba(255,255,255,0.5);">No orders found</td></tr>`;
        return;
    }

    orders.forEach(order => {
        try {
            const row = document.createElement("tr");
            const date = new Date(order.created_at).toLocaleDateString("en-PH");
            const assignedName = order.firstName ? 
                (order.firstName + " " + (order.lastName || "")).trim() : 
                "Unassigned";
            const assignedStyle = !order.firstName ? "style='color: rgba(255,255,255,0.5);'" : "";

            row.innerHTML = `
                <td><strong>#${order.ext_id}</strong></td>
                <td>${order.platform.toUpperCase()}</td>
                <td>${order.buyer_username || "N/A"}</td>
                <td>
                    <span class="badge badge-${order.status}">
                        ${order.status.toUpperCase()}
                    </span>
                </td>
                <td ${assignedStyle}>${assignedName}</td>
                <td>₱${parseFloat(order.total_worth || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                <td>${date}</td>
            `;
            tbody.appendChild(row);
        } catch (e) {
            console.error("Error rendering order row:", e);
        }
    });
}

function updateStats(orders) {
    const totalOrders = orders.length;
    const assignedOrders = orders.filter(o => o.user_id !== null).length;
    const unassignedOrders = totalOrders - assignedOrders;
    const totalWorth = orders.reduce((sum, o) => sum + (parseFloat(o.total_worth) || 0), 0);

    document.getElementById("totalOrders").textContent = totalOrders.toLocaleString('en-PH');
    document.getElementById("assignedOrders").textContent = assignedOrders.toLocaleString('en-PH');
    document.getElementById("unassignedOrders").textContent = unassignedOrders.toLocaleString('en-PH');
    document.getElementById("totalWorth").textContent = "₱" + totalWorth.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function filterOrders() {
    const searchTerm = document.getElementById("searchInput").value.toLowerCase().trim();
    
    if (searchTerm === "") {
        renderOrdersTable(allOrders);
        updateStats(allOrders);
        return;
    }

    const filteredOrders = allOrders.filter(order => {
        const orderID = order.ext_id.toLowerCase();
        const platform = order.platform.toLowerCase();
        const buyer = (order.buyer_username || "").toLowerCase();
        const assigned = ((order.firstName || "") + " " + (order.lastName || "")).toLowerCase();
        
        return orderID.includes(searchTerm) || 
               platform.includes(searchTerm) || 
               buyer.includes(searchTerm) ||
               assigned.includes(searchTerm);
    });

    renderOrdersTable(filteredOrders);
    updateStats(filteredOrders);
}

function resetSearch() {
    document.getElementById("searchInput").value = "";
    renderOrdersTable(allOrders);
    updateStats(allOrders);
}

// Allow search on Enter key
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("keyup", (e) => {
            if (e.key === "Enter") {
                filterOrders();
            }
        });
    }
});
