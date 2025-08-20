<?php

$faucetName = Core::sanitizeOutput($config->get('faucet_name'));  // ✅ XSS védelem

// Beállítások lekérése OOP módon
$offerwalls_status = $config->get('offerwalls_status');
$claimStatus = $config->get('claim_enabled');
$ptcStatus = $config->get('zeradsptc_status');
$zerads_id = Core::sanitizeOutput($config->get('zerads_id'));
$shortlink_status = $config->get('shortlink_status');
$achievements_status = $config->get('achievements_status');
$dailybonus_status = $config->get('dailybonus_status');
$autofaucet_status = $config->get('autofaucet_status');
$energyshop_status = $config->get('energyshop_status');
$bitcotasks_ptc_status = $config->get('bitcotasks_ptc_status');
$bitcotasks_shortlink_status = $config->get('bitcotasks_shortlink_status');
$depositStatus = $config->get('deposit_status');
$ptc_status = $config->get('ptc_status');

$userId = '';
if (isset($user) && is_object($user) && method_exists($user, 'getUserData')) {
    $userId = Core::sanitizeOutput($user->getUserData('id'));
}
$websiteUrl = Core::sanitizeOutput($config->get('website_url'));
$pageTitle = Core::sanitizeOutput($pageTitle);

$pages = [
    'autofaucet' => ['status' => $autofaucet_status, 'icon' => 'bi-arrow-repeat', 'name' => 'AutoFaucet'],
    'energyshop' => ['status' => $energyshop_status, 'icon' => 'bi-shop', 'name' => 'EnergyShop'],
    'achievements' => ['status' => $achievements_status, 'icon' => 'bi-trophy', 'name' => 'Achievements'],
    'daily_bonus' => ['status' => $dailybonus_status, 'icon' => 'bi-calendar-check', 'name' => 'Daily Bonus'],
    'faucet' => ['status' => $claimStatus, 'icon' => 'bi-droplet', 'name' => 'Faucet'],
    'offerwalls' => ['status' => $offerwalls_status, 'icon' => 'bi-cash-stack', 'name' => 'Offerwalls'],
    'zeradsptc' => ['status' => $ptcStatus, 'icon' => 'bi-megaphone', 'name' => 'PTC (Zerads)', 'external' => true, 'url' => "https://zerads.com/ptc.php?ref=$zerads_id&user=$userId"],
    'ptc' => ['status' => $ptc_status, 'icon' => 'bi-cash-stack', 'name' => 'PTC #1'],
    'bitcotasks_ptc' => ['status' => $bitcotasks_ptc_status, 'icon' => 'bi-cash-stack', 'name' => 'PTC #2'],
    'shortlink' => ['status' => $shortlink_status, 'icon' => 'bi-link-45deg', 'name' => 'Shortlinks #1'],
    'bitcotasks_shortlinks' => ['status' => $bitcotasks_shortlink_status, 'icon' => 'bi-link', 'name' => 'Shortlinks #2']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
	 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="<?= $websiteUrl ?>favicon.png" type="image/png">

<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap');
/* Base Styles */
:root {
  --sidebar-width: 250px;
  --header-height: 56px;
  --primary-color: #d0ff00;
  --secondary-color: #00ff80;
  --bg-dark: #1a1a2e;
  --bg-darker: #16213e;
  --text-light: #eaffd0;
  --text-muted: rgba(234, 255, 208, 0.7);
  --transition-speed: 0.3s;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  font-size: 16px;
  height: 100%;
  scroll-behavior: smooth;
}

body {
  background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-darker) 100%);
  color: var(--text-light);
  font-family: 'Orbitron', 'Segoe UI', Arial, sans-serif;
  min-height: 100vh;
  margin: 0;
  padding: 0;
  line-height: 1.6;
  overflow-x: hidden;
  position: relative;
  padding-top: var(--header-height);
}

/* Make sure all buttons are tappable on mobile */
button, .btn, [role="button"], input[type="submit"], input[type="button"] {
  min-height: 44px; /* Minimum touch target size */
  min-width: 44px;
}

a, button, .btn {
  -webkit-tap-highlight-color: transparent;
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
/* Main container */
.container {
  width: 100%;
  padding-right: 15px;
  padding-left: 15px;
  margin-right: auto;
  margin-left: auto;
  max-width: 1140px;
}

/* Cards and content blocks */
.cyberpunk-container, .card, .content-block {
  background: linear-gradient(135deg, rgba(200,255,0,0.07) 0%, rgba(0,255,128,0.13) 100%);
  border: 2px solid #d0ff00;
  border-radius: 18px;
  box-shadow: 0 0 30px 0 #d0ff00aa, 0 0 10px 2px #00ff80cc;
  padding: 2rem;
  margin-bottom: 2rem;
  width: 100%;
  box-sizing: border-box;
}

/* Grid layout for cards */
.row {
  display: flex;
  flex-wrap: wrap;
  margin-right: -15px;
  margin-left: -15px;
}

.col {
  flex: 1 0 0%;
  padding: 0 15px;
  margin-bottom: 2rem;
}

/* Responsive grid */
@media (min-width: 768px) {
  .col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
  }
  
  .col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
  }
  
  .col-md-3 {
    flex: 0 0 25%;
    max-width: 25%;
  }
}

