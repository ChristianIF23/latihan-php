<!DOCTYPE html>
<html>
<head>
  <title>Detail Todo</title>
  <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <a href="index.php" class="btn btn-secondary mb-3">Kembali</a>
    <div class="card">
      <div class="card-body">
        <h3><?= htmlspecialchars($todo['title']) ?></h3>
        <p><?= nl2br(htmlspecialchars($todo['description'])) ?></p>
        <span class="badge <?= $todo['is_finished'] ? 'bg-success' : 'bg-danger' ?>">
          <?= $todo['is_finished'] ? 'Selesai' : 'Belum Selesai' ?>
        </span>
        <p class="mt-2 text-muted">Dibuat: <?= $todo['created_at'] ?><br>Diperbarui: <?= $todo['updated_at'] ?></p>
      </div>
    </div>
  </div>
</body>
</html>
