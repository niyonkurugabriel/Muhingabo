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
  <title>MUHINGABO - Purchase Item</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">

      <h3 style="color: #fff;">Purchase (Stock In)</h3>

      <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ok'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Purchase recorded!</strong> Stock has been updated.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <form id="purchaseForm" action="save_purchase.php" method="POST" class="row g-3">

        <div class="col-md-6">
          <label class="form-label">Item</label>
          <select id="pItemSelect" name="dummy_item" class="form-select" required>
            <option value="">-- choose item --</option>
            <?php 
              $res = mysqli_query($conn, "SELECT item_id, item_name, quantity, price FROM items ORDER BY item_name");
              while($r = mysqli_fetch_assoc($res)) { 
            ?>
              <option 
                value="<?php echo $r['item_id']; ?>" 
                data-stock="<?php echo $r['quantity']; ?>" 
                data-price="<?php echo $r['price']; ?>"
              >
                <?php echo htmlspecialchars($r['item_name']).' ('.$r['quantity'].' in stock)'; ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Quantity</label>
          <input type="number" id="pItemQty" class="form-control" min="1" value="1" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Price (per unit)</label>
          <input type="number" step="0.01" id="pItemPrice" class="form-control" required>
        </div>

        <div class="col-12">
          <button type="button" id="pAddToCartBtn" class="btn btn-secondary">Add to Cart</button>
        </div>

        <div class="col-12 mt-3">
          <h5 class="text-white">Cart</h5>

          <div class="card bg-light text-dark">
            <div class="card-body p-2">

              <div id="pCartEmpty" class="text-muted">No items in cart.</div>

              <table id="pCartTable" class="table table-sm table-hover d-none mb-0">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th id="pCartTotal">FRW 0.00</th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>

            </div>
          </div>
        </div>

        <div class="col-12">
          <button type="submit" id="pCheckoutBtn" class="btn btn-success" disabled>
            Record Purchase
          </button>
        </div>
      </form>


      <!-- ============================= -->
      <!-- FIXED JAVASCRIPT CART LOGIC -->
      <!-- ============================= -->

      <script>
        const pCart = [];

        function formatCurrency(n) {
          return 'FRW ' + Number(n).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          });
        }

        function pRenderCart() {
          const table = document.getElementById('pCartTable');
          const tbody = table.querySelector('tbody');

          tbody.innerHTML = '';

          if (pCart.length === 0) {
            document.getElementById('pCartEmpty').classList.remove('d-none');
            table.classList.add('d-none');
            document.getElementById('pCheckoutBtn').disabled = true;
            return;
          }

          document.getElementById('pCartEmpty').classList.add('d-none');
          table.classList.remove('d-none');

          let total = 0;

          pCart.forEach((it, idx) => {
            const itemTotal = it.qty * it.price;
            total += itemTotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${it.name}</td>
              <td>${it.qty}</td>
              <td>${formatCurrency(it.price)}</td>
              <td>${formatCurrency(itemTotal)}</td>
              <td><button type="button" class="btn btn-sm btn-link text-danger" onclick="pRemoveFromCart(${idx})">Remove</button></td>
            `;
            tbody.appendChild(tr);
          });

          document.getElementById('pCartTotal').innerText = formatCurrency(total);
          document.getElementById('pCheckoutBtn').disabled = false;
        }

        function pRemoveFromCart(i) {
          pCart.splice(i, 1);
          pRenderCart();
        }

        document.getElementById('pAddToCartBtn').addEventListener('click', function () {

          const sel = document.getElementById('pItemSelect');
          const itemId = sel.value;

          if (!itemId) return alert('Please choose an item');

          const itemName = sel.options[sel.selectedIndex].text.split(' (')[0];
          const qty = parseInt(document.getElementById('pItemQty').value || '0', 10);
          const price = parseFloat(document.getElementById('pItemPrice').value);

          if (qty <= 0) return alert('Quantity must be at least 1');
          if (isNaN(price) || price <= 0) return alert('Enter a valid price per unit');

          // Merge duplicate items
          let exist = pCart.find(it => it.id === itemId);

          if (exist) {
            exist.qty += qty;
            exist.price = price;
          } else {
            pCart.push({ id: itemId, name: itemName, qty: qty, price: price });
          }

          pRenderCart();
        });

        document.getElementById('purchaseForm').addEventListener('submit', function (e) {

          if (pCart.length === 0) {
            e.preventDefault();
            return alert('Cart is empty');
          }

          // Clean old hidden inputs
          document.querySelectorAll("input[name='item_id[]'], input[name='quantity[]'], input[name='price[]']")
            .forEach(e => e.remove());

          // Add new hidden inputs
          pCart.forEach(it => {
            let idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'item_id[]';
            idInput.value = it.id;

            let qtyInput = document.createElement('input');
            qtyInput.type = 'hidden';
            qtyInput.name = 'quantity[]';
            qtyInput.value = it.qty;

            let priceInput = document.createElement('input');
            priceInput.type = 'hidden';
            priceInput.name = 'price[]';
            priceInput.value = it.price;

            purchaseForm.appendChild(idInput);
            purchaseForm.appendChild(qtyInput);
            purchaseForm.appendChild(priceInput);
          });
        });
      </script>

    </div>
  </div>
</div>

</body>
</html>
