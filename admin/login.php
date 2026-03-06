<?php
/**
 * admin/login.php — Session authentication entry point
 *
 * Single-password scheme (no username field).
 * Supports plaintext password AND bcrypt hash (set via settings.php).
 */
session_start();

// Already logged in — go directly to dashboard.
if (isset($_SESSION['admin'])) {
    header('Location: /admin/');
    exit;
}

require_once __DIR__ . '/../includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = $_POST['password'] ?? '';

    if ($submitted === '') {
        $error = 'Password errata.';
    } else {
        $stored = ADMIN_PASSWORD;
        $ok = (substr($stored, 0, 4) === '$2y$')
            ? password_verify($submitted, $stored)
            : ($submitted === $stored);

        if ($ok) {
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            header('Location: /admin/');
            exit;
        } else {
            $error = 'Password errata.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Viaggia col Baffo</title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Admin stylesheet -->
  <link rel="stylesheet" href="/admin/admin.css">
</head>
<body>

<div class="admin-login">
  <div class="admin-login__card">

    <div class="admin-login__logo">
      <i class="fa-solid fa-compass" style="color: var(--gold); margin-right: 6px;"></i>
      Viaggia col Baffo Admin
    </div>
    <p class="admin-login__subtitle">Inserisci la password per accedere al pannello.</p>

    <form method="POST" action="/admin/login.php" autocomplete="on">
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input
          class="form-control"
          type="password"
          id="password"
          name="password"
          autocomplete="current-password"
          autofocus
          required
        >
      </div>

      <?php if ($error !== ''): ?>
        <div class="admin-alert admin-alert-error" role="alert">
          <i class="fa-solid fa-circle-xmark"></i>
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-right-to-bracket"></i>
        Accedi
      </button>
    </form>

  </div>
</div>

</body>
</html>
