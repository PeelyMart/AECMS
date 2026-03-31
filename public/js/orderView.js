document.addEventListener("DOMContentLoaded", async () => { 
    // Wait until the HTML page is fully loaded before running the script

    const params = new URLSearchParams(window.location.search); 
    // Read the query parameters from the URL (ex: ?id=123&platform=Shopee)

    const orderId = params.get("id"); 
    // Get the order ID from the URL parameters

    const displayId = params.get("displayId") || orderId; 
    // Get displayId if available, otherwise use the orderId

    const platform = params.get("platform") || "Unknown"; 
    // Get the platform name from the URL or set it to "Unknown" if not provided

    document.getElementById("OrderID").innerText = `Order ID [${displayId}]`; 
    // Display the order ID on the page

    document.getElementById("Platform").innerText = `Platform: ${platform}`; 
    // Display the platform name on the page

    const container = document.getElementById("orderItemsContainer"); 
    // Get the HTML element where the order items will be displayed

    container.innerHTML = `<h2 style="color:white; padding-bottom:10px;">Order Items:</h2>`; 
    // Add a header title for the order items section

    try {
        // Send a request to the backend to retrieve the order items
        const res = await fetch("../endpoint/orderViewGet.php", {
            method: "POST", // Use POST request
            headers: {
                "Content-Type": "application/x-www-form-urlencoded" 
                // Set request content type for form-style POST data
            },
            body: `id=${encodeURIComponent(orderId)}` 
            // Send the order ID safely encoded in the request body
        });

        const data = await res.json(); 
        // Convert the response from JSON format into a JavaScript object

        // Check if the returned data is not an array or if there are no items
        if (!Array.isArray(data) || data.length === 0) {
            container.innerHTML += `
                <div class="orderItem">
                    <p style="color:white;">No order items found for this order.</p>
                </div>
            `;
            return; // Stop further execution
        }

        // Loop through each order item returned from the server
        for (let i = 0; i < data.length; i++) {
            container.innerHTML += `
                <a href="#" class="orderItem">
                    <span>
                        <h2 style="color:white;" class="orderItemSKU">
                            SKU: ${data[i].external_sku || data[i].id}
                        </h2>

                        <p style="color:white;" class="orderItemName">
                            NAME: ${data[i].name}
                        </p>

                        <p style="color:white;" class="orderItemQTY">
                            QTY: ${data[i].qty} pcs
                        </p>
                    </span>
                </a>
            `;
            // Dynamically add each order item to the page
            // Shows SKU, product name, and quantity
        }
    } catch (error) {
        // If the request fails (network error, server issue, etc.)
        console.error("Failed to load order items:", error);

        container.innerHTML += `
            <div class="orderItem">
                <p style="color:white;">Unable to load order items right now.</p>
            </div>
        `;
        // Show an error message in the UI
    }
});