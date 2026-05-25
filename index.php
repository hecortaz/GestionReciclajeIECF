<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect_for_role();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND active = 1 LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && verify_password($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];
        redirect_for_role();
    }

    $error = 'Usuario o contraseña incorrectos.';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(APP_NAME) ?> | Inicio de sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
</head>
<body class="login-body">
  <section class="login-shell">
    <div class="login-brand">
      <img src="assets/img/LOGO_IECF_2015.JPG" alt="Logo IECF">
      <div>
        <p class="kicker">I.E. Cincuentenario de Fabricato</p>
        <h1>Gestión del reciclaje institucional</h1>
        <p class="lead">Inventario institucional para registrar residuos, calcular aprovechamiento y tomar decisiones ambientales.</p>
        <div class="login-highlights">
          <span><i class="bi bi-bar-chart"></i> Indicadores en tiempo real</span>
          <span><i class="bi bi-geo-alt"></i> Control por zonas</span>
          <span><i class="bi bi-shield-check"></i> Acceso por roles</span>
        </div>
      </div>
    </div>

    <form method="post" class="login-card" autocomplete="off">
      <h2>Inicio de sesión</h2>
      <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= e($error) ?></div>
      <?php endif; ?>
      <label class="form-label" for="username">Usuario</label>
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-person"></i></span>
        <input class="form-control" id="username" name="username" required>
      </div>
      <label class="form-label" for="password">Contraseña</label>
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
        <input class="form-control" type="password" id="password" name="password" required>
      </div>
      <button class="btn btn-primary w-100" type="submit">Entrar</button>
      <div class="demo-users">
        <strong>Usuarios de ejemplo</strong>
        <span>software / software123</span>
        <span>monitoreo / monitoreo123</span>
        <span>jefe / jefe123</span>
      </div>
    </form>
  </section>
</body>
</html>
