
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
			window.location.href = data.redirect;
			console.log(data.redirect);
		}else {
			console.log(data.errorMsg);
			console.log(data.debug);
		}
	});

}
