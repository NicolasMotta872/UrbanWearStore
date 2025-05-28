<header class="admin-header">
    <div class="admin-header-left">
        <button id="sidebarToggle" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <div class="admin-header-right">
        <div class="admin-user">
            <div class="admin-user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="admin-user-info">
                <span><?= $_SESSION['admin_username'] ?></span>
                <div class="admin-dropdown">
                    <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
                    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<?php if (isset($_SESSION['success_message'])): ?>
<div class="alert alert-success" id="successAlert">
    <?= $_SESSION['success_message'] ?>
    <button class="close-alert">&times;</button>
</div>
<?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
<div class="alert alert-danger" id="errorAlert">
    <?= $_SESSION['error_message'] ?>
    <button class="close-alert">&times;</button>
</div>
<?php unset($_SESSION['error_message']); ?>
<?php endif; ?>