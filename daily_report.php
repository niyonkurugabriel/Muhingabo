<?php include 'db_connect.php'; 

// Default to today
$report_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$start = $report_date . ' 00:00:00';
$end = $report_date . ' 23:59:59';

// Get sales data
$sales_res = mysqli_query($conn, "SELECT SUM(quantity) as total_qty, SUM(total) as total_sales FROM sales WHERE sale_date BETWEEN '$start' AND '$end'");
$sales_data = mysqli_fetch_assoc($sales_res);
$total_sales = (float)($sales_data['total_sales'] ?? 0);
$total_qty_sold = (int)($sales_data['total_qty'] ?? 0);

// Get purchase data
$purchase_res = mysqli_query($conn, "SELECT SUM(quantity) as total_qty, SUM(total) as total_purchases FROM purchases WHERE purchase_date BETWEEN '$start' AND '$end'");
$purchase_data = mysqli_fetch_assoc($purchase_res);
$total_purchases = (float)($purchase_data['total_purchases'] ?? 0);
$total_qty_bought = (int)($purchase_data['total_qty'] ?? 0);

// Calculate income (revenue from sales)
$income = $total_sales;

// Calculate expenses (cost of purchases)
$expenses = $total_purchases;

// Calculate net balance
$net_balance = $income - $expenses;

// Get transaction counts
$sales_count_res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM sales WHERE sale_date BETWEEN '$start' AND '$end'");
$sales_count = mysqli_fetch_assoc($sales_count_res)['cnt'];

$purchase_count_res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM purchases WHERE purchase_date BETWEEN '$start' AND '$end'");
$purchase_count = mysqli_fetch_assoc($purchase_count_res)['cnt'];

// Get sales breakdown by item
$sales_items_res = mysqli_query($conn, "SELECT i.item_name, SUM(s.quantity) as qty, SUM(s.total) as total FROM sales s LEFT JOIN items i ON s.item_id = i.item_id WHERE s.sale_date BETWEEN '$start' AND '$end' GROUP BY s.item_id ORDER BY total DESC");

// Get purchase breakdown by item
$purchase_items_res = mysqli_query($conn, "SELECT i.item_name, SUM(p.quantity) as qty, SUM(p.total) as total FROM purchases p LEFT JOIN items i ON p.item_id = i.item_id WHERE p.purchase_date BETWEEN '$start' AND '$end' GROUP BY p.item_id ORDER BY total DESC");

// Prepare monthly aggregates for last 12 months (Revenue, Expenses)
$months = [];
$monthLabels = [];
$monthKeys = [];
$now = new DateTime();
for ($i = 11; $i >= 0; $i--) {
  $dt = (clone $now)->modify("-{$i} months");
  $key = $dt->format('Y-m');
  $label = $dt->format('M Y');
  $months[$key] = ['sales' => 0.0, 'purchases' => 0.0];
  $monthLabels[] = $label;
  $monthKeys[] = $key;
}

$startMonths = date('Y-m-01 00:00:00', strtotime('-11 months'));

$mres = mysqli_query($conn, "SELECT DATE_FORMAT(sale_date,'%Y-%m') as ym, SUM(total) as total FROM sales WHERE sale_date >= '$startMonths' GROUP BY ym");
if ($mres) {
  while ($r = mysqli_fetch_assoc($mres)) {
    $ym = $r['ym'];
    if (isset($months[$ym])) $months[$ym]['sales'] = (float)$r['total'];
  }
}

$mres2 = mysqli_query($conn, "SELECT DATE_FORMAT(purchase_date,'%Y-%m') as ym, SUM(total) as total FROM purchases WHERE purchase_date >= '$startMonths' GROUP BY ym");
if ($mres2) {
  while ($r = mysqli_fetch_assoc($mres2)) {
    $ym = $r['ym'];
    if (isset($months[$ym])) $months[$ym]['purchases'] = (float)$r['total'];
  }
}

