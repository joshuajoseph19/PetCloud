<!-- shop-sidebar.php -->
<aside class="sidebar"
    style="width: 280px; background: #fff; border-right: 1px solid #e5e7eb; height: 100vh; position: fixed; left: 0; top: 0; display: flex; flex-direction: column; z-index: 1000;">
    <div class="sidebar-brand"
        style="padding: 0.5rem 1.5rem 0; display: flex; align-items: flex-start; border-bottom: 1px solid #f3f4f6; margin-bottom: 0;">
        <img src="images/logo.png" alt="PetCloud Logo" style="width: 180px; height: auto; object-fit: contain;">
    </div>

    <nav class="sidebar-nav" style="flex: 1; padding: 1.5rem 1rem; overflow-y: auto;">
        <a href="shopowner-dashboard.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shopowner-dashboard.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
        <a href="shop-products.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shop-products.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-boxes-stacked"></i> Products
        </a>
        <a href="shop-pet-rehoming.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shop-pet-rehoming.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-house-chimney-user"></i> Pet Rehoming
        </a>
        <a href="shop-orders.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shop-orders.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-truck-fast"></i> Orders
            <?php
            // Minor badge for pending orders
            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT o.id) FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id WHERE p.shop_id = ? AND o.status = 'Pending'");
            $stmt->execute([$shop_id ?? 0]);
            $pendingCount = $stmt->fetchColumn();
            if ($pendingCount > 0)
                echo "<span class='nav-badge' style='background: #ef4444;'>$pendingCount</span>";
            ?>
        </a>
        <a href="shop-customers.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shop-customers.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i> Customers
        </a>
        <a href="shop-reviews.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shop-reviews.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-star"></i> Reviews
        </a>
        <a href="shop-reports.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shop-reports.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-invoice"></i> Reports
        </a>
        <a href="shop-notifications.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shop-notifications.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-bell"></i> Notifications
        </a>
    </nav>

    <div class="sidebar-footer" style="padding: 1.5rem; border-top: 1px solid #f3f4f6;">
        <a href="shop-settings.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shop-settings.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-gear"></i> Settings
        </a>
        <a href="logout.php" class="nav-item" style="color: #ef4444;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</aside>

<style>
    .nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        color: #4b5563;
        text-decoration: none;
        border-radius: 0.75rem;
        font-weight: 500;
        transition: 0.2s;
        margin-bottom: 0.25rem;
        position: relative;
    }

    .nav-item:hover {
        background: #f9fafb;
        color: #4f46e5;
    }

    .nav-item.active {
        background: #eef2ff;
        color: #4f46e5;
    }

    .nav-item i {
        width: 20px;
        text-align: center;
    }

    .nav-badge {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        padding: 0.15rem 0.5rem;
        border-radius: 2rem;
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
    }
</style>