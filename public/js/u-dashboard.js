console.log("Hello DASHBOARD"); 

// Configuration for the different order sections shown in the dashboard
const orderSections = [
    {
        endpoint: "../endpoint/getToPack.php", // API endpoint that returns orders assigned to the user that still need packing
        selector: ".currentOrders .moduleContainer", // HTML container where these orders will be displayed
        emptyMessage: "No unpacked orders assigned to you yet.", // Message shown when no orders are returned
        action: {
            label: "Pack Order", // Button label
            value: "pack" // Action value sent to the backend
        }
    },
    {
        endpoint: "../endpoint/getPacked.php", // API endpoint that returns already packed orders
        selector: ".processedOrders .moduleContainer", // HTML container for packed orders
        emptyMessage: "No packed orders assigned to you yet.", // Message if there are no packed orders
        action: {
            label: "Mark Unpacked", // Button label
            value: "unpack", // Action to revert order status back to pending
            variant: "secondary" // Style variant for the button
        }
    },
    {
        endpoint: "../endpoint/getUnclaimed.php", // API endpoint for orders that are not assigned to any user
        selector: ".unclaimedOrders .moduleContainer", // HTML container where unclaimed orders are shown
        emptyMessage: "No unclaimed orders right now.", // Message when there are no unclaimed orders
        action: {
            label: "Claim Order", // Button label
            value: "claim_pack" // Action that claims the order for the user
        }
    }
];

// Runs when the page has finished loading
document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Request logged-in user information from the server
        const userRes = await fetch("../endpoint/userData.php", {
            method: "POST",
            credentials: "include" // Ensures cookies/session are sent with the request
        });

        const userData = await userRes.json(); // Convert the response to JSON

        // If authentication fails, redirect to login page
        if (userData.status !== "success") {
            window.location.href = "../index.html";
            return;
        }

        // Build the user's full name from the response
        const fullName = `${userData.firstName} ${userData.lastName}`;

        // Display the greeting message on the dashboard
        document.getElementById("greet").innerHTML = "Hello, " + fullName;

        // Load orders for all sections
        await refreshDashboardOrders();
    } catch (err) {
        console.error("Error loading dashboard:", err); // Log any errors
    }
});

// Event listener for clicks on action buttons
document.addEventListener("click", async (event) => {

    // Check if the clicked element or its parent is an order action button
    const actionButton = event.target.closest(".js-order-action");

    if (!actionButton) {
        return; // If not an action button, ignore the click
    }

    event.preventDefault(); // Prevent default link/button behavior
    event.stopPropagation(); // Prevent click from triggering parent elements

    // Get order ID and action type from data attributes
    const orderId = actionButton.dataset.orderId;
    const action = actionButton.dataset.action;

    const originalLabel = actionButton.textContent; // Save original button text

    // Disable the button while processing
    actionButton.disabled = true;
    actionButton.textContent = "Updating...";

    try {
        // Send request to update order status
        const response = await fetch("../endpoint/userOrderAction.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            credentials: "include", // Include session cookies
            body: `order_id=${encodeURIComponent(orderId)}&action=${encodeURIComponent(action)}`
            // Send order ID and action safely encoded
        });

        const result = await response.json(); // Parse JSON response

        // If the request failed, throw an error
        if (!response.ok || result.status !== "success") {
            throw new Error(result.errorMsg || "Unable to update the order.");
        }

        // Refresh dashboard order lists after update
        await refreshDashboardOrders();
    } catch (error) {
        console.error("Failed to update order:", error);

        alert(error.message || "Unable to update the order right now."); // Show error message

        // Restore button if update fails
        actionButton.disabled = false;
        actionButton.textContent = originalLabel;
    }
});

// Function that refreshes all order sections
async function refreshDashboardOrders() {
    await Promise.all(orderSections.map(loadOrders)); 
    // Load all order sections in parallel using their configuration
}

// Function that loads orders for a specific section
async function loadOrders(config) {

    const container = document.querySelector(config.selector); 
    // Find the container where orders will be displayed

    if (!container) {
        return; // Exit if container is not found
    }

    // Show loading message while fetching orders
    container.innerHTML = `<p style="color: rgba(255,255,255,0.65);">Loading orders...</p>`;

    try {
        // Fetch orders from the specified API endpoint
        const response = await fetch(config.endpoint, {
            credentials: "include" // Send session cookies
        });

        const orders = await response.json(); // Parse JSON response

        // If no orders are returned
        if (!Array.isArray(orders) || orders.length === 0) {
            container.innerHTML = `<p style="color: rgba(255,255,255,0.65);">${config.emptyMessage}</p>`;
            return;
        }

        // Render each order as a card
        container.innerHTML = orders.map(order => renderOrderCard(order, config.action)).join("");

    } catch (error) {
        console.error(`Failed to load orders from ${config.endpoint}:`, error);

        // Display error message if request fails
        container.innerHTML = `<p style="color: #ffb3b3;">Unable to load orders right now.</p>`;
    }
}

// Function that generates the HTML for a single order card
function renderOrderCard(order, actionConfig) {

    const displayId = order.ext_id || order.id; // Use external order ID if available

    const importedLabel = order.created_at ? `Imported on ${formatOrderDate(order.created_at)}` : "Imported order";
    // Show import date if available

    const platform = (order.platform || "unknown").toUpperCase(); 
    // Convert platform name to uppercase

    const totalWorth = Number(order.total_worth || 0).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    // Format total order value with two decimal places

    const actionClass = actionConfig?.variant === "secondary"
        ? "actionButton secondaryAction"
        : "actionButton";
    // Choose button style depending on variant

    // Generate action button HTML if an action is defined
    const actionHtml = actionConfig ? `
        <div class="orderActions">
            <button
                type="button"
                class="${actionClass} js-order-action"
                data-order-id="${order.id}"
                data-action="${actionConfig.value}">
                ${actionConfig.label}
            </button>
        </div>
    ` : "";

    // Return the full HTML structure of the order card
    return `
        <div class="orders orderCard">
            <a href="orderView.html?id=${encodeURIComponent(order.id)}&displayId=${encodeURIComponent(displayId)}&platform=${encodeURIComponent(order.platform || "")}" class="orderLink">
                <div class="orderLeft">
                    <span class="orderId">Order #${displayId}</span>
                    <span class="orderId">${importedLabel}</span>
                </div>
                <div class="orderRight">
                    <span class="platform">${platform}</span>
                    <span class="total">₱ ${totalWorth}</span>
                </div>
            </a>
            ${actionHtml}
        </div>
    `;
}

// Function to format date values
function formatOrderDate(value) {

    const date = new Date(value); // Convert value to Date object

    if (Number.isNaN(date.getTime())) {
        return value; // If invalid date, return original value
    }

    return date.toLocaleString(); // Return formatted date string
}