$monthly_sales = [];
$monthly_purchases = [];
$monthly_net = [];
foreach ($monthKeys as $k) {
  $monthly_sales[] = $months[$k]['sales'];
  $monthly_purchases[] = $months[$k]['purchases'];
  $monthly_net[] = $months[$k]['sales'] - $months[$k]['purchases'];
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MUHINGABO - Daily Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .metric-card { border-left: 4px solid; min-height: 120px; }
    .metric-income { border-left-color: #28a745; }
    .metric-expense { border-left-color: #dc3545; }
    .metric-balance { border-left-color: #007bff; }
    .metric-value { font-size: 1.8rem; font-weight: bold; }
    .print-btn { position: fixed; bottom: 30px; right: 30px; }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container-lg py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 style="color: #fff;">📊 Daily Report & Balance Sheet</h1>
    <div class="input-group" style="width: 200px;">
      <input type="date" id="reportDate" class="form-control" value="<?php echo $report_date; ?>">
      <button type="button" class="btn btn-primary" onclick="goToDate()">Go</button>
    </div>
  </div>

  <!-- Monthly Transactions Chart -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #2a2a3e, #1e1e2e); border-bottom: 1px solid #333; color: #fff;">
          <h5 class="mb-0">📈 Monthly Transactions (last 12 months)</h5>
        </div>
        <div class="card-body">
          <canvas id="monthlyChart" height="100"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
      <div class="card metric-card metric-income shadow-sm">
        <div class="card-body">
          <h6 class="text-muted">Total Income (Sales)</h6>
          <div class="metric-value text-success">$<?php echo number_format($income, 2); ?></div>
          <small class="text-muted"><?php echo $total_qty_sold; ?> units sold (<?php echo $sales_count; ?> transactions)</small>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card metric-card metric-expense shadow-sm">
        <div class="card-body">
          <h6 class="text-muted">Total Expenses (Purchases)</h6>
          <div class="metric-value text-danger">$<?php echo number_format($expenses, 2); ?></div>
          <small class="text-muted"><?php echo $total_qty_bought; ?> units bought (<?php echo $purchase_count; ?> transactions)</small>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card metric-card metric-balance shadow-sm">
        <div class="card-body">
          <h6 class="text-muted">Net Balance</h6>
          <div class="metric-value <?php echo $net_balance >= 0 ? 'text-success' : 'text-danger'; ?>">
            <?php echo $net_balance >= 0 ? '+' : '-'; ?>$<?php echo number_format(abs($net_balance), 2); ?>
          </div>
          <small class="text-muted">Income - Expenses</small>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="text-muted">Margin</h6>
          <div class="metric-value text-info">
            <?php echo $income > 0 ? number_format(($net_balance / $income) * 100, 1) : '0'; ?>%
          </div>
          <small class="text-muted">Net margin</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Sales Breakdown -->
  <div class="row g-3 mb-4">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #2a2a3e, #1e1e2e); border-bottom: 1px solid #333; color: #fff;">
          <h5 class="mb-0">💰 Sales Breakdown</h5>
        </div>
        <div class="card-body">
          <?php if (mysqli_num_rows($sales_items_res) == 0): ?>
            <p class="text-muted">No sales recorded for <?php echo $report_date; ?></p>
          <?php else: ?>
            <table class="table table-sm">
              <thead class="table-light">
                <tr><th>Item</th><th class="text-end">Qty</th><th class="text-end">Total</th></tr>
              </thead>
              <tbody>
                <?php while ($s = mysqli_fetch_assoc($sales_items_res)): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($s['item_name'] ?? 'N/A'); ?></td>
                    <td class="text-end"><strong><?php echo $s['qty']; ?></strong></td>
                    <td class="text-end text-success"><strong>$<?php echo number_format($s['total'], 2); ?></strong></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Purchase Breakdown -->
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #2a2a3e, #1e1e2e); border-bottom: 1px solid #333; color: #fff;">
          <h5 class="mb-0">📦 Purchase Breakdown</h5>
        </div>
        <div class="card-body">
          <?php if (mysqli_num_rows($purchase_items_res) == 0): ?>
            <p class="text-muted">No purchases recorded for <?php echo $report_date; ?></p>
          <?php else: ?>
            <table class="table table-sm">
              <thead class="table-light">
                <tr><th>Item</th><th class="text-end">Qty</th><th class="text-end">Cost</th></tr>
              </thead>
              <tbody>
                <?php while ($p = mysqli_fetch_assoc($purchase_items_res)): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($p['item_name'] ?? 'N/A'); ?></td>
                    <td class="text-end"><strong><?php echo $p['qty']; ?></strong></td>
                    <td class="text-end text-danger"><strong>$<?php echo number_format($p['total'], 2); ?></strong></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Financial Summary -->
  <div class="row">
    <div class="col-lg-8 offset-lg-2">
      <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
          <h5 class="mb-0">📋 Financial Summary</h5>
        </div>
        <div class="card-body">
          <table class="table table-borderless">
            <tr>
              <td><strong>Total Income (Revenue)</strong></td>
              <td class="text-end text-success"><strong>+ $<?php echo number_format($income, 2); ?></strong></td>
            </tr>
            <tr>
              <td><strong>Total Expenses (Cost)</strong></td>
              <td class="text-end text-danger"><strong>- $<?php echo number_format($expenses, 2); ?></strong></td>
            </tr>
            <tr class="table-active">
              <td><strong style="font-size: 1.2rem;">Net Balance (Profit/Loss)</strong></td>
              <td class="text-end <?php echo $net_balance >= 0 ? 'text-success' : 'text-danger'; ?>"><strong style="font-size: 1.2rem;">
                <?php echo $net_balance >= 0 ? '+' : ''; ?>$<?php echo number_format($net_balance, 2); ?>
              </strong></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="text-center mt-4">
    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">🖨️ Print Report</button>
    <a href="daily_report.php?date=<?php echo date('Y-m-d', strtotime($report_date . ' -1 day')); ?>" class="btn btn-outline-primary">← Previous Day</a>
    <a href="daily_report.php" class="btn btn-outline-primary">Today</a>
    <a href="daily_report.php?date=<?php echo date('Y-m-d', strtotime($report_date . ' +1 day')); ?>" class="btn btn-outline-primary">Next Day →</a>
  </div>
</div>

<script>
function goToDate() {
  const date = document.getElementById('reportDate').value;
  window.location.href = 'daily_report.php?date=' + date;
}
</script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function(){
    const labels = <?php echo json_encode($monthLabels); ?>;
    const sales = <?php echo json_encode($monthly_sales); ?>;
    const purchases = <?php echo json_encode($monthly_purchases); ?>;
    const net = <?php echo json_encode($monthly_net); ?>;

    const ctx = document.getElementById('monthlyChart');
    if (!ctx) return;
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Revenue (Sales)',
            data: sales,
            backgroundColor: 'rgba(40,167,69,0.6)',
            borderColor: 'rgba(40,167,69,0.9)',
            yAxisID: 'y',
          },
          {
            label: 'Expenses (Purchases)',
            data: purchases,
            backgroundColor: 'rgba(220,53,69,0.6)',
            borderColor: 'rgba(220,53,69,0.9)',
            yAxisID: 'y',
          },
          {
            label: 'Net',
            data: net,
            type: 'line',
            borderColor: 'rgba(54, 162, 235, 0.9)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            tension: 0.25,
            yAxisID: 'y',
          }
        ]
      },
      options: {
        interaction: { mode: 'index', intersect: false },
        responsive: true,
        plugins: { legend: { position: 'top' }, tooltip: { mode: 'index', intersect: false } },
        scales: {
          y: { beginAtZero: true, ticks: { callback: function(v){ return '$' + Number(v).toLocaleString(); } } }
        }
      }
    });
  })();
</script>
</body>
</html>