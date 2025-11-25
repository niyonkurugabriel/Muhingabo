function loadInventory() {
    const search = encodeURIComponent(document.getElementById('search').value || '');
    const category = encodeURIComponent(document.getElementById('category').value || '');
    const low_stock = document.getElementById('low_stock').checked ? 1 : 0;
    fetch(`view_item_ajax.php?search=${search}&category=${category}&low_stock=${low_stock}`)
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById('inventory-body');
        tbody.innerHTML = '';
        data.forEach(item => {
            let row = document.createElement('tr');
            if(item.low_stock) row.classList.add('low-stock');
            row.innerHTML = `<td>${item.item_id}</td>
                             <td>${item.item_name}</td>
                             <td>${item.category}</td>
                             <td>${item.quantity}</td>
                             <td>${item.price}</td>
                             <td>${item.supplier}</td>
                             <td>${item.date_added}</td>
                             <td>${item.last_modified}</td>
                             <td>
                              <a class='btn btn-sm btn-outline-primary me-1' href='update_item.php?id=${item.item_id}'>Edit</a>
                              <a class='btn btn-sm btn-outline-danger' href='delete_item.php?id=${item.item_id}' onclick='return confirm("Delete this item?")'>Delete</a>
                             </td>`;
            tbody.appendChild(row);
        });

        const lowCount = data.filter(i => i.low_stock).length;
        const badge = document.getElementById('lowBadge');
        if (badge) {
            badge.textContent = lowCount;
            badge.style.display = lowCount > 0 ? 'inline-block' : 'none';
        }
    })
    .catch(err => {
        console.error('Failed to load inventory:', err);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    loadInventory();
    
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const lowStockCheckbox = document.getElementById('low_stock');
    const filterForm = document.getElementById('filterForm');
    
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            loadInventory();
        });
    }
    
    if (categorySelect) {
        categorySelect.addEventListener('change', () => {
            loadInventory();
        });
    }
    
    if (lowStockCheckbox) {
        lowStockCheckbox.addEventListener('change', () => {
            loadInventory();
        });
    }
    
    if (filterForm) {
        filterForm.addEventListener('submit', e => {
            e.preventDefault();
            loadInventory();
        });
    }
});