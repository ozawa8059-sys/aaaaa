<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categories = $_POST['category'] ?? []; // array of selected checkboxes

    if ($title === '') {
        $error = 'Title is required.';
    } elseif (empty($categories)) {
        $error = 'Please select at least one category.';
    } else {
        // Join multiple selected categories into a string
        $category_str = implode(',', $categories);

        // Insert product
        $stmt = $pdo->prepare("INSERT INTO products (title, description, category) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $category_str]);
        $product_id = $pdo->lastInsertId();

        // Handle multiple images
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] !== 0) continue;

                $fname = basename($_FILES['images']['name'][$key]);
                $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','webp','gif'];

                if (in_array($ext, $allowed)) {
                    $newName = uniqid('img_') . '.' . $ext;
                    $target = $uploadDir . $newName;
                    if (move_uploaded_file($tmp_name, $target)) {
                        $stmt2 = $pdo->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                        $stmt2->execute([$product_id, $newName]);
                    }
                }
            }
        }

        header('Location: /admin/dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Add Product</title>
<link href="/assets/style.css" rel="stylesheet">

</head>
<body>
<div class="container" style="max-width:720px;margin-top:40px">
  <h2>Add Product</h2>
  <?php if ($error): ?><div style="color:#b00020"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  
  <form method="post" enctype="multipart/form-data">
    <label>Title</label><br>
    <input name="title" style="width:100%;padding:10px;border-radius:8px"><br><br>

    <label>Category</label><br>
    <div class="category-options">
      <label><input type="checkbox" name="category[]" value="KYC"> KYC</label>
      <label><input type="checkbox" name="category[]" value="CSV"> CSV</label>
    </div>

    <label>Description</label><br>
    <textarea name="description" rows="6" style="width:100%;padding:10px;border-radius:8px"></textarea><br><br>

    <label>Product Images</label><br>
    <input type="file" name="images[]" multiple accept="image/*"><br>
    <small>You can select multiple images</small><br><br>

    <button class="btn btn-add" type="submit">Save</button>
    <a href="/admin/dashboard.php" class="btn" style="margin-left:8px">Cancel</a>
  </form>
</div>
</body>
</html>
