<?php
include "../config.php";

if(!isset($_SESSION['admin_id'])){
header("Location: ../login.php");
exit();
}

$search = "";

if(isset($_GET['search']) && trim($_GET['search']) !== ""){
  $search = trim($_GET['search']);
  $stmt = $conn->prepare("SELECT * FROM events WHERE title LIKE ? ORDER BY event_date ASC");
  $searchTerm = "%" . $search . "%";
  $stmt->bind_param("s", $searchTerm);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
}

$events = [];
while($row = $result->fetch_assoc()) $events[] = $row;

$total    = count($events);
$upcoming = 0; $past = 0;
foreach($events as $e){
  if(strtotime($e['event_date']) >= strtotime('today')) $upcoming++;
  else $past++;
}

$typeConfig = [
  'Holiday'  => ['color' => '#b45309', 'bg' => '#fffbeb', 'border' => 'rgba(180,83,9,0.2)',  'dot' => '#f59e0b'],
  'Exam'     => ['color' => '#9f1239', 'bg' => '#fff1f2', 'border' => 'rgba(159,18,57,0.2)', 'dot' => '#e11d48'],
  'Academic' => ['color' => '#065f46', 'bg' => '#ecfdf5', 'border' => 'rgba(6,95,70,0.2)',   'dot' => '#10b981'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — ADBU Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300;1,400&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg:          #f8f7f4;
    --surface:     #ffffff;
    --surface-2:   #f3f2ef;
    --border:      #e5e3de;
    --border-2:    #d4d1ca;
    --ink:         #18171a;
    --ink-2:       #44424d;
    --ink-3:       #8a8794;
    --accent:      #2d6a4f;
    --accent-2:    #1b4332;
    --accent-pale: #ecfdf5;
    --accent-ring: rgba(16,185,129,0.18);
    --red:         #9f1239;
    --red-pale:    #fff1f2;
    --red-border:  rgba(159,18,57,0.2);
    --amber:       #b45309;
    --blue:        #1d4ed8;
    --blue-pale:   #eff6ff;
    --radius:      12px;
    --radius-sm:   8px;
    --radius-xs:   5px;
    --sidebar-w:   240px;
  }

  html { scroll-behavior: smooth; }

  body {
    min-height: 100vh;
    background: var(--bg);
    font-family: 'DM Sans', sans-serif;
    color: var(--ink);
    -webkit-font-smoothing: antialiased;
    display: flex;
  }

  body::after {
    content: '';
    position: fixed; inset: 0;
    background-image:
      linear-gradient(var(--border) 1px, transparent 1px),
      linear-gradient(90deg, var(--border) 1px, transparent 1px);
    background-size: 40px 40px;
    opacity: 0.25;
    pointer-events: none;
    z-index: 0;
  }


  .sidebar {
    width: var(--sidebar-w);
    flex-shrink: 0;
    background: var(--surface);
    border-right: 1px solid var(--border);
    min-height: 100vh;
    position: sticky; top: 0;
    height: 100vh;
    overflow-y: auto;
    display: flex; flex-direction: column;
    z-index: 50;
    animation: slideRight 0.5s cubic-bezier(0.16,1,0.3,1) both;
  }

  @keyframes slideRight {
    from { opacity: 0; transform: translateX(-12px); }
    to   { opacity: 1; transform: translateX(0); }
  }

  .sidebar-brand {
    padding: 22px 20px 18px;
    border-bottom: 1px solid var(--border);
    display: flex; flex-direction: column; gap: 10px;
  }

  .sidebar-brand img {
    height: 36px; width: auto; object-fit: contain;
    align-self: flex-start;
  }

  .sidebar-brand-name {
    font-family: 'Outfit', sans-serif;
    font-size: 13px; font-weight: 600;
    color: var(--ink); letter-spacing: -0.01em;
    line-height: 1.3;
  }

  .sidebar-brand-sub {
    font-size: 11px; color: var(--ink-3);
    margin-top: 1px;
  }

  .sidebar-nav {
    padding: 16px 12px;
    flex: 1;
    display: flex; flex-direction: column; gap: 2px;
  }

  .nav-section-label {
    font-family: 'Outfit', sans-serif;
    font-size: 10px; font-weight: 600;
    letter-spacing: 0.1em; text-transform: uppercase;
    color: var(--ink-3);
    padding: 8px 8px 4px;
    margin-top: 8px;
  }

  .nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 10px;
    border-radius: var(--radius-sm);
    font-size: 13.5px; font-weight: 500;
    color: var(--ink-2);
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
    cursor: pointer; border: none; background: none; width: 100%;
    text-align: left;
  }

  .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; opacity: 0.7; }

  .nav-item:hover { background: var(--surface-2); color: var(--ink); }
  .nav-item:hover svg { opacity: 1; }

  .nav-item.active {
    background: var(--accent-pale);
    color: var(--accent);
    font-weight: 600;
  }

  .nav-item.active svg { opacity: 1; color: var(--accent); }

  .nav-item.danger { color: var(--red); }
  .nav-item.danger:hover { background: var(--red-pale); }

  .sidebar-footer {
    padding: 14px 12px;
    border-top: 1px solid var(--border);
  }

  .user-chip {
    display: flex; align-items: center; gap: 10px;
    padding: 10px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
  }

  .user-avatar {
    width: 30px; height: 30px;
    background: var(--accent-pale);
    border: 1px solid var(--accent-ring);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-family: 'Outfit', sans-serif;
    font-size: 12px; font-weight: 700;
    color: var(--accent);
  }

  .user-name {
    font-size: 13px; font-weight: 500;
    color: var(--ink); line-height: 1;
    margin-bottom: 2px;
  }

  .user-role {
    font-size: 11px; color: var(--ink-3);
  }


  .main {
    flex: 1; min-width: 0;
    display: flex; flex-direction: column;
    position: relative; z-index: 1;
  }


  .topbar {
    background: rgba(255,255,255,0.85);
    border-bottom: 1px solid var(--border);
    backdrop-filter: blur(12px);
    padding: 0 36px;
    height: 60px;
    display: flex; align-items: center;
    justify-content: space-between; gap: 20px;
    position: sticky; top: 0; z-index: 40;
    animation: fadeDown 0.5s cubic-bezier(0.16,1,0.3,1) both;
  }

  @keyframes fadeDown {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .topbar-left {
    display: flex; flex-direction: column; gap: 1px;
  }

  .topbar-title {
    font-family: 'Outfit', sans-serif;
    font-size: 15px; font-weight: 700;
    color: var(--ink); letter-spacing: -0.01em;
  }

  .topbar-date {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px; color: var(--ink-3);
  }


  .search-form {
    display: flex; align-items: center; gap: 8px;
  }

  .search-wrap {
    position: relative;
  }

  .search-icon {
    position: absolute; left: 10px; top: 50%;
    transform: translateY(-50%);
    color: var(--ink-3); width: 14px; height: 14px;
    pointer-events: none;
  }

  .search-input {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 7px 12px 7px 32px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px; color: var(--ink);
    outline: none; width: 220px;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    -webkit-appearance: none;
  }

  .search-input::placeholder { color: var(--ink-3); }

  .search-input:focus {
    border-color: var(--accent);
    background: var(--surface);
    box-shadow: 0 0 0 3px var(--accent-ring);
  }

  .btn {
    font-family: 'Outfit', sans-serif;
    font-size: 12.5px; font-weight: 600;
    border: none; cursor: pointer;
    border-radius: var(--radius-sm);
    display: inline-flex; align-items: center; gap: 6px;
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
    text-decoration: none; padding: 8px 16px;
  }

  .btn svg { width: 14px; height: 14px; }

  .btn-primary {
    background: var(--accent); color: #fff;
  }

  .btn-primary:hover {
    background: var(--accent-2);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(45,106,79,0.25);
  }

  .btn-sm { padding: 6px 12px; font-size: 12px; }
  .btn-sm svg { width: 12px; height: 12px; }

  .btn-search {
    background: var(--ink); color: #fff; padding: 7px 14px;
  }

  .btn-search:hover { background: var(--ink-2); }


  .content {
    padding: 32px 36px 60px;
    animation: fadeUp 0.55s 0.1s cubic-bezier(0.16,1,0.3,1) both;
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
  }


  .stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 28px;
  }

  .stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    display: flex; align-items: center; gap: 14px;
    transition: box-shadow 0.2s;
  }

  .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); }

  .stat-icon-wrap {
    width: 40px; height: 40px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }

  .stat-icon-wrap svg { width: 18px; height: 18px; }

  .stat-num {
    font-family: 'Outfit', sans-serif;
    font-size: 24px; font-weight: 700;
    color: var(--ink); line-height: 1;
    letter-spacing: -0.02em; margin-bottom: 2px;
  }

  .stat-lbl { font-size: 12px; color: var(--ink-3); }


  .section-header {
    display: flex; align-items: center;
    justify-content: space-between; gap: 16px;
    margin-bottom: 16px;
  }

  .section-title {
    font-family: 'Outfit', sans-serif;
    font-size: 15px; font-weight: 700;
    color: var(--ink); letter-spacing: -0.01em;
  }

  .section-sub {
    font-size: 13px; color: var(--ink-3); margin-top: 1px;
  }


  .search-notice {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--blue-pale);
    border: 1px solid rgba(29,78,216,0.15);
    border-radius: var(--radius-xs);
    padding: 5px 11px;
    font-size: 12.5px; color: var(--blue);
    margin-bottom: 14px;
  }

  .search-notice a {
    color: var(--blue); font-weight: 600;
    text-decoration: none; border-bottom: 1px solid rgba(29,78,216,0.3);
    margin-left: 4px;
  }


  .table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }

  table {
    width: 100%; border-collapse: collapse;
  }

  thead { background: var(--surface-2); }

  th {
    font-family: 'Outfit', sans-serif;
    font-size: 11px; font-weight: 600;
    letter-spacing: 0.06em; text-transform: uppercase;
    color: var(--ink-3);
    padding: 12px 18px;
    text-align: left;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
  }

  th:last-child { text-align: right; }

  tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.12s;
  }

  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover { background: var(--surface-2); }

  td {
    padding: 13px 18px;
    font-size: 14px; color: var(--ink-2);
    vertical-align: middle;
  }

  td:last-child { text-align: right; }

  .td-id {
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px; color: var(--ink-3);
  }

  .td-title {
    font-family: 'Outfit', sans-serif;
    font-size: 14px; font-weight: 600;
    color: var(--ink); letter-spacing: -0.01em;
  }

  .td-date {
    font-family: 'JetBrains Mono', monospace;
    font-size: 12.5px; color: var(--ink-2);
    white-space: nowrap;
  }

  .ev-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'Outfit', sans-serif;
    font-size: 11px; font-weight: 600;
    letter-spacing: 0.03em; text-transform: uppercase;
    padding: 3px 9px; border-radius: 100px;
    border: 1px solid; white-space: nowrap;
  }

  .ev-badge-dot {
    width: 5px; height: 5px; border-radius: 50%;
  }


  .action-group {
    display: flex; align-items: center;
    justify-content: flex-end; gap: 6px;
  }

  .btn-edit {
    background: var(--blue-pale);
    color: var(--blue);
    border: 1px solid rgba(29,78,216,0.15);
    font-family: 'Outfit', sans-serif;
    font-size: 11.5px; font-weight: 600;
    padding: 5px 12px; border-radius: var(--radius-xs);
    text-decoration: none;
    display: inline-flex; align-items: center; gap: 5px;
    transition: background 0.15s, box-shadow 0.15s;
  }

  .btn-edit:hover { background: #dbeafe; box-shadow: 0 2px 8px rgba(29,78,216,0.12); }
  .btn-edit svg { width: 12px; height: 12px; }

  .btn-delete {
    background: var(--red-pale);
    color: var(--red);
    border: 1px solid var(--red-border);
    font-family: 'Outfit', sans-serif;
    font-size: 11.5px; font-weight: 600;
    padding: 5px 12px; border-radius: var(--radius-xs);
    text-decoration: none;
    display: inline-flex; align-items: center; gap: 5px;
    transition: background 0.15s, box-shadow 0.15s;
  }

  .btn-delete:hover { background: #ffe4e6; box-shadow: 0 2px 8px rgba(159,18,57,0.12); }
  .btn-delete svg { width: 12px; height: 12px; }


  .empty-row td {
    padding: 52px 20px;
    text-align: center;
  }

  .empty-inner {
    display: flex; flex-direction: column;
    align-items: center; gap: 10px;
  }

  .empty-icon {
    width: 44px; height: 44px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    color: var(--ink-3);
  }

  .empty-icon svg { width: 20px; height: 20px; }

  .empty-inner h3 {
    font-family: 'Outfit', sans-serif;
    font-size: 15px; font-weight: 600;
    color: var(--ink-2);
  }

  .empty-inner p { font-size: 13px; color: var(--ink-3); }


  .sidebar-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 49;
    backdrop-filter: blur(2px);
  }

  .sidebar-overlay.open { display: block; }

  
  .menu-toggle {
    display: none;
    align-items: center; justify-content: center;
    width: 36px; height: 36px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    cursor: pointer; flex-shrink: 0;
    color: var(--ink-2);
  }

  .menu-toggle svg { width: 18px; height: 18px; }

  @media (max-width: 768px) {
    body { display: block; }


    .sidebar {
      position: fixed;
      left: 0; top: 0; bottom: 0;
      transform: translateX(-100%);
      transition: transform 0.3s cubic-bezier(0.16,1,0.3,1);
      z-index: 200;
      box-shadow: 4px 0 24px rgba(0,0,0,0.12);
      animation: none !important;
      min-height: 100vh;
    }

    .sidebar.open { transform: translateX(0) !important; }

    .menu-toggle { display: flex; }

    .main { min-height: 100vh; }

    .topbar {
      padding: 0 16px;
      gap: 10px;
    }

    .topbar-left { gap: 8px; flex: 1; min-width: 0; }

    .topbar-breadcrumb { display: none; }

   
    .search-form { flex-wrap: wrap; gap: 6px; }
    .search-input { width: 160px; font-size: 13px; }

    .topbar > div:last-child {
      gap: 6px;
    }


    .btn-topbar-text { display: none; }

    .content { padding: 20px 16px 48px; }


    .stats-row { grid-template-columns: 1fr; gap: 8px; }


    .section-header { flex-direction: column; align-items: flex-start; gap: 10px; }


    .table-card { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    table { min-width: 560px; }

    th, td { padding: 11px 14px; }

    .action-group { gap: 4px; }

    .btn-edit, .btn-delete { padding: 5px 9px; font-size: 11px; }
  }

  @media (max-width: 480px) {
    .topbar-date { display: none; }
    .search-input { width: 120px; }
  }
</style>
</head>
<body>


<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>


<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <img src="../logo.png" alt="ADBU Logo">
    <div>
      <div class="sidebar-brand-name">Assam Don Bosco University</div>
      <div class="sidebar-brand-sub">Admin Portal</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Events</div>
    <a href="dashboard.php" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      All Events
    </a>
    <a href="add_event.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
      </svg>
      Add Event
    </a>

    <div class="nav-section-label">Public</div>
    <a href="../index.php" class="nav-item" target="_blank">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
      </svg>
      View Calendar
    </a>

    <div class="nav-section-label">Account</div>
    <a href="../logout.php" class="nav-item danger">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
      Logout
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-chip">
      <div class="user-avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
      <div>
        <div class="user-name"><?= htmlspecialchars($_SESSION['username']) ?></div>
        <div class="user-role">Administrator</div>
      </div>
    </div>
  </div>
</aside>


<div class="main">


  <div class="topbar">
    <div class="topbar-left">
      <button class="menu-toggle" onclick="openSidebar()" aria-label="Open menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
      <span class="topbar-title">Events Dashboard</span>
      <span class="topbar-date"><?= date('l, j F Y') ?></span>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <form method="get" class="search-form">
        <div class="search-wrap">
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" name="search" class="search-input"
            placeholder="Search events…"
            value="<?= htmlspecialchars($search) ?>">
        </div>
        <button type="submit" class="btn btn-search">Search</button>
        <?php if($search): ?>
          <a href="dashboard.php" class="btn" style="background:var(--surface-2);color:var(--ink-2);border:1px solid var(--border);">Clear</a>
        <?php endif; ?>
      </form>
      <a href="add_event.php" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Event
      </a>
    </div>
  </div>


  <div class="content">


    <?php if(!$search): ?>
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon-wrap" style="background:#ecfdf5;color:#10b981;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <div>
          <div class="stat-num"><?= $total ?></div>
          <div class="stat-lbl">Total events</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrap" style="background:#eff6ff;color:#3b82f6;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <div>
          <div class="stat-num"><?= $upcoming ?></div>
          <div class="stat-lbl">Upcoming</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrap" style="background:#fef3c7;color:#f59e0b;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
          </svg>
        </div>
        <div>
          <div class="stat-num"><?= $past ?></div>
          <div class="stat-lbl">Past events</div>
        </div>
      </div>
    </div>
    <?php endif; ?>


    <div class="section-header">
      <div>
        <div class="section-title">
          <?= $search ? 'Search Results' : 'All Events' ?>
        </div>
        <div class="section-sub">
          <?= $total ?> <?= $total === 1 ? 'record' : 'records' ?><?= $search ? ' matching "' . htmlspecialchars($search) . '"' : '' ?>
        </div>
      </div>
    </div>

    <?php if($search): ?>
    <div class="search-notice">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      Showing results for "<?= htmlspecialchars($search) ?>"
      <a href="dashboard.php">Clear search</a>
    </div>
    <?php endif; ?>

    <div class="table-card">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Date</th>
            <th>Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($events)): ?>
          <tr class="empty-row">
            <td colspan="5">
              <div class="empty-inner">
                <div class="empty-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                  </svg>
                </div>
                <h3><?= $search ? 'No results found' : 'No events yet' ?></h3>
                <p><?= $search ? 'Try a different search term.' : 'Add your first event to get started.' ?></p>
              </div>
            </td>
          </tr>
          <?php else: ?>
          <?php foreach($events as $row):
            $c = $typeConfig[$row['type']] ?? $typeConfig['Academic'];
            $isPast = strtotime($row['event_date']) < strtotime('today');
          ?>
          <tr style="<?= $isPast ? 'opacity:0.65;' : '' ?>">
            <td class="td-id"><?= $row['id'] ?></td>
            <td class="td-title"><?= htmlspecialchars($row['title']) ?></td>
            <td class="td-date"><?= date('j M Y', strtotime($row['event_date'])) ?></td>
            <td>
              <span class="ev-badge" style="color:<?= $c['color'] ?>;background:<?= $c['bg'] ?>;border-color:<?= $c['border'] ?>">
                <span class="ev-badge-dot" style="background:<?= $c['dot'] ?>"></span>
                <?= htmlspecialchars($row['type']) ?>
              </span>
            </td>
            <td>
              <div class="action-group">
                <a href="edit_event.php?id=<?= $row['id'] ?>" class="btn-edit">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                  Edit
                </a>
                <a href="delete_event.php?id=<?= $row['id'] ?>"
                  class="btn-delete"
                  onclick="return confirm('Delete this event?')">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                  </svg>
                  Delete
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

</body>
<script>
  function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }

  document.addEventListener('keydown', e => { if(e.key === 'Escape') closeSidebar(); });
</script>
</html>