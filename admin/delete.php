<?php
// admin/delete.php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    // fetch image to remove
    $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        if (!empty($row['image_path'])) {
            $file = __DIR__ . '/../uploads/' . $row['image_path'];
            if (file_exists($file)) @unlink($file);
        }
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }
}
header('Location: /admin/dashboard.php');
exit;
