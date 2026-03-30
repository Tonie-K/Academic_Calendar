<?php
include "../config.php";

if(!isset($_SESSION['admin_id'])){
header("Location: ../login.php");
exit();
}

if(isset($_POST['add'])){

$stmt=$conn->prepare("INSERT INTO events(title,type,event_date,description) VALUES(?,?,?,?)");

$stmt->bind_param("ssss",
$_POST['title'],
$_POST['type'],
$_POST['date'],
$_POST['description']
);

$stmt->execute();

header("Location: dashboard.php");
exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Event — ADBU Admin</title>
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

  /* ── SIDEBAR ── */
  .sidebar {
    width: var(--sidebar-w);
    flex-shrink: 0;
    background: var(--surface);
    border-right: 1px solid var(--border);
    min-height: 100vh;
    position: sticky; top: 0;
    height: 100vh; overflow-y: auto;
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

  .sidebar-brand-sub { font-size: 11px; color: var(--ink-3); margin-top: 1px; }

  .sidebar-nav {
    padding: 16px 12px;
    flex: 1; display: flex; flex-direction: column; gap: 2px;
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
  }

  .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; opacity: 0.7; }
  .nav-item:hover { background: var(--surface-2); color: var(--ink); }
  .nav-item:hover svg { opacity: 1; }

  .nav-item.active {
    background: var(--accent-pale);
    color: var(--accent); font-weight: 600;
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

  .user-name { font-size: 13px; font-weight: 500; color: var(--ink); line-height: 1; margin-bottom: 2px; }
  .user-role { font-size: 11px; color: var(--ink-3); }

  /* ── MAIN ── */
  .main {
    flex: 1; min-width: 0;
    display: flex; flex-direction: column;
    position: relative; z-index: 1;
  }

  /* Topbar */
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

  .topbar-left { display: flex; align-items: center; gap: 10px; }

  .back-btn {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--ink-3);
    text-decoration: none;
    padding: 5px 10px; border-radius: var(--radius-xs);
    border: 1px solid var(--border);
    background: var(--surface-2);
    transition: background 0.15s, color 0.15s;
  }

  .back-btn:hover { background: var(--border); color: var(--ink); }
  .back-btn svg { width: 13px; height: 13px; }

  .topbar-breadcrumb {
    display: flex; align-items: center; gap: 6px;
    font-size: 13.5px; color: var(--ink-3);
  }

  .topbar-breadcrumb a {
    color: var(--ink-3); text-decoration: none;
    transition: color 0.15s;
  }

  .topbar-breadcrumb a:hover { color: var(--ink); }

  .topbar-breadcrumb .sep { opacity: 0.4; }

  .topbar-breadcrumb .current {
    color: var(--ink); font-weight: 500;
  }

  .topbar-date {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px; color: var(--ink-3);
  }

  /* ── CONTENT ── */
  .content {
    padding: 36px;
    max-width: 720px;
    animation: fadeUp 0.55s 0.1s cubic-bezier(0.16,1,0.3,1) both;
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .page-header {
    margin-bottom: 28px;
  }

  .page-title {
    font-family: 'Outfit', sans-serif;
    font-size: 22px; font-weight: 700;
    color: var(--ink); letter-spacing: -0.02em;
    margin-bottom: 4px;
  }

  .page-desc { font-size: 14px; color: var(--ink-3); }

  /* Form card */
  .form-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }

  .form-section {
    padding: 28px 32px;
    border-bottom: 1px solid var(--border);
  }

  .form-section:last-of-type { border-bottom: none; }

  .form-section-label {
    font-family: 'Outfit', sans-serif;
    font-size: 11px; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    color: var(--ink-3);
    margin-bottom: 20px;
    display: flex; align-items: center; gap: 8px;
  }

  .form-section-label::after {
    content: '';
    flex: 1; height: 1px;
    background: var(--border);
  }

  /* Two-column grid */
  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px 24px;
  }

  .field-full { grid-column: 1 / -1; }

  .field { display: flex; flex-direction: column; gap: 7px; }

  label {
    font-family: 'Outfit', sans-serif;
    font-size: 12px; font-weight: 600;
    letter-spacing: 0.03em; text-transform: uppercase;
    color: var(--ink-2);
    display: flex; align-items: center; gap: 4px;
  }

  label .req { color: var(--red); font-size: 14px; line-height: 1; }

  .input-wrap { position: relative; }

  .input-icon {
    position: absolute; left: 11px; top: 50%;
    transform: translateY(-50%);
    color: var(--ink-3); width: 15px; height: 15px;
    pointer-events: none;
  }

  input[type="text"],
  input[type="date"],
  select,
  textarea {
    width: 100%;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px; color: var(--ink);
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    -webkit-appearance: none;
  }

  input[type="text"],
  input[type="date"],
  select { padding: 10px 12px 10px 36px; }

  input[type="text"]::placeholder { color: var(--ink-3); }

  input[type="date"] { cursor: pointer; }
  input[type="date"]::-webkit-calendar-picker-indicator { opacity: 0.5; cursor: pointer; }

  textarea {
    padding: 11px 14px;
    resize: vertical; min-height: 100px;
    line-height: 1.6; font-size: 14px;
  }

  input:focus, select:focus, textarea:focus {
    border-color: var(--accent);
    background: var(--surface);
    box-shadow: 0 0 0 3px var(--accent-ring);
  }

  /* Select arrow */
  .select-wrap { position: relative; }

  .select-wrap select { padding-right: 32px; cursor: pointer; }

  .select-arrow {
    position: absolute; right: 11px; top: 50%;
    transform: translateY(-50%);
    color: var(--ink-3); pointer-events: none;
    width: 14px; height: 14px;
  }

  /* Type preview pills */
  .type-pills {
    display: flex; gap: 6px; flex-wrap: wrap;
    margin-top: 8px;
  }

  .type-pill-opt {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'Outfit', sans-serif;
    font-size: 11px; font-weight: 600;
    letter-spacing: 0.03em; text-transform: uppercase;
    padding: 4px 10px; border-radius: 100px;
    border: 1px solid; opacity: 0.6;
    cursor: pointer; transition: opacity 0.15s;
  }

  .type-pill-opt.selected { opacity: 1; }
  .type-pill-dot { width: 5px; height: 5px; border-radius: 50%; }

  /* Field hint */
  .field-hint { font-size: 12px; color: var(--ink-3); margin-top: -2px; }

  /* Form actions */
  .form-actions {
    padding: 20px 32px;
    background: var(--surface-2);
    border-top: 1px solid var(--border);
    display: flex; align-items: center;
    justify-content: space-between; gap: 14px;
  }

  .form-actions-note {
    font-size: 12.5px; color: var(--ink-3);
  }

  .form-actions-note em { color: var(--red); font-style: normal; }

  .form-actions-right {
    display: flex; align-items: center; gap: 10px;
  }

  .btn {
    font-family: 'Outfit', sans-serif;
    font-size: 13px; font-weight: 600;
    border: none; cursor: pointer;
    border-radius: var(--radius-sm);
    display: inline-flex; align-items: center; gap: 7px;
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
    text-decoration: none; padding: 10px 20px;
    letter-spacing: 0.01em;
  }

  .btn svg { width: 15px; height: 15px; }

  .btn-primary {
    background: var(--accent); color: #fff;
    position: relative; overflow: hidden;
  }

  .btn-primary::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(120deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    transform: translateX(-100%);
    transition: transform 0.5s;
  }

  .btn-primary:hover {
    background: var(--accent-2);
    transform: translateY(-1px);
    box-shadow: 0 5px 16px rgba(45,106,79,0.28);
  }

  .btn-primary:hover::after { transform: translateX(100%); }
  .btn-primary:active { transform: translateY(0); box-shadow: none; }

  .btn-ghost {
    background: transparent; color: var(--ink-2);
    border: 1px solid var(--border);
  }

  .btn-ghost:hover { background: var(--border); }

  /* ── MOBILE ── */
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

    .topbar { padding: 0 16px; }
    .topbar-left { gap: 8px; }

    .content { padding: 20px 16px 48px; max-width: 100%; }

    .form-grid { grid-template-columns: 1fr; }
    .field-full { grid-column: 1; }

    .form-section { padding: 22px 18px; }
    .form-actions { padding: 16px 18px; flex-direction: column; align-items: stretch; gap: 12px; }
    .form-actions-right { flex-direction: column; gap: 8px; }
    .form-actions-right .btn { justify-content: center; }
    .form-actions-note { text-align: center; }

    .type-pills { gap: 5px; }
  }

  @media (max-width: 480px) {
    .topbar-date { display: none; }
    .back-btn span { display: none; }
  }
