<?php
// create_admin.php - run once
require_once __DIR__ . '/includes/db_connect.php';

if (php_sapi_name() === 'cli') {
    $username = readline("Enter username: ");
    $pass = readline("Enter password: ");
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $pass = $_POST['password'] ?? '';
    } else {
        echo '<form method="post">
                <label>Username</label><br><input name="username"><br>
                <label>Password</label><br><input name="password" type="password"><br><button>Save</button>
              </form>';
        exit;
    }
}

if (!empty($username) && !empty($pass)) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    try {
        $stmt->execute([$username, $hash]);
        echo "Admin account created. Please delete create_admin.php when done.\n";
    } catch (Exception $e) {
        echo "Error creating admin: " . $e->getMessage();
    }
} else {
    echo "Provide username & password.\n";
}
