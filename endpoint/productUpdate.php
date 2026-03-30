<?php
require 'database/DBConnection.php';
$name = $_POST['name']; 
$qty = $_POST['qty']; 
$remarks = $_POST['remarks']; 
$unitPrice = $_POST['unitPrice']; 
$lazSku = $_POST['lsku'];
$shopSku = $_POST['ssku'];
$tikSku = $_POST['tsku'];
$id = $_POST['id'];
skuCheck($lazSku);
skuCheck($shopSku);
skuCheck($tikSku);


$sql = $conn->prepare("UPDATE products SET name = ?, l_sku = ? , s_sku = ? , t_sku = ? , qty = ? , remarks = ? , unit_price = ? WHERE id = ?");
$sql ->bind_param("ssssisdi", $name, $lazSku, $shopSku, $tikSku, $qty, $remarks, $unitPrice, $id);
$sql ->execute();

header("Location: ../public/a-readProd.html");

function skuCheck(&$sku){
	if($sku === ""){
		$sku = null;
		return;
	}
	return;
}
?>
