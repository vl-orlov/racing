<?php
if (!isset($basePath)) {
    include __DIR__ . '/path_helper.php';
    $basePath = getAdminBasePath();
}
?>
<ul class="navbar-nav sidebar accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= $basePath ?>index.php">
        <img src="<?= $basePath ?>img/logo.svg" alt="Racing Gaming" class="adm-logo-img">
        <span class="adm-brand-text">Racing<br>Gaming<small>Admin Panel</small></span>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="<?= $basePath ?>index.php">
            <i class="fas fa-fw fa-box-open"></i>
            <span>Productos</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link" href="../" target="_blank">
            <i class="fas fa-fw fa-store"></i>
            <span>Ver tienda</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

</ul>
