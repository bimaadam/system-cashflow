<?php
http_response_code(404);
$title = '404 - Halaman Tidak Ditemukan';
$message = 'Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.';
$home = '../index.php';
$logo = '../assets/img/Graceful.jpg';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8f9fa; }
    .card { max-width: 640px; width: 100%; border: none; box-shadow: 0 10px 25px rgba(0,0,0,.08); }
    .brand { display:flex; align-items:center; gap:12px; }
    .brand img { height:48px; border-radius: 6px; object-fit: cover; }
    .code { font-size: 3rem; font-weight: 800; letter-spacing: 2px; }
  </style>
</head>
<body>
  <div class="card p-4 p-md-5 text-center bg-white">
    <div class="brand mb-3 justify-content-center">
      <?php if (is_file($logo)) : ?>
        <img src="<?= htmlspecialchars($logo) ?>" alt="Graceful Decoration">
      <?php endif; ?>
      <div class="text-start">
        <div class="fw-bold">Graceful Decoration</div>
        <div class="text-muted small">Tasikmalaya</div>
      </div>
    </div>
    <div class="code text-warning">404</div>
    <h1 class="h4 mb-2">Halaman Tidak Ditemukan</h1>
    <p class="text-muted mb-4"><?= htmlspecialchars($message) ?></p>
    <div class="d-flex gap-2 justify-content-center">
      <a class="btn btn-primary" href="<?= htmlspecialchars($home) ?>">Kembali ke Beranda</a>
      <a class="btn btn-outline-secondary" href="javascript:history.back()">Kembali</a>
    </div>
  </div>
</body>
</html>
