<?php
// Session handling for navbar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_in = isset($_SESSION['username']);
$current_user = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
  <div class="container-fluid">
    <div class="d-flex gap-2 align-items-center">
      <a class="navbar-brand fw-bold" href="index.php">
        <span style="font-size: 1.5rem;">🌸</span> MUHINGABO STOCK
      </a>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">
            Home
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo in_array($current_page, ['view_items.php','add_item.php']) ? 'active' : ''; ?>" href="#" id="invMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">Stock</a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="invMenu">
            <li><a class="dropdown-item" href="view_items.php">View Stock</a></li>
            <li><a class="dropdown-item" href="add_item.php">Add Item</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo in_array($current_page, ['sell_item.php','purchase_item.php','sales_dashboard.php','purchase_dashboard.php','audit_log.php','daily_report.php']) ? 'active' : ''; ?>" href="#" id="flowMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">Flow</a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="flowMenu">
            <li><a class="dropdown-item" href="sales_dashboard.php">Sales History</a></li>
            <li><a class="dropdown-item" href="purchase_dashboard.php">Purchase History</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="audit_log.php">📋 Audit Log</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link daily-report-btn <?php echo $current_page === 'daily_report.php' ? 'active' : ''; ?>" href="daily_report.php">📊 Daily Report</a>
        </li>
        
        <!-- User Menu -->
        <?php if ($is_logged_in): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            👤 <?php echo htmlspecialchars($current_user); ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
            <li><a class="dropdown-item" href="#" onclick="return false;">
              <small style="color: #999;">Logged in as: <strong><?php echo htmlspecialchars($current_user); ?></strong></small>
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item logout-btn" href="logout.php">
              🚪 Logout
            </a></li>
          </ul>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<style>
.animate-pulse { animation: pulse-glow 2s infinite; }
@keyframes pulse-glow {
  0%, 100% { opacity: 1; box-shadow: 0 0 5px rgba(255,255,255,0.3); }
  50% { opacity: 0.7; box-shadow: 0 0 15px rgba(255,255,255,0.6); }
}
.daily-report-btn {
  background: linear-gradient(90deg,#ffd166,#ff8a00);
  color: #1b1b1b !important;
  border-radius: 0.35rem;
  padding: 0.3rem 0.6rem;
  margin-left: 0.5rem;
  box-shadow: 0 4px 14px rgba(255,138,0,0.18);
  transition: transform .15s ease, box-shadow .15s ease;
}
.daily-report-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 26px rgba(255,138,0,0.24); }
.logout-btn {
  color: #dc3545 !important;
  font-weight: 500;
}
.logout-btn:hover {
  background-color: #f8d7da !important;
}
</style>


