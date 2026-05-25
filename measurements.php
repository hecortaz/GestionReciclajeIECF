<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_manager();

$stmt = $pdo->query("
    SELECT m.*, u.full_name registered_by
    FROM measurements m
    LEFT JOIN users u ON u.id = m.user_id
    ORDER BY m.measurement_date DESC, m.id DESC
");
$measurements = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
  <div>
    <p class="kicker">Control operativo</p>
    <h1>Mediciones registradas</h1>
  </div>
  <a class="btn btn-success" href="measurement_form.php"><i class="bi bi-plus-circle"></i> Nueva medición</a>
</div>

<?php if (isset($_GET['created'])): ?><div class="alert alert-success">Medición creada correctamente.</div><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Medición actualizada correctamente.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-warning">Medición eliminada correctamente.</div><?php endif; ?>

<div class="table-surface">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Zona</th>
          <th>Salón</th>
          <th>Jornada</th>
          <th>Residuo</th>
          <th>Estado</th>
          <th class="text-end">Kg</th>
          <th>Registró</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($measurements as $item): ?>
        <tr>
          <td><?= e($item['measurement_date']) ?></td>
          <td><?= e($item['zone']) ?></td>
          <td><?= e($item['classroom']) ?></td>
          <td><?= e($item['shift']) ?></td>
          <td><?= e($item['waste_type']) ?></td>
          <td><span class="status-pill <?= $item['waste_state'] === 'Aprovechable' ? 'ok' : 'bad' ?>"><?= e($item['waste_state']) ?></span></td>
          <td class="text-end fw-semibold"><?= number_format((float) $item['weight_kg'], 2) ?></td>
          <td><?= e($item['registered_by'] ?? 'Sistema') ?></td>
          <td class="text-end action-buttons">
            <a class="btn btn-sm btn-outline-primary" href="measurement_form.php?id=<?= (int) $item['id'] ?>" title="Editar"><i class="bi bi-pencil"></i></a>
            <form method="post" action="measurement_delete.php" onsubmit="return confirm('¿Eliminar esta medición?');">
              <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
              <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
