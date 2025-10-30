<?php
require_once __DIR__ . '/includes/db_connect.php';

// Handle category filter
$category = $_GET['category'] ?? 'all';

if ($category === 'KYC') {
    $st = $pdo->prepare("SELECT * FROM products WHERE FIND_IN_SET('KYC', category) ORDER BY created_at DESC");
    $st->execute();
} elseif ($category === 'CSV') {
    $st = $pdo->prepare("SELECT * FROM products WHERE FIND_IN_SET('CSV', category) ORDER BY created_at DESC");
    $st->execute();
} else {
    $st = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
}

$products = $st->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<meta charset="utf-8">
<title>Shuju | 数据</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="assets/style.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<header class="site-header">
  <div class="container header-container">
    <a href="index.php" class="logo">
      <img src="/assets/logo.png" alt="Shuju Logo">
    </a>
  </div>
</header>

<main class="container">

  <!-- Category Filter -->
<div class="filter-bar">
  <form method="GET" action="">
    <div class="filter-buttons">
      <button type="submit" name="category" value="all" class="filter-btn <?= ($category === 'all') ? 'active' : '' ?>">All</button>
      <button type="submit" name="category" value="KYC" class="filter-btn <?= ($category === 'KYC') ? 'active' : '' ?>">KYC</button>
      <button type="submit" name="category" value="CSV" class="filter-btn <?= ($category === 'CSV') ? 'active' : '' ?>">CSV</button>
    </div>
  </form>
</div>

<section class="grid">
<?php foreach ($products as $p): ?>
  <?php
    // Fetch images for this product
    $stmt_img = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
    $stmt_img->execute([$p['id']]);
    $images = $stmt_img->fetchAll();
  ?>
<article class="card">
  <div class="card-media">
    <?php if ($images): ?>
      <div class="image-slider" data-images='<?= json_encode(array_column($images, 'image_path')) ?>'>
        <?php foreach ($images as $index => $img): ?>
          <div class="slide <?= $index === 0 ? 'active' : '' ?>">
            <img src="/uploads/<?= $img['image_path'] ?>" alt="<?= htmlspecialchars($p['title']); ?>">
          </div>
        <?php endforeach; ?>
        <?php if (count($images) > 1): ?>
          <button class="prev"></button>
          <button class="next"></button>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="placeholder">No image</div>
    <?php endif; ?>
  </div>

  <div class="card-body">
    <h3><?= htmlspecialchars($p['title']); ?></h3>
    <p class="category"><?= htmlspecialchars($p['category']); ?></p>
    <p class="desc"><?= nl2br(htmlspecialchars($p['description'])); ?></p>
    <button class="buy-btn" data-title="<?= htmlspecialchars($p['title']); ?>">Buy</button>
  </div>
</article>


<?php endforeach; ?>

<?php if (empty($products)): ?>
  <p>No products found in this category.</p>
<?php endif; ?>
</section>
</main>

<footer class="site-footer">
<div class="container">© <?= date('Y'); ?> Shuju Team</div>
</footer>

<!-- Buy modal -->
<div id="buyModal" class="modal">
  <div class="modal-inner">
    <button class="modal-close">&times;</button>
    <h2 id="modalTitle">Contact</h2>
    <p>Contact us to buy this product:</p>
    <ul class="contacts">
      <li>Telegram: <a id="tgLink" href="https://t.me/beurman" target="_blank">@beurman</a></li>
      <li>Email: ShujuDB@proton.me <a id="emailLink" href="mailto:ShujuDB@proton.me"></a></li>
    </ul>
  </div>
</div>

<script src="assets/script.js"></script>
</body>
</html>
