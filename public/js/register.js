let form = document.getElementById("registerForm")
form.addEventListener("submit", checkForms);

let passwordError = "Passwords must match";
let emailError = "Enter a valid email";
let numberError = "field can only accept characters from [0-9]";
const numbersOnly = /^[0-9]+$/;


function checkForms(e){
	e.preventDefault();
	let data = new FormData(e.target);
	let form = e.target;
	let approve = 0;


	document.getElementById("passwordMessage").innerHTML = "";
	document.getElementById("emailMessage").innerHTML = "";
	if(data.get("password") === data.get("confirm")){
		approve++;

	}else{
		document.getElementById("passwordMessage").innerHTML = passwordError;
	}
	if(data.get("email").includes("@") && data.get("email").includes(".")){
		approve++;
	}else{
		document.getElementById("emailMessage").innerHTML = emailError;
	}if(!numbersOnly.test(data.get("contactNumber"))){
		document.getElementById("numberMessage").innerHTML = numberError;
	}
	
}
