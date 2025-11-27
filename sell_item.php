<?php 
require_once 'session_config.php';
include 'db_connect.php';

// Require login
require_login();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MUHINGABO - Sell Item</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 style="color: #fff;">Sell Item</h3>
      <?php if (isset($_GET['error']) && $_GET['error'] === 'stock'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Insufficient stock!</strong> Not enough quantity available.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php elseif (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Invalid item!</strong> Please select a valid item.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      <form id="saleForm" action="save_sale.php" method="POST" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Item</label>
          <select id="itemSelect" class="form-select" required>
            <option value="">-- choose item --</option>
            <?php $res = mysqli_query($conn, "SELECT item_id, item_name, quantity, price FROM items ORDER BY item_name"); while($r = mysqli_fetch_assoc($res)) { ?>
              <option value="<?php echo $r['item_id']; ?>" data-stock="<?php echo $r['quantity']; ?>" data-price="<?php echo $r['price']; ?>"><?php echo htmlspecialchars($r['item_name']).' ('.$r['quantity'].' in stock)'; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Quantity</label>
          <input type="number" id="itemQty" class="form-control" min="1" value="1">
        </div>
        <div class="col-md-3">
          <label class="form-label">Price (per unit)</label>
          <input type="number" step="0.01" id="itemPrice" class="form-control" required>
        </div>
        <div class="col-12">
          <button type="button" id="addToCartBtn" class="btn btn-secondary">Add to Cart</button>
        </div>

        <div class="col-12 mt-3">
          <h5 class="text-white">Cart</h5>
          <div class="card bg-light text-dark">
            <div class="card-body p-2">
              <div id="cartEmpty" class="text-muted">No items in cart.</div>
              <table id="cartTable" class="table table-sm table-hover d-none mb-0">
                <thead>
                  <tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr><th colspan="3" class="text-end">Total</th><th id="cartTotal">FRW 0.00</th><th></th></tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <div class="col-12">
          <button type="submit" id="checkoutBtn" class="btn btn-danger" disabled>Record Sale</button>
        </div>
      </form>

      <script>
        const cart = [];
        function formatCurrency(n){ return '<?php echo "FRW "; ?>' + Number(n).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}); }

        function renderCart(){
          const table = document.getElementById('cartTable');
          const tbody = table.querySelector('tbody');
          tbody.innerHTML = '';
          if(cart.length === 0){
            document.getElementById('cartEmpty').classList.remove('d-none');
            table.classList.add('d-none');
            document.getElementById('checkoutBtn').disabled = true;
            return;
          }
          document.getElementById('cartEmpty').classList.add('d-none');
          table.classList.remove('d-none');
          let total = 0;
          cart.forEach((it, idx) => {
            const tr = document.createElement('tr');
            const itemTotal = it.qty * it.price;
            total += itemTotal;
            tr.innerHTML = `<td>${it.name}</td><td>${it.qty}</td><td>${formatCurrency(it.price)}</td><td>${formatCurrency(itemTotal)}</td><td><button type="button" class="btn btn-sm btn-link text-danger" onclick="removeFromCart(${idx})">Remove</button></td>`;
            tbody.appendChild(tr);
          });
          document.getElementById('cartTotal').innerText = formatCurrency(total);
          document.getElementById('checkoutBtn').disabled = false;
        }

        function removeFromCart(i){ cart.splice(i,1); renderCart(); }

        document.getElementById('addToCartBtn').addEventListener('click', function(){
          const sel = document.getElementById('itemSelect');
          const itemId = sel.value;
          if(!itemId) return alert('Please choose an item');
          const itemName = sel.options[sel.selectedIndex].text.split(' (')[0];
          const stock = parseInt(sel.options[sel.selectedIndex].dataset.stock || '0', 10);
          const qty = parseInt(document.getElementById('itemQty').value || '0', 10);
          const price = parseFloat(document.getElementById('itemPrice').value || sel.options[sel.selectedIndex].dataset.price || '0');
          if(qty <= 0) return alert('Quantity must be at least 1');
          if(qty > stock) return alert('Not enough stock available');
          cart.push({id: itemId, name: itemName, qty: qty, price: price});
          renderCart();
        });

        // on submit, create hidden inputs
        document.getElementById('saleForm').addEventListener('submit', function(e){
          if(cart.length === 0){ e.preventDefault(); return alert('Cart is empty'); }
          // remove any existing dynamic inputs
          document.querySelectorAll('#saleForm input[name^="item_id_"]').forEach(n=>n.remove());
          cart.forEach((it, idx) => {
            const iid = document.createElement('input'); iid.type='hidden'; iid.name = 'item_id[]'; iid.value = it.id; document.getElementById('saleForm').appendChild(iid);
            const iq = document.createElement('input'); iq.type='hidden'; iq.name = 'quantity[]'; iq.value = it.qty; document.getElementById('saleForm').appendChild(iq);
            const ip = document.createElement('input'); ip.type='hidden'; ip.name = 'price[]'; ip.value = it.price; document.getElementById('saleForm').appendChild(ip);
          });
        });
      </script>
    </div>
  </div>
</div>
</body>
</html>