<nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?page=dashboard">Hardware Shop</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if ($_SESSION['role'] === 'Admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=inventory">Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=purchases">Purchases</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=purchase_returns">Purchase Returns</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=reports">Reports</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] === 'Cashier'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=sales">Sales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=sales_returns">Sales Returns</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <span class="navbar-text">
            Logged in as <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)
            <a href="index.php?page=logout" class="btn btn-outline-danger btn-sm">Logout</a>
        </span>
    </div>
</nav>
