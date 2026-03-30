document.addEventListener("DOMContentLoaded", async () => {

    // =========================
    // GET URL PARAMETERS
    // =========================
    const params = new URLSearchParams(window.location.search);
    const orderId = params.get("id");
    const platform = params.get("platform");

    // =========================
    // FETCH ORDER DATA
    // =========================
    const res = await fetch("../endpoint/orderViewGet.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id=${orderId}&platform=${platform}`
    });

    const data = await res.json();

    // =========================
    // HEADER
    // =========================
    document.getElementById("OrderID").innerText = `OrderID [${orderId}]`;
    document.getElementById("Platform").innerText = `Platform: ${platform}`;

    // =========================
    // ITEMS CONTAINER
    // =========================
    const container = document.querySelector(".Container");

    // keep first h2 (Order Items title), clear everything else
    container.innerHTML = `<h2 style="color:white; padding-bottom:10px;">Order Items:</h2>`;

    // =========================
    // RENDER ITEMS
    // =========================
    for (let i = 0; i < data.length; i++) {

        container.innerHTML += `
            <a href="#" class="orderItem">
                <span>
                    <h2 style="color:white;" class="orderItemSKU">
                        LOCAL SKU: ${data[i].id}
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
    }

});
