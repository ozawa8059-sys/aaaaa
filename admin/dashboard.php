<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

// fetch all products
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching products: " . $e->getMessage());
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard</title>
  <link href="/assets/style.css" rel="stylesheet">
</head>
<body>
<div class="container">
  <div class="admin-top">
    <h2>Dashboard</h2>
    <div>
      <a href="/admin/add.php" class="btn btn-add">+ Add product</a>
      <a href="/admin/logout.php" class="btn" style="margin-left:8px">Logout</a>
    </div>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Category</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($products): ?>
        <?php foreach ($products as $p): ?>
        <tr>
          <td style="width:90px">
            <?php
            // fetch first image for this product
            try {
                $stmt_img = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ? ORDER BY id ASC LIMIT 1");
                $stmt_img->execute([$p['id']]);
                $img = $stmt_img->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $img = false;
            }
            ?>
            <?php if ($img && !empty($img['image_path']) && file_exists(__DIR__ . '/../uploads/' . $img['image_path'])): ?>
              <img src="/uploads/<?php echo htmlspecialchars($img['image_path']); ?>" style="width:80px;height:50px;object-fit:cover;border-radius:8px" alt="">
            <?php else: ?>
              <div style="width:80px;height:50px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#999">
                No
              </div>
            <?php endif; ?>
          </td>
          <td><?php echo htmlspecialchars($p['title']); ?></td>
          <td><?php echo htmlspecialchars($p['category']); ?></td>
          <td><?php echo htmlspecialchars($p['created_at']); ?></td>
          <td>
            <a class="btn btn-edit" href="/admin/edit.php?id=<?php echo $p['id']; ?>">Edit</a>
            <a class="btn btn-del" href="/admin/delete.php?id=<?php echo $p['id']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" style="text-align:center;">No products found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
