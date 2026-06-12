<?php

session_start();
require_once './includes/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Fetch stats from database
$totalProducts = 0;
$totalUsers    = 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
if ($result) $totalProducts = mysqli_fetch_assoc($result)['total'];

$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
if ($result) $totalUsers = mysqli_fetch_assoc($result)['total'];

// Fetch products for display
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – ShopSmart | BIT3208</title>
    <style>
        :root {
            --primary: #1a3c5e;
            --accent: #e74c3c;
            --success: #27ae60;
            --warning: #f39c12;
            --light: #f0f4f8;
            --border: #e0e0e0;
            --white: #ffffff;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--light); display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            width: 230px;
            background: var(--primary);
            color: white;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .sidebar-logo {
            padding: 25px 20px;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-user {
            padding: 15px 20px;
            font-size: 13px;
            background: rgba(255,255,255,0.08);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-user .role {
            display: inline-block;
            background: var(--accent);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }
        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--accent);
        }
        .sidebar-footer {
            margin-top: auto;
            padding: 15px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-footer a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 13px;
        }
        .sidebar-footer a:hover { color: white; }

        /* MAIN CONTENT */
        .main { flex: 1; display: flex; flex-direction: column; }
        .topbar {
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .topbar h1 { font-size: 1.2rem; color: var(--primary); }
        .session-badge {
            background: var(--light);
            border: 1px solid var(--border);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            color: #555;
        }

        .content { padding: 30px; }

        /* STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            display: flex;
            align-items: center;
            gap: 15px;
            border-top: 4px solid var(--primary);
        }
        .stat-card.green { border-top-color: var(--success); }
        .stat-card.red { border-top-color: var(--accent); }
        .stat-card.orange { border-top-color: var(--warning); }
        .stat-icon { font-size: 2rem; }
        .stat-info .number { font-size: 1.8rem; font-weight: bold; color: var(--primary); }
        .stat-info .label { font-size: 12px; color: #7f8c8d; }

        /* TABLE */
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 1rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light);
        }
        table { width: 100%; border-collapse: collapse; }
        th { background: var(--light); padding: 11px 14px; text-align: left; font-size: 12px; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 12px 14px; border-bottom: 1px solid var(--border); font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-electronics { background: #e3f2fd; color: #1565c0; }
        .badge-accessories { background: #f3e5f5; color: #6a1b9a; }

        /* SESSION INFO */
        .session-info {
            background: #fff9e6;
            border: 1px solid #f39c12;
            border-radius: 8px;
            padding: 15px;
            font-size: 13px;
            color: #7d4800;
        }
        .session-info h4 { margin-bottom: 8px; color: #7d4800; }
        .session-info code { background: rgba(0,0,0,0.07); padding: 1px 5px; border-radius: 3px; }

        /* ADD PRODUCT FORM */
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 5px;
            font-size: 13px;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26, 60, 94, 0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: #0d2843;
        }
        .btn-secondary {
            background: var(--light);
            color: var(--primary);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover {
            background: #e8ecf0;
        }
        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 13px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 500px;
            z-index: 1000;
            padding: 30px;
        }
        .modal.active,
        .modal-backdrop.active {
            display: block;
        }
        .modal-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h2 {
            margin: 0;
            color: var(--primary);
        }
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        .close-btn:hover { color: #333; }

        @media (max-width: 700px) {
            .sidebar { display: none; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-logo">🛒 ShopSmart</div>
    <div class="sidebar-user">
        <div>👤 <?= htmlspecialchars($_SESSION['username']) ?></div>
        <div class="role"><?= htmlspecialchars($_SESSION['role']) ?></div>
    </div>
    <nav>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="#">Products</a>
        <a href="#">Users</a>
        <a href="#">Orders</a>
        <a href="#">Reports</a>
        <a href="#">Settings</a>
    </nav>
    <div class="sidebar-footer">
        <a href="?logout=1">Logout</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <div class="topbar">
        <h1>Dashboard Overview</h1>
    </div>

    <div class="content">

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="number"><?= $totalProducts ?></div>
                    <div class="label">Total Products</div>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-info">
                    <div class="number"><?= $totalUsers ?></div>
                    <div class="label">Registered Users</div>
                </div>
            </div>
            <div class="stat-card red">
                <div class="stat-info">
                    <div class="number">0</div>
                    <div class="label">Orders Today</div>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-info">
                    <div class="number">KES 0</div>
                    <div class="label">Revenue</div>
                </div>
            </div>
        </div>

        <!-- SESSION INFO  -->
        <div class="session-info" style="margin-bottom:20px;">
            <p>User ID: <code><?= $_SESSION['user_id'] ?></code> &nbsp;|&nbsp;
               Username: <code><?= htmlspecialchars($_SESSION['username']) ?></code> &nbsp;|&nbsp;
               Role: <code><?= htmlspecialchars($_SESSION['role']) ?></code> &nbsp;|&nbsp;
               Session ID: <code><?= substr(session_id(), 0, 20) ?>...</code>
            </p>
        </div>

        <!-- PRODUCTS TABLE -->
        <div class="card">
            <div class="card-title">Recent Products </div>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price (KES)</th>
                    <th>Stock</th>
                </tr>
                <?php if ($products && mysqli_num_rows($products) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower($row['category']) ?>">
                                <?= htmlspecialchars($row['category']) ?>
                            </span>
                        </td>
                        <td><?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['stock'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;color:#999;padding:20px;">No products found.</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- ADD PRODUCT BUTTON -->
        <button class="btn btn-primary" id="addProductBtn" style="margin-top: 15px;">+ Add New Product</button>

    </div>
</div>

<!-- MODAL BACKDROP -->
<div class="modal-backdrop" id="modalBackdrop"></div>

<!-- ADD PRODUCT MODAL -->
<div class="modal" id="addProductModal">
    <div class="modal-header">
        <h2>Add New Product</h2>
        <button class="close-btn" id="closeModal">&times;</button>
    </div>
    <div id="modalMessage"></div>
    <form id="addProductForm">
        <div class="form-group">
            <label for="productName">Product Name *</label>
            <input type="text" id="productName" name="name" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="productCategory">Category *</label>
                <select id="productCategory" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Clothing">Clothing</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="productPrice">Price (KES) *</label>
                <input type="number" id="productPrice" name="price" step="0.01" min="0" required>
            </div>
        </div>

        <div class="form-group">
            <label for="productStock">Stock Quantity *</label>
            <input type="number" id="productStock" name="stock" min="0" required>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 25px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Add Product</button>
            <button type="button" class="btn btn-secondary" id="cancelBtn" style="flex: 1;">Cancel</button>
        </div>
    </form>
</div>

<script>
// Modal Management
const modal = document.getElementById('addProductModal');
const backdrop = document.getElementById('modalBackdrop');
const addProductBtn = document.getElementById('addProductBtn');
const closeModal = document.getElementById('closeModal');
const cancelBtn = document.getElementById('cancelBtn');
const addProductForm = document.getElementById('addProductForm');
const modalMessage = document.getElementById('modalMessage');

// Open Modal
addProductBtn.addEventListener('click', () => {
    modal.classList.add('active');
    backdrop.classList.add('active');
    addProductForm.reset();
    modalMessage.innerHTML = '';
});

// Close Modal
const closeModalFunction = () => {
    modal.classList.remove('active');
    backdrop.classList.remove('active');
    modalMessage.innerHTML = '';
};

closeModal.addEventListener('click', closeModalFunction);
cancelBtn.addEventListener('click', closeModalFunction);
backdrop.addEventListener('click', closeModalFunction);

// Handle Form Submission
addProductForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(addProductForm);
    
    try {
        const response = await fetch('./includes/add_product.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            addProductForm.reset();
            
            // Add new product to table in real-time
            addProductToTable(result.product);
            
            // Close modal after 1.5 seconds
            setTimeout(closeModalFunction, 1500);
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        showMessage('Error adding product. Please try again.', 'error');
        console.error('Error:', error);
    }
});

// Show message in modal
function showMessage(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    modalMessage.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
}

// Add product to table in real-time
function addProductToTable(product) {
    const tableBody = document.querySelector('.card:last-of-type table tbody');
    
    // If no tbody exists, create one
    let tbody = tableBody;
    if (!tbody) {
        tbody = document.createElement('tbody');
        document.querySelector('.card:last-of-type table').appendChild(tbody);
    }
    
    // If table shows "No products", remove that message
    const emptyRow = tbody.querySelector('tr:only-child td[colspan="5"]');
    if (emptyRow) {
        tbody.innerHTML = '';
    }
    
    // Create new row
    const newRow = document.createElement('tr');
    const badgeClass = 'badge-' + product.category.toLowerCase();
    newRow.innerHTML = `
        <td>${product.id}</td>
        <td>${escapeHtml(product.name)}</td>
        <td><span class="badge ${badgeClass}">${escapeHtml(product.category)}</span></td>
        <td>${parseFloat(product.price).toFixed(2)}</td>
        <td>${product.stock}</td>
    `;
    
    // Insert at the top of the table
    tbody.insertBefore(newRow, tbody.firstChild);
    
    // Keep only 5 products visible
    while (tbody.children.length > 5) {
        tbody.removeChild(tbody.lastChild);
    }
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

</body>
</html>
