<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$error = '';
$product_id = $_GET['id'] ?? 0;

// fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die('Product not found.');
}

// fetch existing images
$stmt_img = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
$stmt_img->execute([$product_id]);
$images = $stmt_img->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categories = $_POST['category'] ?? []; // array of selected checkboxes

    if ($title === '') {
        $error = 'Title is required.';
    } elseif (empty($categories)) {
        $error = 'Please select at least one category.';
    } else {
        $category_str = implode(',', $categories);

        // update product
        $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, category = ? WHERE id = ?");
        $stmt->execute([$title, $description, $category_str, $product_id]);

        // handle multiple image uploads
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

// convert product category string into array for checkboxes
$product_categories = explode(',', $product['category']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Product</title>
<link href="/assets/style.css" rel="stylesheet">
</head>
<body>
<div class="container" style="max-width:720px;margin-top:40px">
  <h2>Edit Product</h2>
  <?php if ($error): ?><div style="color:#b00020"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label>Title</label><br>
    <input name="title" value="<?= htmlspecialchars($product['title']) ?>" style="width:100%;padding:10px;border-radius:8px"><br><br>

    <label>Category</label><br>
    <div class="category-options">
      <label><input type="checkbox" name="category[]" value="KYC" <?= in_array('KYC', $product_categories) ? 'checked' : '' ?>> KYC</label>
      <label><input type="checkbox" name="category[]" value="CSV" <?= in_array('CSV', $product_categories) ? 'checked' : '' ?>> CSV</label>
    </div><br>

    <label>Description</label><br>
    <textarea name="description" rows="6" style="width:100%;padding:10px;border-radius:8px"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

    <label>Existing Images</label><br>
    <?php foreach ($images as $img): ?>
      <a href="/uploads/<?= $img['image_path'] ?>" target="_blank">
        <img src="/uploads/<?= $img['image_path'] ?>" style="width:100px;height:100px;object-fit:cover;margin:5px;border-radius:8px">
      </a>
    <?php endforeach; ?><br><br>

    <label>Add More Images</label><br>
    <input type="file" name="images[]" multiple accept="image/*"><br>
    <small>You can select multiple images</small><br><br>

    <button class="btn btn-add" type="submit">Save</button>
    <a href="/admin/dashboard.php" class="btn" style="margin-left:8px">Cancel</a>
  </form>
</div>
</body>
</html>
