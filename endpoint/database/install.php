<?php
require __DIR__ . '/../../vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

    
//ATTENTION ADMINS! DELETE THIS FLIE/MAKE THIS UNACCESSIBLE AFTER FIRST TIME BOOT UP 
//rewrite .ENV file BEFORE RUNNING THIS FILE!
echo "<html>";
echo "<h1>Welcome to the AECMS first time-set up please wait as we set up your database</h1><br>";
// =========================
// DB CONNECTION (NO DB YET)
// =========================
$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$db   = "ITPROG";

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$db`")) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($db);
$sql = file_get_contents("../../config/DEPLOYMENT.sql");

if (!$conn->multi_query($sql)) {
    die("Error running SQL: " . $conn->error);
}

while ($conn->more_results() && $conn->next_result()) {;}

$email = "admin@company.com";
$password = "admin123";

echo("<h1>Creating your admin account using ID = 1 and password = " . $password . "</h1>");

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// avoid duplicate admin if rerun
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {

    $stmt = $conn->prepare("
        INSERT INTO users 
        (email, firstName, lastName, contactNumber, password, status, role)
        VALUES (?, 'Admin', 'User', '0000000000', ?, 'ACTIVE', 'ADMIN')
    ");

    $stmt->bind_param("ss", $email, $hashedPassword);
    $stmt->execute();

    echo "[◊]Installation complete<br>";
    echo "Admin created<br>";

} else {
    echo "[!]Admin already exists if unintented, please clear your databse and run this script again<br>";
}
echo "<h2>Installation Complete</h2>";
echo "<p>Redirecting to login in 30 seconds...</p>";
echo "
<script>
setTimeout(() => {
    window.location.href = '../../index.html';
}, 10000);
</script>
";
//just enough time so that the admin can see our current 
echo "</html>";
$conn->close();

?>
