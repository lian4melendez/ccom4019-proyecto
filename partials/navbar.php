<div class="nav">
    <?php require_once 'logotype.php'; ?>

    <div class="link-group">
    <a class="<?php if (!isset($_GET['courses']) && !isset($_GET['departments']) && !isset($_GET['users']) && !isset($_GET['terms'])) echo "selected" ?>" href="?">Available Courses</a>
        <?php if (Auth::checkAdmin()) { ?>
            <a class="<?php if (isset($_GET['courses'])) echo "selected" ?>" href="?courses">Manage Courses</a>
            <a class="<?php if (isset($_GET['departments'])) echo "selected" ?>" href="?departments">Departments</a>
            <a class="<?php if (isset($_GET['users'])) echo "selected" ?>" href="?users">Users</a>
            <a class="<?php if (isset($_GET['terms'])) echo "selected" ?>" href="?terms">Terms</a>
            
        <?php } ?>

    </div>

    <div class="auth-profile">
        <?php if (isset($_SESSION['user'])) { ?>
            <a href="index.php?logout"> <i class="las la-sign-out-alt"></i>Logout</a>
        <?php } else { ?>
            <a href="index.php?login"><i class="las la-key"></i>Login</a>
        <?php } ?>
    </div>
</div>