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

});
