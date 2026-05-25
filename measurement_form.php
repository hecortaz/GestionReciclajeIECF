<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$editing = $id > 0;
$record = [
    'measurement_date' => date('Y-m-d'),
    'grade' => '',
    'classroom' => '',
    'zone' => '',
    'shift' => 'Mañana',
    'waste_type' => 'Papel limpio',
    'weight_kg' => '',
    'quantity' => 0,
    'students_count' => '',
];

if ($editing) {
    require_manager();
    $stmt = $pdo->prepare('SELECT * FROM measurements WHERE id = ?');
    $stmt->execute([$id]);
    $record = $stmt->fetch();
    if (!$record) {
        http_response_code(404);
        exit('Medición no encontrada.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$state, $classification] = classify_waste($_POST['waste_type']);
    $data = [
        trim($_POST['measurement_date']),
        trim($_POST['grade']),
        trim($_POST['classroom']),
        trim($_POST['zone']),
        trim($_POST['shift']),
        trim($_POST['waste_type']),
        (float) $_POST['weight_kg'],
        (int) $_POST['quantity'],
        (int) $_POST['students_count'],
        $state,
        $classification,
    ];

    if ($editing) {
        $data[] = $id;
        $stmt = $pdo->prepare("
            UPDATE measurements
            SET measurement_date = ?, grade = ?, classroom = ?, zone = ?, shift = ?, waste_type = ?,
                weight_kg = ?, quantity = ?, students_count = ?, waste_state = ?, classification = ?
            WHERE id = ?
        ");
        $stmt->execute($data);
        header('Location: measurements.php?updated=1');
        exit;
    }

    $data[] = current_user()['id'];
    $stmt = $pdo->prepare("
        INSERT INTO measurements
        (measurement_date, grade, classroom, zone, shift, waste_type, weight_kg, quantity, students_count, waste_state, classification, user_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute($data);
    header('Location: ' . (is_manager() ? 'measurements.php?created=1' : 'measurement_form.php?created=1'));
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-heading compact">
  <div>
    <p class="kicker"><?= $editing ? 'Editar medición' : 'Registro de residuos' ?></p>
    <h1><?= $editing ? 'Actualizar medición' : 'Nueva medición' ?></h1>
  </div>
  <?php if (is_manager()): ?>
    <a class="btn btn-outline-secondary" href="measurements.php"><i class="bi bi-arrow-left"></i> Volver</a>
  <?php endif; ?>
</div>

<?php if (isset($_GET['created'])): ?>
  <div class="alert alert-success">Medición registrada correctamente.</div>
<?php endif; ?>

<form method="post" class="form-surface">
  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label">Fecha</label>
      <input class="form-control" type="date" name="measurement_date" value="<?= e($record['measurement_date']) ?>" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Grado</label>
      <input class="form-control" name="grade" value="<?= e($record['grade']) ?>" placeholder="8°, 9°, Restaurante" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Salón</label>
      <input class="form-control" name="classroom" value="<?= e($record['classroom']) ?>" placeholder="Salón 801" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Zona</label>
      <input class="form-control" name="zone" value="<?= e($record['zone']) ?>" placeholder="Bloque A" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Jornada</label>
      <select class="form-select" name="shift" required>
        <?php foreach (['Mañana', 'Tarde', 'Noche'] as $shift): ?>
          <option value="<?= e($shift) ?>" <?= $record['shift'] === $shift ? 'selected' : '' ?>><?= e($shift) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Tipo de residuo</label>
      <select class="form-select" name="waste_type" id="wasteType" required>
        <?php foreach (['Papel limpio', 'Papel sucio', 'PET limpio', 'PET sucio', 'Orgánico'] as $type): ?>
          <option value="<?= e($type) ?>" <?= $record['waste_type'] === $type ? 'selected' : '' ?>><?= e($type) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Clasificación automática</label>
      <input class="form-control" id="classificationPreview" value="" readonly>
    </div>
    <div class="col-md-4">
      <label class="form-label">Peso (kg)</label>
      <input class="form-control" type="number" step="0.01" min="0.01" name="weight_kg" value="<?= e((string) $record['weight_kg']) ?>" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Cantidad</label>
      <input class="form-control" type="number" min="0" name="quantity" value="<?= e((string) $record['quantity']) ?>" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Número de estudiantes</label>
      <input class="form-control" type="number" min="1" name="students_count" value="<?= e((string) $record['students_count']) ?>" required>
    </div>
  </div>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Guardar medición</button>
  </div>
</form>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
