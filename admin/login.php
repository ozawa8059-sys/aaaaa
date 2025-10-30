<?php
// admin/login.php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: /admin/dashboard.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Please provide username and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password'])) {
            login($admin['id'], $admin['username']);
            header('Location: /admin/dashboard.php');
            exit;
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login</title>
  <link href="/assets/style.css" rel="stylesheet">
</head>
<body>
  <div class="container" style="max-width:420px;margin:60px auto;">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
      <div style="color:#b00020;margin-bottom:10px"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post">
      <label>Username</label><br>
      <input type="text" name="username" style="width:100%;padding:10px;margin:6px 0;border-radius:8px"><br>
      <label>Password</label><br>
      <input type="password" name="password" style="width:100%;padding:10px;margin:6px 0;border-radius:8px"><br>
      <button class="btn btn-add" type="submit">Login</button>
    </form>
    <p style="margin-top:12px;color:#666;font-size:13px;">Use the setup script to create your admin account (see README).</p>
  </div>
</body>
</html>
