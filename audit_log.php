<?php include 'db_connect.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MUHINGABO - Audit Log</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="mb-3" style="color: #fff;">📋 Unified Audit Log</h3>
      
      <div class="mb-3">
        <div class="btn-group" role="group">
          <input type="radio" class="btn-check" name="filter" id="all" value="all" checked>
          <label class="btn btn-outline-primary" for="all">All</label>
          
          <input type="radio" class="btn-check" name="filter" id="add" value="ADD">
          <label class="btn btn-outline-info" for="add">Add</label>
          
          <input type="radio" class="btn-check" name="filter" id="update" value="UPDATE">
          <label class="btn btn-outline-warning" for="update">Update</label>
          
          <input type="radio" class="btn-check" name="filter" id="sale" value="SALE">
          <label class="btn btn-outline-danger" for="sale">Sales</label>
          
          <input type="radio" class="btn-check" name="filter" id="purchase" value="PURCHASE">
          <label class="btn btn-outline-success" for="purchase">Purchases</label>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>Time</th>
              <th>Action Type</th>
              <th>Item</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody id="logBody">
            <?php
              $logres = mysqli_query($conn, "SELECT a.*, i.item_name FROM actions a LEFT JOIN items i ON a.item_id = i.item_id ORDER BY a.action_date DESC LIMIT 500");
              if (!$logres) {
                echo '<tr><td colspan="4" class="text-center text-danger">Error loading logs</td></tr>';
              } else {
                if (mysqli_num_rows($logres) == 0) {
                  echo '<tr><td colspan="4" class="text-center text-muted">No audit logs yet</td></tr>';
                } else {
                  while ($l = mysqli_fetch_assoc($logres)) {
                    $type_badge = '';
                    if ($l['action_type'] === 'ADD') $type_badge = 'bg-info';
                    elseif ($l['action_type'] === 'UPDATE') $type_badge = 'bg-warning text-dark';
                    elseif ($l['action_type'] === 'SALE') $type_badge = 'bg-danger';
                    elseif ($l['action_type'] === 'PURCHASE') $type_badge = 'bg-success';
                    else $type_badge = 'bg-secondary';
                    
                    $iname = $l['item_name'] ? htmlspecialchars($l['item_name']) : '—';
                    $when = date('Y-m-d H:i:s', strtotime($l['action_date']));
                    echo '<tr class="action-row" data-type="'.htmlspecialchars($l['action_type']).'">';
                    echo '<td><small>'.htmlspecialchars($when).'</small></td>';
                    echo '<td><span class="badge '.$type_badge.'">'.htmlspecialchars($l['action_type']).'</span></td>';
                    echo '<td><strong>'.htmlspecialchars($iname).'</strong></td>';
                    echo '<td><small>'.htmlspecialchars($l['details']).'</small></td>';
                    echo '</tr>';
                  }
                }
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('input[name="filter"]').forEach(radio => {
  radio.addEventListener('change', () => {
    const filter = radio.value;
    document.querySelectorAll('.action-row').forEach(row => {
      if (filter === 'all' || row.dataset.type === filter) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
});
</script>
</body>
</html>