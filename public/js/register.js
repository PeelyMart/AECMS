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
	//this is not formal form validation this is just to have a dynamic html message 
	// Password check
    if (data.get("password") === data.get("confirm")) {
        approve++;
    } else {
        document.getElementById("passwordMessage").innerHTML = passwordError;
    }

    // Email check
    if (data.get("email").includes("@") && data.get("email").includes(".")) {
        approve++;
    } else {
        document.getElementById("emailMessage").innerHTML = emailError;
    }

    // Contact number check (allow empty)
    let contact = data.get("contactNumber");
    if (contact === "" || numbersOnly.test(contact)) {
        approve++;
    } else {
        document.getElementById("numberMessage").innerHTML = numberError;
    }

    // If all validations passed, submit the form
    if (approve === 3) {
	// this sends it to register.php as normal
        form.submit();
	    alert("Account created: Contact admin for ID and approval");
    }
}
