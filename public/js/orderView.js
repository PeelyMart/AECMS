document.addEventListener("DOMContentLoaded", async () => {

    // =========================
    // GET URL PARAMETERS
    // =========================
    const params = new URLSearchParams(window.location.search);
    const orderId = params.get("id");
    const viewType = params.get("type");
	console.log(viewType);
	switch(viewType){
		case 'pending':
			document.getElementById("actionButton").href = `../endpoint/packOrder.php?id=${orderId}`;
			document.getElementById("actionButton").innerText = "pack";
			break;
		case 'unclaimed':
			document.getElementById("actionButton").href = `../endpoint/claimOrder.php?id=${orderId}`;
			document.getElementById("actionButton").innerText = `claim`;
			break;
		default:
			break;
	}
	console.log("exitedcase");

    // =========================
    // FETCH ORDER DATA
    // =========================
    const res = await fetch("../endpoint/orderViewGet.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id=${orderId}`
    });

    const data = await res.json();
	

    // =========================
    // ITEMS CONTAINER
    // =========================
    const container = document.getElementById("itemsContainer");
	document.getElementById("OrderID").textContent = `ORDER ID (external) = ${data[0].ext_id}`;
	document.getElementById("Platform").textContent = `Ordered on: ${data[0].platform}`;
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
		<span>
			<p style="color: white;" class="orderItemName">
			SUB TOTAL: ${data[i].sub_total}
			</p>

		</span>
            </a>
        `;
	
    }


});
