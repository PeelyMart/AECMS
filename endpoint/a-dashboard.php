<?php
require __DIR__ . '/database/DBConnection.php';
session_start();

$userList = $conn->query("SELECT id, firstName, lastName FROM users");
$users = [];
while($u = $userList->fetch_assoc()) {
    $users[] = $u;
}

$query = "SELECT oh.*, u.firstName, u.lastName 
          FROM orders_header oh 
          LEFT JOIN users u ON oh.assigned_to = u.id 
          ORDER BY oh.created_at DESC";
$ordersResult = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="public/stylesheets/u-main.css">
    <style>
        .order-controls { margin-top: 10px; display: flex; gap: 10px; align-items: center; }
        select { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 5px; border-radius: 5px; }
        .btn-update { background: #4CAF50; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .btn-delete { background: #ff4d4d; color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="topBar">
        <h2 class="greet">Order Management</h2>
        <nav>
            <ul class="navList">
                <li><a href="a-dashboard.php" class="navBar selected">Dashboard</a></li>
                <li><a href="endpoint/a-profiles.php" class="navBar">Profiles</a></li>
            </ul>
        </nav>
    </div>

    <div class="bottomContent">
        <div class="module">
            <h2 class="moduleTitle">All System Orders</h2>
            <div class="moduleContainer">
                <?php while($order = $ordersResult->fetch_assoc()): ?>
                    <div class="orders">
                        <span class="OrderId">
                            <strong>Order #<?= $order['ext_id'] ?></strong> [<?= strtoupper($order['platform']) ?>]
                        </span>
                        
                        <p style="font-size: 0.8rem; color: rgba(255,255,255,0.6);">
                            Status: <?= strtoupper($order['status']) ?> | 
                            Current: <?= $order['firstName'] ? $order['firstName'].' '.$order['lastName'] : 'Unclaimed' ?>
                        </p>

                        <form action="updateOrder.php" method="POST" class="order-controls">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="assigned_to">
                                <option value="">-- Mark Unclaimed --</option>
                                <?php foreach($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= ($order['assigned_to'] == $user['id']) ? 'selected' : '' ?>>
                                        Assign to: <?= $user['firstName'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn-update">Update</button>
                            
                            <a href="deleteOrder.php?id=<?= $order['id'] ?>" 
                               class="btn-delete" 
                               onclick="return confirm('WARNING: This will permanently remove the order and its items from the database. Continue?')">
                               Delete
                            </a>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>