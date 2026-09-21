<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistema Electoral 2026' ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/ele2026/public/css/style.css">
    
    <!-- Chart.js (Loaded only if needed, but included for Dashboard) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php if (!isset($hideLayout) || !$hideLayout): ?>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar glass-panel">
            <div class="sidebar-logo">
                <span>ELE</span>2026
            </div>
            
            <nav>
                <ul class="nav-links">
                    <li><a href="/ele2026/public/dashboard" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">📊 Resultados</a></li>
                    <li><a href="/ele2026/public/digitacion" class="nav-link <?= ($activeMenu ?? '') === 'digitacion' ? 'active' : '' ?>">⌨️ Digitación de Actas</a></li>
                    <!-- Future links -->
                    <li style="margin-top: 2rem;">
                        <a href="#" id="btnLogout" class="nav-link text-danger">🚪 Cerrar Sesión</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <h1 class="header-title"><?= $title ?? 'Panel de Control' ?></h1>
                
                <div class="user-profile">
                    <div class="user-info" style="text-align: right;">
                        <div style="font-weight: 600; font-size: 0.9rem;" id="userNameDisplay">Operador</div>
                        <div class="text-muted" style="font-size: 0.8rem;">En línea</div>
                    </div>
                    <div class="avatar">OP</div>
                </div>
            </header>
            
            <!-- Dynamic View Content -->
            <div class="animate-fade-in">
                <?php require __DIR__ . '/' . $contentView . '.php'; ?>
            </div>
        </main>
    </div>
<?php else: ?>
    <!-- For screens like Login where we don't want the sidebar -->
    <div class="animate-fade-in">
        <?php require __DIR__ . '/' . $contentView . '.php'; ?>
    </div>
<?php endif; ?>

<!-- Core JavaScript -->
<script src="/ele2026/public/js/api.js"></script>
<script src="/ele2026/public/js/app.js"></script>
</body>
</html>
