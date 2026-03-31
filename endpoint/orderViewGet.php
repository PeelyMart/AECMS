<?php
require "database/DBConnection.php";

$id = $_POST['id'];

// =====================
// BUILD QUERY
// =====================
$sql = "
	SELECT 
	    p.id, 
	    p.name, 
	    oi.qty, 
	    oi.sub_total, 
	    oh.ext_id, 
	    oh.platform
	FROM order_items oi
	JOIN products p 
	    ON oi.product_id = p.id
	JOIN orders_header oh 
	    ON oi.order_id = oh.id 
	WHERE oi.order_id = ?;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
