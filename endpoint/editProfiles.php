<?php
require __DIR__ . '/database/DBConnection.php';
session_start();

if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $fName = $_POST['firstName'];
    $lName = $_POST['lastName'];
    $email = $_POST['email'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET firstName=?, lastName=?, email=?, status=? WHERE id=?");
    $stmt->bind_param("ssssi", $fName, $lName, $email, $status, $id);
    $stmt->execute();
    
    header("Location: a-profiles.php");
    exit;
}

if (!isset($_GET['id'])) { header("Location: a-profiles.php"); exit; }
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="public/stylesheets/a-profiles.css">
    <title>Edit Profile</title>
</head>
<body>
    <div class="mainContent">
        <div class="module">
            <h2 class="moduleTitle">Edit Profile: <?php echo $u['firstName']; ?></h2>
            <form method="POST" action="editProfile.php" class="moduleContainer">
                <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                
                <label>First Name</label>
                <input type="text" name="firstName" value="<?php echo $u['firstName']; ?>">
                
                <label>Last Name</label>
                <input type="text" name="lastName" value="<?php echo $u['lastName']; ?>">
                
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $u['email']; ?>">

                <label>Status</label>
                <select name="status">
                    <option value="PENDING" <?= $u['status'] == 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                    <option value="ACTIVE" <?= $u['status'] == 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
                    <option value="INACTIVE" <?= $u['status'] == 'INACTIVE' ? 'selected' : '' ?>>INACTIVE</option>
                </select>

                <button type="submit" name="update_user" class="btn-save" style="margin-top:20px;">Update User</button>
                <a href="a-profiles.php" style="color: rgba(255,255,255,0.5); text-align: center; text-decoration: none; margin-top: 10px;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>