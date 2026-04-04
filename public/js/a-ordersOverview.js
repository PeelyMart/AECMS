let allOrders = [];
let filteredOrders = [];
let staffList = [];
let selectedStatuses = [];
let fromDate = "";
let toDate = "";
let currentPage = 1;
const ordersPerPage = 10;
let selectedOrderId = null;

document.addEventListener("DOMContentLoaded", async () => {
    // Fetch orders, stats, and staff list
    await loadAllOrders();
    await loadStaffList();
    renderPagination();
    
    document.getElementById("fromDate").addEventListener("change", () => {
        fromDate = document.getElementById("fromDate").value;
        filterOrders();
    });
    document.getElementById("toDate").addEventListener("change", () => {
        toDate = document.getElementById("toDate").value;
        filterOrders();
    });
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
        filteredOrders = [...allOrders];
        currentPage = 1;
        renderStatusFilters();
        renderOrdersTable();
        updateStats(allOrders);
    } catch (error) {
        console.error("Error loading orders:", error);
        document.getElementById("ordersTableBody").innerHTML = 
            `<tr><td colspan="7" style="color: red;">Error loading orders</td></tr>`;
    }
}

function renderStatusFilters() {

    const uniqueStatuses = [...new Set(allOrders.map(o => o.status))].sort();
    const container = document.getElementById("statusFiltersContainer");
    container.innerHTML = "";
    
    const statusColorMap = {
        "PACKED": "#4CAF50",
        "PENDING": "#FFC107",
        "SHIPPED": "#2196F3"
    };
    
    uniqueStatuses.forEach(status => {
        const radioDiv = document.createElement("div");
        radioDiv.className = "statusRadio";
        
        const radio = document.createElement("input");
        radio.type = "radio";
        radio.name = "statusFilter";
        radio.id = `status-${status}`;
        radio.value = status;
        radio.setAttribute("data-status", status);
        
        const color = statusColorMap[status] || "#4CAF50";
        radio.style.accentColor = color;
        
        radio.addEventListener("change", () => {
            updateSelectedStatuses();
            filterOrders();
        });
        
        const label = document.createElement("label");
        label.htmlFor = `status-${status}`;
        label.textContent = status.toUpperCase();
        
        radioDiv.appendChild(radio);
        radioDiv.appendChild(label);
        container.appendChild(radioDiv);
    });
}

function updateSelectedStatuses() {
    const checked = document.querySelector("#statusFiltersContainer input[type='radio']:checked");
    selectedStatuses = checked ? [checked.value] : [];
}

async function loadStaffList() {
    try {
        const res = await fetch("../endpoint/a-getStaffList.php", {
            method: "GET",
            credentials: "include",
            headers: { "Content-Type": "application/json" }
        });
        if (!res.ok) throw new Error("Failed to fetch staff list");
        
        staffList = await res.json();
        populateStaffSelect();
    } catch (error) {
        console.error("Error loading staff list:", error);
    }
}

function populateStaffSelect() {
    const select = document.getElementById("staffSelect");

    while (select.options.length > 1) {
        select.remove(1);
    }
    
    staffList.forEach(staff => {
        const option = document.createElement("option");
        option.value = staff.id;
        option.textContent = staff.firstName + " " + staff.lastName;
        select.appendChild(option);
    });
}

