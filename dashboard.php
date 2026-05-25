<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_manager();

$totals = fetch_dashboard_totals($pdo);

$topGrade = $pdo->query("
    SELECT grade, SUM(weight_kg) total
    FROM measurements
    GROUP BY grade
    ORDER BY total DESC
    LIMIT 1
")->fetch();

$topOrganic = $pdo->query("
    SELECT classroom, SUM(weight_kg) total
    FROM measurements
    WHERE waste_type = 'Orgánico'
    GROUP BY classroom
    ORDER BY total DESC
    LIMIT 1
")->fetch();

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-heading dashboard-hero">
  <div>
    <p class="kicker">Panel del jefe de proyecto</p>
    <h1>Mapa y control de reciclaje</h1>
    <p class="page-subtitle">Seguimiento ejecutivo de residuos por zona, salón, jornada y aprovechamiento.</p>
  </div>
  <a class="btn btn-success" href="measurement_form.php"><i class="bi bi-plus-circle"></i> Nueva medición</a>
</div>

<section class="stats-grid">
  <article class="metric-card">
    <div class="metric-icon"><i class="bi bi-box-seam"></i></div>
    <div><span>Total generado</span><strong><?= number_format($totals['total_kg'], 2) ?> kg</strong></div>
  </article>
  <article class="metric-card success">
    <div class="metric-icon"><i class="bi bi-recycle"></i></div>
    <div><span>Aprovechable</span><strong><?= number_format($totals['usable_kg'], 2) ?> kg</strong></div>
  </article>
  <article class="metric-card warning">
    <div class="metric-icon"><i class="bi bi-exclamation-triangle"></i></div>
    <div><span>No aprovechable</span><strong><?= number_format($totals['unusable_kg'], 2) ?> kg</strong></div>
  </article>
  <article class="metric-card info">
    <div class="metric-icon"><i class="bi bi-graph-up-arrow"></i></div>
    <div><span>Tasa de aprovechamiento</span><strong><?= number_format($totals['rate'], 1) ?>%</strong></div>
  </article>
</section>

<section class="insights-band">
  <div>
    <i class="bi bi-lightbulb"></i>
    <span>El grado con mayor generación es <strong><?= e($topGrade['grade'] ?? 'sin datos') ?></strong>.</span>
  </div>
  <div>
    <i class="bi bi-recycle"></i>
    <span>El <strong><?= number_format($totals['rate'], 1) ?>%</strong> de los residuos son aprovechables.</span>
  </div>
  <div>
    <i class="bi bi-geo-alt"></i>
    <span>Mayor orgánico: <strong><?= e($topOrganic['classroom'] ?? 'sin datos') ?></strong>.</span>
  </div>
</section>

<section class="dashboard-layout">
  <div class="chart-panel">
    <div class="panel-title">
      <h2>Medida por zona</h2>
      <span>kg registrados</span>
    </div>
    <canvas id="zoneChart" height="230"></canvas>
  </div>
  <div class="chart-panel">
    <div class="panel-title">
      <h2>Medida por salón</h2>
      <span>comparativo</span>
    </div>
    <canvas id="classroomChart" height="230"></canvas>
  </div>
  <div class="chart-panel">
    <div class="panel-title">
      <h2>Jornada</h2>
      <span>distribución</span>
    </div>
    <canvas id="shiftChart" height="230"></canvas>
  </div>
  <div class="chart-panel">
    <div class="panel-title">
      <h2>Tipo de residuo</h2>
      <span>porcentaje</span>
    </div>
    <canvas id="wasteChart" height="230"></canvas>
  </div>
</section>

<section class="map-section">
  <div class="panel-title">
    <h2>Mapa de reciclaje por zonas</h2>
    <span>color según tasa de aprovechamiento</span>
  </div>
  <div id="recyclingMap" class="zone-map"></div>
</section>

<script>
window.RECYCLING_STATS_ENDPOINT = 'api/stats.php';
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