</style>
</head>
<body>

<!-- Sidebar -->
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
    <a href="dashboard.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      All Events
    </a>
    <a href="add_event.php" class="nav-item active">
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

<!-- Main -->
<div class="main">

  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="menu-toggle" onclick="openSidebar()" aria-label="Open menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
      <a href="dashboard.php" class="back-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
        Back
      </a>
      <div class="topbar-breadcrumb">
        <a href="dashboard.php">Dashboard</a>
        <span class="sep">/</span>
        <span class="current">Add Event</span>
      </div>
    </div>
    <span class="topbar-date"><?= date('l, j F Y') ?></span>
  </div>

  <!-- Content -->
  <div class="content">

    <div class="page-header">
      <div class="page-title">Add New Event</div>
      <div class="page-desc">Fill in the details below to add an event to the academic calendar.</div>
    </div>

    <div class="form-card">
      <form method="post" novalidate>

        <!-- Section 1: Core details -->
        <div class="form-section">
          <div class="form-section-label">Event Details</div>
          <div class="form-grid">

            <div class="field field-full">
              <label for="title">Event Title <span class="req">*</span></label>
              <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/>
                </svg>
                <input type="text" id="title" name="title"
                  placeholder="e.g. Mid-Semester Examinations"
                  required autocomplete="off"
                  value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
              </div>
            </div>

            <div class="field">
              <label for="date">Event Date <span class="req">*</span></label>
              <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <input type="date" id="date" name="date" required
                  value="<?= isset($_POST['date']) ? htmlspecialchars($_POST['date']) : '' ?>">
              </div>
            </div>

            <div class="field">
              <label for="type">Event Type <span class="req">*</span></label>
              <div class="input-wrap select-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                <select id="type" name="type" required>
                  <option value="Holiday" <?= (isset($_POST['type']) && $_POST['type']==='Holiday') ? 'selected' : '' ?>>Holiday</option>
                  <option value="Exam"    <?= (isset($_POST['type']) && $_POST['type']==='Exam')    ? 'selected' : '' ?>>Examination</option>
                  <option value="Academic"<?= (isset($_POST['type']) && $_POST['type']==='Academic')? 'selected' : '' ?>>Academic</option>
                </select>
                <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6 9 12 15 18 9"/>
                </svg>
              </div>
              <div class="type-pills">
                <span class="type-pill-opt" style="color:#b45309;background:#fffbeb;border-color:rgba(180,83,9,0.2)">
                  <span class="type-pill-dot" style="background:#f59e0b"></span>Holiday
                </span>
                <span class="type-pill-opt" style="color:#9f1239;background:#fff1f2;border-color:rgba(159,18,57,0.2)">
                  <span class="type-pill-dot" style="background:#e11d48"></span>Exam
                </span>
                <span class="type-pill-opt selected" style="color:#065f46;background:#ecfdf5;border-color:rgba(6,95,70,0.2)">
                  <span class="type-pill-dot" style="background:#10b981"></span>Academic
                </span>
              </div>
            </div>

          </div>
        </div>

        <!-- Section 2: Description -->
        <div class="form-section">
          <div class="form-section-label">Additional Info</div>
          <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description"
              placeholder="Add any relevant notes, venue details, or instructions…"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
            <span class="field-hint">Optional — this will be shown on the public calendar.</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <span class="form-actions-note">Fields marked <em>*</em> are required</span>
          <div class="form-actions-right">
            <a href="dashboard.php" class="btn btn-ghost">Cancel</a>
            <button type="submit" name="add" class="btn btn-primary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
              </svg>
              Add Event
            </button>
          </div>
        </div>

      </form>
    </div>

  </div>
</div>

<script>
  // Sync type pills with select
  const typeSelect = document.getElementById('type');
  const pills = document.querySelectorAll('.type-pill-opt');
  const typeMap = ['Holiday', 'Exam', 'Academic'];

  function syncPills(val) {
    pills.forEach((p, i) => {
      p.classList.toggle('selected', typeMap[i] === val);
    });
  }

  typeSelect.addEventListener('change', () => syncPills(typeSelect.value));

  pills.forEach((p, i) => {
    p.addEventListener('click', () => {
      typeSelect.value = typeMap[i];
      syncPills(typeMap[i]);
    });
  });

  syncPills(typeSelect.value);
</script>
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