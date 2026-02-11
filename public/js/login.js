
$form = document.getElementById("loginForm");
$form.addEventListener("submit", initLogin)



function initLogin(e){
	e.preventDefault();
	
	

fetch("endpoint/login.php",{
	method: "POST",
	body:  new FormData(e.target)
	})
	.then(res => res.json())
	.then(data => 

		{
		if(data.status === "success"){
			window.location.href = "dashboard.html";
		}else {
			console.log(data.errorMsg);
		}
	});

}
