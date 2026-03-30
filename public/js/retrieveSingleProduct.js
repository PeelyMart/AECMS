const params = new URLSearchParams(window.location.search);
const id = params.get("id"); //getting the url 



async function loadProduct(){
	const res = await fetch("../endpoint/s-productRet.php?id=" + id);
	const p = await res.json();

	document.getElementById("id").value = p.id;
	document.getElementById("name").value = p.name;
	if(p.l_sku != null){
		document.getElementById("lsku").value = p.l_sku;
	}
	if(p.s_sku != null){
		document.getElementById("ssku").value = p.s_sku;
	}
	if(p.t_sku != null){
		document.getElementById("tsku").value = p.t_sku;
	}
	document.getElementById("qty").value = p.qty;
	document.getElementById("remarks").value = p.remarks;
	document.getElementById("unitPrice").value = p.unit_price;
}

loadProduct();

