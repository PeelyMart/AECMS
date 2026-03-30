<?php
require __DIR__ . '/database/DBConnection.php';
session_start();

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profiles</title>
    <link rel="stylesheet" href="public/stylesheets/a-profiles.css">
</head>
<body>
    <div class="topBar">
        <h1 class="greet">Admin Panel: User Profiles</h1>
        <nav>
            <ul class="navList">
                <li><a href="a-dashboard.php" class="navBar">Dashboard</a></li>
                <li><a href="a-profiles.php" class="navBar selected">Profiles</a></li>
            </ul>
        </nav>
    </div>

    <div class="mainContent">
        <div class="module" style="flex: 2;">
            <h2 class="moduleTitle">System Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $user['id']; ?></td>
                        <td><?php echo $user['firstName'] . " " . $user['lastName']; ?></td>
                        <td><span class="badge"><?php echo $user['status']; ?></span></td>
                        <td>
                            <a href="editProfiles.php?id=<?php echo $user['id']; ?>" class="navBar" style="font-size: 0.8rem;">Edit</a>
                            <a href="deleteProfiles.php?id=<?php echo $user['id']; ?>" class="navBar" style="font-size: 0.8rem; color: #ff6b6b;" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>