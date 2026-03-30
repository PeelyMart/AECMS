console.log("Hello DASHBOARD");


document.addEventListener("DOMContentLoaded", () => {

    fetch("../endpoint/userData.php", {
        method: "POST",
        credentials: "include"
    })
    .then(res => res.json())
    .then(data => {

        if (data.status !== "success") {
            // not logged in → redirect
            window.location.href = "../index.html";
            return;
        }

        const fullName = `${data.firstName} ${data.lastName}`;

        document.getElementById("greet").innerHTML =
            "Hello, " + fullName;

    })
    .catch(err => {
        console.error("Error loading user:", err);
    });
	loadUnclaimed();
});



/*
    const res1 = await fetch("../endpoint/getToPack.php");
    const toPack = await res1.json();

    const toPackContainer = document.querySelector(".currentOrders .moduleContainer");
    toPackContainer.innerHTML = "";

    for (let i = 0; i < toPack.length; i++) {
        toPackContainer.innerHTML += `
            <a href="orderView.html?id=${toPack[i].id}" class="orders">
                <span class="OrderId">Order #${toPack[i].ext_id}</span>
            </a>
        `;
    }


    // =======================
    // PACKED
    // =======================
    const res2 = await fetch("../endpoint/getPacked.php");
    const packed = await res2.json();

    const packedContainer = document.querySelector(".processedOrders .moduleContainer");
    packedContainer.innerHTML = "";

    for (let i = 0; i < packed.length; i++) {
        packedContainer.innerHTML += `
            <a href="orderView.html?id=${packed[i].id}" class="orders">
                <span class="OrderId">Order #${packed[i].ext_id}</span>
            </a>
        `;
    }
*/

    // =======================
    // UNCLAIMED
    // =======================
 async function loadUnclaimed(){
	const res3 = await fetch("../endpoint/getUnclaimed.php");
    const unclaimed = await res3.json();

    const unclaimedContainer = document.querySelector(".unclaimedOrders .moduleContainer");

    for (let i = 0; i < unclaimed.length; i++) {
        unclaimedContainer.innerHTML += `
            <a href="orderView.html?id=${unclaimed[i].id}?&platform=${unclaimed[i].id}" class="orders">
		<div class="orderLeft">
                <span class="orderId">Order #${unclaimed[i].id}</span>
                <span class="orderId">Imported on ${unclaimed[i].created_at}</span>
		</div>
		<div class="orderRight">
                <span class="platform">${unclaimed[i].platform}</span>
                <span class="total">P ${unclaimed[i].total_worth}</span>
		</div>
            </a>
        `;
    }
 }
