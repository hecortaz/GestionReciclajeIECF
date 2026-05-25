<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_manager();

$editingId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editingUser = null;

if ($editingId) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$editingId]);
    $editingUser = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $fullName = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $role = trim($_POST['role']);
    $active = isset($_POST['active']) ? 1 : 0;
    $password = trim($_POST['password'] ?? '');

    if ($id > 0) {
        if ($password !== '') {
            $stmt = $pdo->prepare('UPDATE users SET full_name = ?, username = ?, role = ?, active = ?, password_hash = ? WHERE id = ?');
            $stmt->execute([$fullName, $username, $role, $active, password_hash($password, PASSWORD_DEFAULT), $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET full_name = ?, username = ?, role = ?, active = ? WHERE id = ?');
            $stmt->execute([$fullName, $username, $role, $active, $id]);
        }
        header('Location: users.php?updated=1');
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO users (full_name, username, password_hash, role, active) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$fullName, $username, password_hash($password, PASSWORD_DEFAULT), $role, $active]);
    header('Location: users.php?created=1');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id !== current_user()['id']) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }
    header('Location: users.php?deleted=1');
    exit;
}

$users = $pdo->query('SELECT * FROM users ORDER BY role, full_name')->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
  <div>
    <p class="kicker">Administración</p>
    <h1>Usuarios del sistema</h1>
  </div>
</div>

<?php if (isset($_GET['created'])): ?><div class="alert alert-success">Usuario creado correctamente.</div><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Usuario actualizado correctamente.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-warning">Usuario eliminado correctamente.</div><?php endif; ?>

<section class="admin-layout">
  <form method="post" class="form-surface">
    <h2><?= $editingUser ? 'Editar usuario' : 'Nuevo usuario' ?></h2>
    <input type="hidden" name="id" value="<?= (int) ($editingUser['id'] ?? 0) ?>">
    <label class="form-label">Nombre completo</label>
    <input class="form-control mb-3" name="full_name" value="<?= e($editingUser['full_name'] ?? '') ?>" required>
    <label class="form-label">Usuario</label>
    <input class="form-control mb-3" name="username" value="<?= e($editingUser['username'] ?? '') ?>" required>
    <label class="form-label">Rol</label>
    <select class="form-select mb-3" name="role" required>
      <?php foreach (['jefe_proyecto', 'aprendiz_software', 'aprendiz_monitoreo'] as $role): ?>
        <option value="<?= e($role) ?>" <?= ($editingUser['role'] ?? '') === $role ? 'selected' : '' ?>><?= e(role_label($role)) ?></option>
      <?php endforeach; ?>
    </select>
    <label class="form-label">Contraseña <?= $editingUser ? '(dejar vacía para conservar)' : '' ?></label>
    <input class="form-control mb-3" type="password" name="password" <?= $editingUser ? '' : 'required' ?>>
    <div class="form-check form-switch mb-3">
      <input class="form-check-input" type="checkbox" name="active" id="activeUser" <?= (int) ($editingUser['active'] ?? 1) === 1 ? 'checked' : '' ?>>
      <label class="form-check-label" for="activeUser">Usuario activo</label>
    </div>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Guardar usuario</button>
    <?php if ($editingUser): ?>
      <a class="btn btn-outline-secondary" href="users.php">Cancelar</a>
    <?php endif; ?>
  </form>

  <div class="table-surface">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $item): ?>
          <tr>
            <td><?= e($item['full_name']) ?></td>
            <td><?= e($item['username']) ?></td>
            <td><?= e(role_label($item['role'])) ?></td>
            <td><span class="status-pill <?= (int) $item['active'] === 1 ? 'ok' : 'bad' ?>"><?= (int) $item['active'] === 1 ? 'Activo' : 'Inactivo' ?></span></td>
            <td class="text-end action-buttons">
              <a class="btn btn-sm btn-outline-primary" href="users.php?edit=<?= (int) $item['id'] ?>" title="Editar"><i class="bi bi-pencil"></i></a>
              <?php if ((int) $item['id'] !== current_user()['id']): ?>
                <a class="btn btn-sm btn-outline-danger" href="users.php?delete=<?= (int) $item['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')" title="Eliminar"><i class="bi bi-trash"></i></a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