/* Ensure main content has proper spacing */
main {
  padding: 1.5rem 0;
  width: 100%;
  box-sizing: border-box;
}
h1, h2, h3, h4 {
  color: #d0ff00;
  text-shadow: 0 0 8px #00ff80, 0 0 2px #d0ff00;
  letter-spacing: 2px;
}
a, .cyberpunk-link {
  color: #d0ff00;
  text-decoration: none;
  border-bottom: 1px dashed #00ff80;
  transition: color 0.2s;
}
a:hover, .cyberpunk-link:hover {
  color: #00ff80;
  border-bottom: 1px solid #d0ff00;
  text-shadow: 0 0 8px #d0ff00;
}
button, .cyberpunk-btn {
  background: linear-gradient(90deg, #d0ff00 0%, #00ff80 100%);
  color: #16213e;
  border: none;
  border-radius: 8px;
  box-shadow: 0 0 10px #d0ff00, 0 0 4px #00ff80;
  font-family: 'Orbitron', 'Segoe UI', Arial, sans-serif;
  font-weight: bold;
  padding: 0.75rem 2rem;
  margin: 1rem 0;
  cursor: pointer;
  transition: background 0.3s, color 0.2s, box-shadow 0.2s;
}
button:hover, .cyberpunk-btn:hover {
  background: linear-gradient(90deg, #00ff80 0%, #d0ff00 100%);
  color: #1a1a2e;
  box-shadow: 0 0 20px #d0ff00, 0 0 8px #00ff80;
}
input, textarea, select {
  background: #22223b;
  color: #eaffd0;
  border: 1.5px solid #00ff80;
  border-radius: 6px;
  padding: 0.5rem;
  margin: 0.5rem 0;
  outline: none;
  box-shadow: 0 0 8px #00ff8033;
  transition: border 0.2s, box-shadow 0.2s;
}
input:focus, textarea:focus, select:focus {
  border: 1.5px solid #d0ff00;
  box-shadow: 0 0 12px #d0ff00bb;
}


/* Breadcrumb Stílus - Középre igazítva és fix szélességben */
.breadcrumb-container {
    max-width: 100%; /* Ne lógjon ki az oldalról */
    padding-left: 15px; /* Sidebar miatt bal oldalon térköz */
    padding-right: 15px; /* Ne érjen ki a képernyő szélére */
}

/* Breadcrumb megjelenés */
.breadcrumb {
    background: rgba(240, 240, 240, 0.8); /* Lágy szürke háttér */
    border-radius: 8px; /* Lekerekített sarkok */
    padding: 10px 15px; /* Szellős padding */
    font-size: 14px; /* Szövegméret */
    font-weight: 500;
    display: flex;
    align-items: center;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); /* Finom árnyék */
    max-width: 100%; /* Ne lépje túl a tartalom szélességét */
    overflow-x: auto; /* Ha szükséges, görgethető legyen */
}

/* Breadcrumb elemek */
.breadcrumb-item {
    color: #555; /* Lágy sötétszürke szín */
}

.breadcrumb-item.active {
    font-weight: 600;
    color: #333; /* Sötétebb kiemelt szöveg */
}

/* Elválasztó */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›"; /* Modern nyíl az elválasztáshoz */
    color: #777;
    padding: 0 8px;
}

/* Sidebar */
.sidebar {
  width: var(--sidebar-width);
  height: 100vh;
  position: fixed;
  top: 0;
  left: -100%;
  background: #2c3034;
  color: white;
  transition: transform var(--transition-speed) ease-in-out, left var(--transition-speed) ease-in-out;
  z-index: 1050;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
  padding-top: var(--header-height);
}

.sidebar.active {
  left: 0;
  transform: translateX(0);
}

/* Overlay for mobile when sidebar is open */
.sidebar-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1040;
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
}

.sidebar-overlay.active {
  display: block;
  opacity: 1;
}

/* Main Content */
.content {
  transition: margin-left var(--transition-speed) ease-in-out, padding var(--transition-speed) ease-in-out;
  min-height: calc(100vh - var(--header-height));
  width: 100%;
  padding: 1.5rem;
  position: relative;
  background: transparent;
}

/* Sidebar fejléc (oldalnév) */
.sidebar-header {
    padding: 20px 15px;
    text-align: center;
    background: #343a40;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

/* Cím stílus és animáció */
.sidebar-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #ffffff;
    display: inline-block;
    transition: transform 0.3s ease, color 0.3s ease;
}

.sidebar-title:hover {
    transform: scale(1.1);
    color: #f8f9fa;
}

/* Menü elválasztó vonal */
.sidebar hr {
    margin: 10px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

/* Menü elemek */
.sidebar .nav-link {
    padding: 5px 15px;
    font-size: 15px;
    color: rgba(255, 255, 255, 0.75);
    display: flex;
    align-items: center;
}

.sidebar .nav-link i {
    margin-right: 10px;
}

.sidebar .nav-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
}

