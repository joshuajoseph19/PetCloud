<?php
// shop-header.php
?>
<header class="top-header"
    style="height: 70px; background: #fff; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; position: sticky; top: 0; z-index: 999; margin-left: 280px;">
    <div class="search-bar">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="shop-search" placeholder="Search orders, products or customers...">
    </div>

    <div class="header-actions" style="display: flex; align-items: center; gap: 1.5rem;">
        <div class="icon-btn" style="position: relative; cursor: pointer; color: #4b5563;">
            <i class="fa-regular fa-bell" style="font-size: 1.25rem;"></i>
            <?php
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM shop_notifications WHERE shop_id = ? AND is_read = 0");
            $stmt->execute([$shop_id ?? 0]);
            $notifCount = $stmt->fetchColumn();
            if ($notifCount > 0): ?>
                <span
                    style="position: absolute; top: -5px; right: -5px; background: #ef4444; width: 16px; height: 16px; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 700; border: 2px solid #fff;">
                    <?php echo $notifCount; ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="user-profile"
            style="display: flex; align-items: center; gap: 0.75rem; padding-left: 1.5rem; border-left: 1px solid #f3f4f6;">
            <div style="text-align: right;">
                <div style="font-size: 0.9rem; font-weight: 600; color: #111827;">
                    <?php echo htmlspecialchars($user_name); ?>
                </div>
                <div style="font-size: 0.75rem; color: #6b7280;">
                    <?php echo htmlspecialchars($shopName); ?>
                </div>
            </div>
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=4f46e5&color=fff"
                style="width: 40px; height: 40px; border-radius: 50%;">
        </div>
    </div>
</header>