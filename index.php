<?php
require_once 'session_config.php';
require_once 'db_connect.php';

// Handle logout via index (integrated)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
  destroy_session();
  header('Location: index.php');
  exit;
}

// Handle login form submission on index
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_action']) && $_POST['login_action'] === 'login') {
  $u = isset($_POST['username']) ? trim($_POST['username']) : '';
  $p = isset($_POST['password']) ? trim($_POST['password']) : '';
  if ($u === '' || $p === '') {
    $login_error = 'Please enter both username and password.';
  } elseif (validate_credentials($u, $p)) {
    create_session($u);
    header('Location: index.php');
    exit;
  } else {
    $login_error = 'Invalid username or password. Please try again.';
  }
}

// If not logged in, show login UI below; otherwise show dashboard
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MUHINGABO Stock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'navbar.php'; ?>

  <main class="container py-5">
    <?php if (!is_logged_in() || is_session_expired()): ?>
      <div class="d-flex justify-content-center">
        <div style="width:100%;max-width:520px;">
          <div class="card shadow-sm">
            <div class="card-body">
              <h3 class="card-title">Sign In</h3>
              <?php if ($login_error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($login_error); ?></div>
              <?php endif; ?>
              <form method="POST" action="index.php">
                <input type="hidden" name="login_action" value="login">
                <div class="mb-3">
                  <label class="form-label">Username</label>
                  <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars(isset($_POST['username'])?$_POST['username']:'dope'); ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" value="<?php echo htmlspecialchars(isset($_POST['password'])?$_POST['password']:'@1205'); ?>" required>
                </div>
                <button class="btn btn-primary" type="submit">Sign In</button>
              </form>
              <div class="mt-3 small text-muted">Default: <strong>dope</strong> / <strong>@1205</strong></div>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
    <div class="p-4 bg-dark rounded shadow-sm text-center mb-4" style="background: linear-gradient(135deg, #1e1e2e, #2a2a3e) !important; border: 1px solid #333;">
      <h1 class="display-6 mb-3" style="color: #fff;">Welcome to your Stock</h1>
      <p class="lead" style="color: #c0c0c0;">Track items, quantities and see a log of every Add / Update / Delete with timestamps.</p>
      <a class="btn btn-primary btn-lg" href="view_items.php">View Inventory</a>
    </div>

    <!-- Enhanced Flow Action Buttons -->
    <div class="row g-3 mb-5">
      <div class="col-12">
        <div class="d-flex justify-content-center gap-4">
          <a href="sell_item.php" class="btn btn-danger btn-lg flow-action-btn flow-sale" title="Record a Sale">
            💰 Record Sale
          </a>
          <a href="purchase_item.php" class="btn btn-success btn-lg flow-action-btn flow-purchase" title="Record a Purchase">
            📦 Record Purchase
          </a>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <?php
        $items = mysqli_query($conn, "SELECT * FROM items ORDER BY item_id DESC LIMIT 12");
        while($item = mysqli_fetch_assoc($items)) {
      ?>
      <div class="col-md-3 col-sm-6">
        <div class="card item-tile h-100 shadow-sm border-0" tabindex="0">
          <div class="card-body text-center">
            <?php if (!empty($item['image'])): ?>
              <img src="<?php echo htmlspecialchars($item['image']); ?>" class="item-image mb-2" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
            <?php endif; ?>
            <h5 class="card-title mb-2"><?php echo htmlspecialchars($item['item_name']); ?></h5>
            <span class="badge bg-primary mb-2">Qty: <?php echo $item['quantity']; ?></span>
            <div class="item-details mt-2" style="display:none;">
              <div class="text-start small">
                <div><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></div>
                <div><strong>Price:</strong> <?php echo currency($item['price']); ?></div>
                <div><strong>Supplier:</strong> <?php echo htmlspecialchars($item['supplier']); ?></div>
                <div><strong>Date Added:</strong> <?php echo htmlspecialchars($item['date_added']); ?></div>
                <div><strong>Last Modified:</strong> <?php echo htmlspecialchars($item['last_modified']); ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.item-tile').forEach(function(tile) {
          tile.addEventListener('mouseenter', function() {
            this.querySelector('.item-details').style.display = 'block';
          });
          tile.addEventListener('mouseleave', function() {
            this.querySelector('.item-details').style.display = 'none';
          });
          tile.addEventListener('focus', function() {
            this.querySelector('.item-details').style.display = 'block';
          });
          tile.addEventListener('blur', function() {
            this.querySelector('.item-details').style.display = 'none';
          });
        });
      });
    </script>
    <?php endif; ?>
  </main>

  <footer class="text-center py-3 small">
    Developed by <span class="heart"></span> — NIYONKURU Gabriel
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
