<?php 
require_once 'session_config.php';
include 'db_connect.php';

// Require login
require_login();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MUHINGABO - Stock List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="">
<?php include 'navbar.php'; ?>

<div class="container py-4">
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <form id="filterForm" class="row g-2">
        <div class="col-md-4">
          <input id="search" class="form-control" placeholder="Search by name...">
        </div>
        <div class="col-md-4">
          <select id="category" class="form-select">
            <option value="">All Categories</option>
            <?php $cat_res = mysqli_query($conn, "SELECT DISTINCT category FROM items"); while($c = mysqli_fetch_assoc($cat_res)) { ?>
              <option value="<?php echo htmlspecialchars($c['category']); ?>"><?php echo htmlspecialchars($c['category']); ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-2 form-check">
          <input class="form-check-input" type="checkbox" id="low_stock">
          <label class="form-check-label" for="low_stock">Low Stock</label>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title" style="color: #fff;">Stock items</h3>
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th><th>Name</th><th>Category</th><th>Qty</th><th>Price</th>
            <th>Supplier</th><th>Date Added</th><th>Last Modified</th><th>Action</th>
          </tr>
        </thead>
        <tbody id="inventory-body"></tbody>
      </table>

      <h5 class="mt-4 mb-3" style="color: #fff;">Action Log (most recent 10) <a href="audit_log.php" class="float-end btn btn-sm btn-outline-primary">View Full Audit Log →</a></h5>
      <div class="table-responsive">
        <table class="table table-sm table-hover">
          <thead class="table-light">
            <tr><th>Time</th><th>Type</th><th>Item</th><th>Details</th></tr>
          </thead>
          <tbody>
        <?php
          $logres = mysqli_query($conn, "SELECT a.*, i.item_name FROM actions a LEFT JOIN items i ON a.item_id = i.item_id ORDER BY a.action_date DESC LIMIT 10");
          if (!$logres) {
            echo '<tr><td colspan="4" class="text-danger">Error loading logs</td></tr>';
          } else {
            if (mysqli_num_rows($logres) == 0) {
              echo '<tr><td colspan="4" class="text-muted">No logs yet</td></tr>';
            } else {
              while ($l = mysqli_fetch_assoc($logres)) {
                $type_badge = '';
                if ($l['action_type'] === 'ADD') $type_badge = 'bg-info';
                elseif ($l['action_type'] === 'UPDATE') $type_badge = 'bg-warning text-white';
                elseif ($l['action_type'] === 'SALE') $type_badge = 'bg-danger';
                elseif ($l['action_type'] === 'PURCHASE') $type_badge = 'bg-success';
                else $type_badge = 'bg-secondary';
                
                $iname = $l['item_name'] ? htmlspecialchars($l['item_name']) : '—';
                $when = date('Y-m-d H:i:s', strtotime($l['action_date']));
                echo '<tr>';
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

<script src="ajax.js"></script>
</body>
</html>