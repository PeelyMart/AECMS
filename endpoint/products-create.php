<?php
require 'database/DBConnection.php';
session_start();
if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "errorMsg" => "Admin access required"
    ]);
    exit;
}





$name = $_POST['name'];
$qty = $_POST['qty'];
$remarks = $_POST['remarks'];
$unitPrice = $_POST['unitPrice'];
$lazSku = $_POST['lsku'];
$shopSku = $_POST['ssku'];
$tikSku = $_POST['tsku'];
skuCheck($lazSku);
skuCheck($shopSku);
skuCheck($tikSku);
$entriesSKU = array(
"l_sku" => $lazSku,
"s_sku" => $shopSku,
"t_sku" => $tikSku
);

$sql = $conn->prepare("INSERT INTO products (name, l_sku, s_sku, t_sku, qty, remarks, unit_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
$sql ->bind_param("ssssisd", $name, $lazSku, $shopSku, $tikSku, $qty, $remarks, $unitPrice);
$sql ->execute();
header("Location: ../public/a-readProd.html");







//if empty string it places a null, mainly used for the sku's as they can be null
function skuCheck(&$sku){
	if($sku === ""){
		$sku = null;
		return;
	}
	return;
}
?>
