<?php
/**
 * Global Header Component
 */
require_once __DIR__ . '/../config/config.php';
$settings = getSettings($pdo);
$pageTitle = isset($pageTitle) ? $pageTitle . " | " . $settings['restaurant_name'] : $settings['restaurant_name'] . " - Gourmet Restaurant & Online Ordering";
$pageDesc = isset($pageDesc) ? $pageDesc : "Order delectable food online from " . $settings['restaurant_name'] . ". Fast delivery, BBQ, Burgers, Pizza, Pakistani delicacies & online reservations.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>assets/images/logo.png">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">

  <!-- Font Awesome Icons 6.4 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom Stylesheet -->
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main>
<div class="container mt-3">
  <?php displayFlash(); ?>
</div>