function renderOrdersTable() {
    const tbody = document.getElementById("ordersTableBody");
    tbody.innerHTML = "";

    const startIdx = (currentPage - 1) * ordersPerPage;
    const endIdx = startIdx + ordersPerPage;
    const pageOrders = filteredOrders.slice(startIdx, endIdx);

    if (pageOrders.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: rgba(255,255,255,0.5);">No orders found</td></tr>`;
        updatePaginationButtons();
        return;
    }

    pageOrders.forEach(order => {
        try {
            const row = document.createElement("tr");
            row.setAttribute("data-order-id", order.id);
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
                <td class="assignedCell" onclick="openModal(${order.id}, '${assignedName}', ${order.user_id || 'null'})" ${assignedStyle}>${assignedName}</td>
                <td>₱${parseFloat(order.total_worth || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                <td>${date}</td>
            `;
            tbody.appendChild(row);
        } catch (e) {
            console.error("Error rendering order row:", e);
        }
    });

    updatePaginationButtons();
}

function updatePaginationButtons() {
    const totalPages = Math.ceil(filteredOrders.length / ordersPerPage);
    
    document.getElementById("prevBtn").disabled = currentPage === 1;
    document.getElementById("nextBtn").disabled = currentPage >= totalPages;
    document.getElementById("pageInfo").textContent = `Page ${currentPage} of ${totalPages > 0 ? totalPages : 1}`;
}

function renderPagination() {
    updatePaginationButtons();
}

function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        renderOrdersTable();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function nextPage() {
    const totalPages = Math.ceil(filteredOrders.length / ordersPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        renderOrdersTable();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
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
    
    let tempFiltered = allOrders;
    
    if (selectedStatuses.length > 0) {
        tempFiltered = tempFiltered.filter(order => selectedStatuses.includes(order.status));
    }
    
    if (fromDate || toDate) {
        tempFiltered = tempFiltered.filter(order => {
            const orderDate = new Date(order.created_at).toISOString().split('T')[0];
            
            if (fromDate && toDate) {
                return orderDate >= fromDate && orderDate <= toDate;
            } else if (fromDate) {
                return orderDate >= fromDate;
            } else if (toDate) {
                return orderDate <= toDate;
            }
            return true;
        });
    }
    
    if (searchTerm !== "") {
        tempFiltered = tempFiltered.filter(order => {
            const orderID = order.ext_id.toLowerCase();
            const platform = order.platform.toLowerCase();
            const buyer = (order.buyer_username || "").toLowerCase();
            const assigned = ((order.firstName || "") + " " + (order.lastName || "")).toLowerCase();
            
            return orderID.includes(searchTerm) || 
                   platform.includes(searchTerm) || 
                   buyer.includes(searchTerm) ||
                   assigned.includes(searchTerm);
        });
    }

    filteredOrders = tempFiltered;
    currentPage = 1;
    renderOrdersTable();
    updateStats(filteredOrders);
}

function resetSearch() {
    document.getElementById("searchInput").value = "";
    document.getElementById("fromDate").value = "";
    document.getElementById("toDate").value = "";

    document.querySelectorAll("#statusFiltersContainer input[type='radio']").forEach(rb => {
        rb.checked = false;
    });
    selectedStatuses = [];
    fromDate = "";
    toDate = "";
    filteredOrders = [...allOrders];
    currentPage = 1;
    renderOrdersTable();
    updateStats(allOrders);
}

function openModal(orderId, currentAssigned, currentUserId) {
    selectedOrderId = orderId;
    document.getElementById("modalOrderId").textContent = orderId;
    document.getElementById("staffSelect").value = currentUserId || "";
    document.getElementById("assignmentModal").classList.add("active");
}

function closeModal() {
    document.getElementById("assignmentModal").classList.remove("active");
    selectedOrderId = null;
}

async function saveAssignment() {
    if (!selectedOrderId) return;

    const assignedToValue = document.getElementById("staffSelect").value;
    const newAssignedTo = assignedToValue === "" ? null : parseInt(assignedToValue);

    try {
        const response = await fetch("../endpoint/a-updateOrderAssignment.php", {
            method: "POST",
            credentials: "include",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                orderId: selectedOrderId,
                assignedTo: newAssignedTo
            })
        });

        if (!response.ok) throw new Error("Failed to update assignment");
        
        const result = await response.json();
        
        if (result.status === "success") {
            closeModal();
            
            const staffName = newAssignedTo ? 
                (result.order.firstName + " " + (result.order.lastName || "")).trim() : 
                "Unassigned";
            
            const orderRes = await fetch("../endpoint/a-adminOrders.php", {
                method: "GET",
                credentials: "include",
                headers: { "Content-Type": "application/json" }
            });
            
            if (orderRes.ok) {
                allOrders = await orderRes.json();
                filterOrders();
                alert(`✓ Order #${result.order.ext_id} assigned to ${staffName}!`);
            } else {
                throw new Error("Failed to reload orders");
            }
        } else {
            alert("Error: " + result.errorMsg);
        }
    } catch (error) {
        console.error("Error updating assignment:", error);
        alert("Failed to update assignment. Check console for details.");
    }
}

document.addEventListener("click", (e) => {
    const modal = document.getElementById("assignmentModal");
    if (e.target === modal) {
        closeModal();
    }
});

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
