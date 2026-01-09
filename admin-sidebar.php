<aside class="admin-sidebar" style="
    width: 260px;
    background: #111827;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    color: white;
    padding: 2rem 1rem;
    display: flex;
    flex-direction: column;
    z-index: 1000;
">
    <div class="sidebar-brand"
        style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 3rem; padding: 0 1rem;">
        <i class="fa-solid fa-shield-halved" style="font-size: 1.5rem; color: #10b981;"></i>
        <span style="font-weight: 800; font-size: 1.25rem; letter-spacing: -0.5px;">Admin Control</span>
    </div>

    <nav class="sidebar-nav" style="display: flex; flex-direction: column; gap: 0.5rem; flex: 1;">
        <a href="admin-dashboard.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-gauge-high"></i> Overview
        </a>
        <a href="admin-users.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-users.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i> Users Management
        </a>
        <a href="admin-shop-approvals.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-shop-approvals.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-store"></i> Shop Approvals
        </a>
        <a href="admin-shops.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-shops.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-shop"></i> Managed Shops
        </a>
        <a href="admin-adoptions.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-adoptions.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-heart"></i> Adoptions
        </a>
        <a href="admin-adoption-approvals.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-adoption-approvals.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-check-double"></i> Listing Approvals
        </a>
        <a href="admin-platform-orders.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-platform-orders.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-invoice-dollar"></i> Platform Revenue
        </a>
        <a href="admin-notifications.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-notifications.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-bullhorn"></i> Announcements
        </a>
        <a href="admin-settings.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-settings.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-gears"></i> System Settings
        </a>
        <a href="admin-logs.php"
            class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin-logs.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-list-check"></i> Audit Logs
        </a>
    </nav>

    <div class="sidebar-footer"
        style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem; margin-top: auto;">
        <a href="admin-logout.php" class="nav-item" style="color: #f87171;">
            <i class="fa-solid fa-right-from-bracket"></i> Sign Out
        </a>
    </div>
</aside>

<style>
    .nav-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 1.25rem;
        color: #9ca3af;
        text-decoration: none;
        border-radius: 0.75rem;
        font-weight: 500;
        font-size: 0.9375rem;
        transition: all 0.2s;
    }

    .nav-item i {
        width: 20px;
        text-align: center;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: white;
    }

    .nav-item.active {
        background: #10b981;
        color: white;
    }
</style>