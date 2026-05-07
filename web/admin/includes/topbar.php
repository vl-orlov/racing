<?php
if (!isset($basePath)) {
    include __DIR__ . '/path_helper.php';
    $basePath = getAdminBasePath();
}
?>
<nav class="navbar navbar-expand topbar static-top adm-topbar">

    <div class="adm-topbar-title">
        <span class="adm-eyebrow">Panel de administración</span>
    </div>

    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item">
            <a class="nav-link adm-topbar-link" href="../" target="_blank" title="Ver tienda">
                <i class="fas fa-store fa-sm"></i>
                <span class="d-none d-lg-inline ml-2">Ver tienda</span>
            </a>
        </li>
        <div class="adm-topbar-divider"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link adm-topbar-link dropdown-toggle" href="#"
               id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle fa-sm mr-1"></i>
                <span class="d-none d-lg-inline">Admin</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right adm-dropdown shadow" aria-labelledby="userDropdown">
                <a class="dropdown-item adm-dropdown-item" href="<?= $basePath ?>index.php?page=logout">
                    <i class="fas fa-sign-out-alt fa-sm mr-2"></i> Cerrar sesión
                </a>
            </div>
        </li>
    </ul>

</nav>
