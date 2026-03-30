<?php
require 'DBConnection.php';
$map = [];
$orderTotals = [];
$platform = $_POST['platform'];
$ordersCache = [];
switch($platform){
case 'shopee':
	$map = [
		'order_id' => 'order id',
		'sku' => 'sku reference no.',
		'qty' => 'quantity',
		'buyer' => 'username (buyer)',
	];
	break;
case 'lazada':
	$map = [
		'order_id' => 'ordernumber',
		'sku' => 'lazadasku',
		'buyer' => 'customername',
		'qty' => null,
	];
	break;
case 'tiktok': 
	$map = [
		'order_id' => 'order id',
		'sku' => 'sku id',
		'buyer' => null,
		'qty' => null
	];
	break;
}




// Open CSV file
if (($handle = fopen($_FILES['csvFile']['tmp_name'], 'r')) !== false) {
	$headers = fgetcsv($handle, 0, ",", '"', "\\");

	$headers = array_map(function($h) {
	    return trim(str_replace("\xEF\xBB\xBF", '', $h));
	}, $headers); //files have weird starters so we unify all of them

	$headers = array_map('strtolower', $headers);
	//further formatting to make it a safer
	



	while (($row = fgetcsv($handle, 0, ",", '"', "\\")) !== false) {
	    if (!$row || count(array_filter($row)) === 0) {
		continue;
	    }

	    if (empty(trim($row[0]))) {
		continue;
	    }

	    if (count($headers) !== count($row)) {
		    continue; // skip malformed rows
	    }
	//this was to fix the issue that it breaks on csv that may me poorly formatted: blank rows
	$data = array_combine($headers, $row);

	    $externalOrderId = $data[$map['order_id']];
	    $buyer = isset($map['buyer']) ? $data[$map['buyer']] : 'TiktokUnknown'; //tiktok imports do not include the buyer's username

	$ext_sku = $data[$map['sku']];
	$qty = isset($map['qty']) ? $data[$map['qty']] : 1;
	$priceSnapshot = 0;
	$extractedPID = "";
	$result = skuExtractor($conn, $ext_sku, $priceSnapshot, $extractedPID);
	//Snapshot because it queries the current price referencing the 'Products' table, it may change however it records in the moment
	if($result === 0){
		continue; //it does not get saved, that means the admin was throwing and did not set any corresponding SKU in the products table
	};  

	    
        
	if (!isset($ordersCache[$externalOrderId])) {
		$placeHolderWorth = 0.00;
		$stmt = $conn -> prepare("INSERT INTO orders_header (ext_id, platform, buyer_username, total_worth) VALUES (?,?,?,?)");
		$stmt->bind_param("sssi", $externalOrderId, $platform, $buyer, $placeHolderWorth);
		$stmt->execute();

		// Get inserted order ID

            $orderId = $conn->insert_id;
            // Cache it for future rows
            $ordersCache[$externalOrderId] = $orderId;
        } else {
            // Order header already inserted, get internal order ID
            $orderId = $ordersCache[$externalOrderId];
        }
        
	// We are inserting order item linked to order header here 
	$sub_total = (float)$qty * $priceSnapshot;
	echo $sub_total;
	if (!isset($orderTotals[$orderId])) { //we lowkey are using a not very efficient hashing system but for now its a proof of concept
	    $orderTotals[$orderId] = 0;
	}
	$orderTotals[$orderId] += $sub_total;

	$stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, external_sku, qty, unit_price_snapshot, sub_total) VALUES (?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("iisidd", $orderId, $extractedPID,$ext_sku, $qty, $priceSnapshot, $sub_total);
	$stmt->execute();


        }
    
    //To update the total worth of the package.
	foreach ($orderTotals as $orderId => $total) {
	    $stmt = $conn->prepare("
		UPDATE orders_header 
		SET total_worth = ? 
		WHERE id = ?
		");
	$stmt->bind_param("di", $total, $orderId);
	$stmt->execute();
	}

    

    fclose($handle);
}
    


function skuExtractor($conn, $sku, &$price, &$productID){
	$sql = "SELECT id, unit_price FROM products WHERE ? in (l_sku, s_sku, t_sku) LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $sku);
	$stmt -> execute();
	$result = $stmt->get_result(); 
	if($row = $result->fetch_assoc()){
		$price = $row['unit_price'];
		$productID = $row['id'];
		return 1;
	}
	else{
		return 0;
	}
}

?>