/* Responsive Breakpoints */
/* Small devices (landscape phones, 576px and up) */
@media (min-width: 576px) {
  .container {
    max-width: 540px;
  }
  
  .card, .content-block {
    padding: 1.5rem;
  }
}

/* Medium devices (tablets, 768px and up) */
@media (min-width: 768px) {
  .container {
    max-width: 720px;
  }
  
  .card, .content-block {
    padding: 2rem;
  }
  
  .sidebar {
    transform: translateX(-100%);
    left: 0;
  }
  
  .content {
    margin-left: 0;
    padding: 2rem;
  }
  
  .sidebar.active + .content {
    margin-left: var(--sidebar-width);
  }
}

/* Large devices (desktops, 992px and up) */
@media (min-width: 992px) {
  .container {
    max-width: 960px;
  }
  
  .sidebar {
    left: 0;
    transform: translateX(0);
  }
  
  .content {
    margin-left: var(--sidebar-width);
    padding: 2.5rem;
  }
  
  /* Hide mobile toggle on desktop */
  #sidebarToggle {
    display: none;
  }
  
  .sidebar-overlay {
    display: none !important;
  }
}

/* Extra large devices (large desktops, 1200px and up) */
@media (min-width: 1200px) {
  .container {
    max-width: 1140px;
  }
}

.footer {
background: #343a40;
}

/* Mobile Header */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1030;
  background: rgba(33, 37, 41, 0.95);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  height: var(--header-height);
  display: flex;
  align-items: center;
  padding: 0 1rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

/* Sidebar Toggle Button */
#sidebarToggle {
  background: transparent;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
  margin-right: 1rem;
  z-index: 1100;
  transition: transform 0.2s ease;
}

#sidebarToggle:active {
  transform: scale(0.9);
}

/* Site Title in Header */
.navbar-brand {
  color: white;
  font-weight: bold;
  font-size: 1.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-right: auto;
}

/* Make sure content doesn't hide under fixed header */
main {
  padding-top: 1rem;
}

</style>

</head>

<body>

<!-- Mobile Header with Sidebar Toggle -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" id="sidebarToggle">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand mx-auto" href="<?= rtrim($websiteUrl, '/') ?>">
        <?= $faucetName ?>
    </a>
    <div class="d-flex align-items-center">
        <?php if (isset($user) && is_object($user) && $user->getUserData('id')): ?>
            <span class="badge bg-success me-2 d-none d-md-inline">
                <i class="bi bi-coin"></i> 
                <span id="userBalance"><?= number_format($user->getUserData('balance'), 8) ?></span>
            </span>
        <?php endif; ?>
    </div>
</nav>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay"></div>

<!-- Sidebar -->
<?php if (isset($user) && is_object($user) && $user->getUserData('id')): ?>
<nav class="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <span class="sidebar-title"><?= $faucetName ?></span>
        <?php if (isset($user) && is_object($user) && $user->getUserData('id')): ?>
            <div class="user-balance mt-2">
                <span class="badge bg-success">
                    <i class="bi bi-coin"></i> 
                    <span id="sidebarBalance"><?= number_format($user->getUserData('balance'), 8) ?></span>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Menü -->
<!-- Menü -->
<ul class="nav flex-column p-3">
    <hr>
    <li class="nav-item">
        <a class="nav-link" href="<?= rtrim($websiteUrl, '/') . '/dashboard' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= rtrim($websiteUrl, '/') . '/referral' ?>">
            <i class="bi bi-people"></i> Referral
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= rtrim($websiteUrl, '/') . '/withdraw' ?>">
            <i class="bi bi-wallet2"></i> Withdraw
        </a>
    </li>
    <?php if ($depositStatus === "on"): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?= rtrim($websiteUrl, '/') . '/deposit' ?>">
            <i class="bi bi-wallet2"></i> Deposit
        </a>
    </li>
    <?php endif; ?>
    <hr>
    <?php foreach ($pages as $page => $data) {
        if ($data['status'] == "on") { ?>
            <li class="nav-item">
                <a class="nav-link"
   href="<?= ($data['external'] ?? false) ? $data['url'] : rtrim($websiteUrl, '/') . '/' . ltrim($page, '/') ?>"
   <?= ($data['external'] ?? false) ? 'target="_blank"' : '' ?>>

                    <i class="bi <?= $data['icon'] ?>"></i> <?= $data['name'] ?>
                </a>
            </li>
    <?php } } ?>
    <hr>
    <li class="nav-item">
        <a class="nav-link" href="<?= rtrim($websiteUrl, '/') . '/advertise' ?>">
            <i class="bi bi-megaphone"></i> Advertise
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= rtrim($websiteUrl, '/') . '/settings' ?>">
            <i class="bi bi-gear"></i> Settings
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-danger" href="<?= rtrim($websiteUrl, '/') . '/logout' ?>">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </li>
</ul>

</nav>
<?php endif; ?>

<!-- Main Content -->
<main class="content">
    <div class="container-fluid">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent px-0">
        <li class="breadcrumb-item active" aria-current="page"><?= $pageTitle ?></li>
    </ol>
</nav>

      <section>




