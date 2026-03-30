<?php
include "config.php";

$result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
$events = [];
while($row = $result->fetch_assoc()) $events[] = $row;

$grouped = [];
foreach($events as $e) {
  $monthKey = date('F Y', strtotime($e['event_date']));
  $grouped[$monthKey][] = $e;
}


$byDate = [];
foreach($events as $e) {
  $byDate[$e['event_date']][] = $e;
}

$typeConfig = [
  'Holiday'  => ['color' => '#b45309', 'bg' => '#fffbeb', 'border' => 'rgba(180,83,9,0.2)',  'dot' => '#f59e0b'],
  'Exam'     => ['color' => '#9f1239', 'bg' => '#fff1f2', 'border' => 'rgba(159,18,57,0.2)', 'dot' => '#e11d48'],
  'Academic' => ['color' => '#065f46', 'bg' => '#ecfdf5', 'border' => 'rgba(6,95,70,0.2)',   'dot' => '#10b981'],
];

$totalEvents = count($events);
$upcoming = 0;
foreach($events as $e) { if(strtotime($e['event_date']) >= strtotime('today')) $upcoming++; }
$monthCount = count($grouped);


$calMonths = [];
foreach(array_keys($grouped) as $mk) {
  $calMonths[] = date('Y-m', strtotime($mk));
}
sort($calMonths);
$calMonths = array_values(array_unique($calMonths));


$curMonth = date('Y-m');
if(!in_array($curMonth, $calMonths)) {
  $calMonths[] = $curMonth;
  sort($calMonths);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ADBU Academic Calendar</title>
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
    --accent-ring: rgba(16,185,129,0.15);
    --radius:      12px;
    --radius-sm:   8px;
    --radius-xs:   5px;
  }

  html { scroll-behavior: smooth; }

  body {
    min-height: 100vh;
    background: var(--bg);
    font-family: 'DM Sans', sans-serif;
    color: var(--ink);
    -webkit-font-smoothing: antialiased;
  }

  body::after {
    content: '';
    position: fixed; inset: 0;
    background-image:
      linear-gradient(var(--border) 1px, transparent 1px),
      linear-gradient(90deg, var(--border) 1px, transparent 1px);
    background-size: 40px 40px;
    opacity: 0.3;
    pointer-events: none;
    z-index: 0;
  }


  .site-header {
    background: rgba(255,255,255,0.92);
    border-bottom: 1px solid var(--border);
    position: sticky; top: 0; z-index: 100;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    animation: slideDown 0.5s cubic-bezier(0.16,1,0.3,1) both;
  }

  .header-inner {
    max-width: 1080px; margin: 0 auto;
    padding: 0 40px; height: 68px;
    display: flex; align-items: center;
    justify-content: space-between; gap: 24px;
  }

  .brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
  .brand img { height: 40px; width: auto; object-fit: contain; }
  .brand-divider { width: 1px; height: 30px; background: var(--border-2); flex-shrink: 0; }
  .brand-name { font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 600; color: var(--ink); line-height: 1; letter-spacing: -0.01em; display: block; margin-bottom: 2px; }
  .brand-sub { font-size: 11.5px; color: var(--ink-3); display: block; }

  .btn-admin {
    font-family: 'Outfit', sans-serif;
    font-size: 12px; font-weight: 500;
    color: var(--surface); background: var(--ink);
    text-decoration: none; padding: 8px 18px;
    border-radius: var(--radius-sm); letter-spacing: 0.02em;
    transition: background 0.2s, transform 0.15s;
    display: inline-flex; align-items: center; gap: 6px;
  }
  .btn-admin:hover { background: var(--ink-2); transform: translateY(-1px); }
  .btn-admin svg { width: 13px; height: 13px; }

 
  .hero {
    max-width: 1080px; margin: 0 auto;
    padding: 48px 40px 36px;
    position: relative; z-index: 1;
    animation: fadeUp 0.6s 0.08s cubic-bezier(0.16,1,0.3,1) both;
  }

  .hero-inner {
    display: flex; align-items: flex-start;
    justify-content: space-between; gap: 32px; flex-wrap: wrap;
  }

  .hero-left { flex: 1; min-width: 260px; }

  .hero-tag {
    display: inline-flex; align-items: center; gap: 6px;
    font-family: 'Outfit', sans-serif; font-size: 11px; font-weight: 600;
    letter-spacing: 0.08em; text-transform: uppercase;
    color: var(--accent); background: var(--accent-pale);
    border: 1px solid var(--accent-ring);
    padding: 4px 11px; border-radius: 100px; margin-bottom: 14px;
  }

  .hero-tag::before {
    content: ''; width: 6px; height: 6px;
    background: var(--accent); border-radius: 50%;
    animation: blink 2.4s ease infinite;
  }

  @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

  h1 {
    font-family: 'Outfit', sans-serif; font-size: clamp(26px, 4vw, 38px);
    font-weight: 700; letter-spacing: -0.03em; line-height: 1.1;
    color: var(--ink); margin-bottom: 10px;
  }

  h1 em { font-style: normal; color: var(--accent); }

  .hero-desc { font-size: 15px; color: var(--ink-2); line-height: 1.6; max-width: 440px; }

  .hero-stats { display: flex; flex-direction: column; gap: 8px; flex-shrink: 0; min-width: 180px; }

  .stat-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 12px 16px;
    display: flex; align-items: center; gap: 10px;
  }

  .stat-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
  .stat-num { font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 700; color: var(--ink); line-height: 1; margin-bottom: 1px; }
  .stat-lbl { font-size: 11px; color: var(--ink-3); }

  .controls-bar {
    max-width: 1080px; margin: 0 auto 24px;
    padding: 0 40px;
    position: relative; z-index: 1;
    display: flex; align-items: center;
    justify-content: space-between; gap: 16px; flex-wrap: wrap;
    animation: fadeUp 0.6s 0.14s cubic-bezier(0.16,1,0.3,1) both;
  }

  .legend { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
  .legend-label { font-family: 'Outfit', sans-serif; font-size: 11px; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--ink-3); margin-right: 4px; }

  .type-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'Outfit', sans-serif; font-size: 11.5px; font-weight: 500;
    padding: 4px 10px; border-radius: 100px; border: 1px solid; letter-spacing: 0.02em;
  }


  .view-toggle {
    display: flex; align-items: center;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius-sm); padding: 3px; gap: 2px;
  }

  .view-btn {
    display: flex; align-items: center; gap: 5px;
    font-family: 'Outfit', sans-serif; font-size: 12px; font-weight: 600;
    color: var(--ink-3); padding: 6px 12px;
    border-radius: 6px; cursor: pointer; border: none;
    background: none; transition: background 0.15s, color 0.15s;
    letter-spacing: 0.01em;
  }

  .view-btn svg { width: 14px; height: 14px; }
  .view-btn:hover { background: var(--surface-2); color: var(--ink); }
  .view-btn.active { background: var(--ink); color: #fff; }


  .content-area {
    max-width: 1080px; margin: 0 auto;
    padding: 0 40px 80px;
    position: relative; z-index: 1;
  }

 
  #calendarView { display: block; }
  #listView     { display: none; }

  .cal-nav {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px;
  }

  .cal-month-title {
    font-family: 'Outfit', sans-serif;
    font-size: 18px; font-weight: 700;
    color: var(--ink); letter-spacing: -0.01em;
  }

  .cal-nav-btns { display: flex; align-items: center; gap: 6px; }

  .cal-nav-btn {
    width: 34px; height: 34px;
    border: 1px solid var(--border); background: var(--surface);
    border-radius: var(--radius-sm); cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--ink-2); transition: background 0.15s, border-color 0.15s;
  }

  .cal-nav-btn:hover { background: var(--surface-2); border-color: var(--border-2); }
  .cal-nav-btn svg { width: 16px; height: 16px; }

  .cal-today-btn {
    font-family: 'Outfit', sans-serif; font-size: 12px; font-weight: 600;
    color: var(--ink-2); background: var(--surface);
    border: 1px solid var(--border); padding: 6px 14px;
    border-radius: var(--radius-sm); cursor: pointer;
    transition: background 0.15s; letter-spacing: 0.01em;
  }

  .cal-today-btn:hover { background: var(--surface-2); }


  .cal-grid {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  }

  .cal-weekdays {
    display: grid; grid-template-columns: repeat(7, 1fr);
    border-bottom: 1px solid var(--border);
    background: var(--surface-2);
  }

  .cal-wd {
    font-family: 'Outfit', sans-serif; font-size: 11px; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    color: var(--ink-3); text-align: center;
    padding: 10px 4px;
  }

  .cal-days {
    display: grid; grid-template-columns: repeat(7, 1fr);
  }

  .cal-day {
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    min-height: 100px; padding: 8px 6px 6px;
    position: relative;
    transition: background 0.12s;
    cursor: default;
  }

  .cal-day:nth-child(7n) { border-right: none; }

  .cal-day.last-row { border-bottom: none; }

  .cal-day.other-month { background: var(--surface-2); }
  .cal-day.other-month .cal-day-num { color: var(--border-2); }

  .cal-day.today .cal-day-num {
    background: var(--accent);
    color: #fff;
    border-radius: 50%;
    width: 24px; height: 24px;
    display: flex; align-items: center; justify-content: center;
  }

  .cal-day.has-events { cursor: pointer; }
  .cal-day.has-events:hover { background: rgba(45,106,79,0.03); }

  .cal-day-num {
    font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 600;
    color: var(--ink-2); margin-bottom: 4px;
    width: 24px; height: 24px;
    display: flex; align-items: center; justify-content: center;
  }

  .cal-events { display: flex; flex-direction: column; gap: 2px; }

  .cal-event-chip {
    font-family: 'Outfit', sans-serif; font-size: 10.5px; font-weight: 600;
    padding: 2px 5px; border-radius: 3px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    cursor: pointer; transition: opacity 0.15s;
    border-left: 2px solid;
  }

  .cal-event-chip:hover { opacity: 0.8; }

  .cal-more {
    font-family: 'JetBrains Mono', monospace; font-size: 10px;
    color: var(--ink-3); padding: 1px 4px; cursor: pointer;
  }

  .event-panel-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.35);
    z-index: 500;
    backdrop-filter: blur(4px);
    animation: fadeIn 0.2s ease;
  }

  @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

  .event-panel {
    position: fixed;
    right: 0; top: 0; bottom: 0;
    width: 360px; max-width: 100vw;
    background: var(--surface);
    border-left: 1px solid var(--border);
    z-index: 501;
    display: flex; flex-direction: column;
    animation: slideLeft 0.3s cubic-bezier(0.16,1,0.3,1);
    box-shadow: -8px 0 32px rgba(0,0,0,0.1);
  }

  @keyframes slideLeft {
    from { transform: translateX(100%); }
    to   { transform: translateX(0); }
  }

  .event-panel-header {
    padding: 20px 20px 16px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
  }

  .event-panel-date {
    font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700;
    color: var(--ink); letter-spacing: -0.01em;
  }

  .event-panel-close {
    width: 30px; height: 30px;
    border: 1px solid var(--border); background: var(--surface-2);
    border-radius: var(--radius-xs); cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--ink-3); transition: background 0.15s;
  }

  .event-panel-close:hover { background: var(--border); color: var(--ink); }
  .event-panel-close svg { width: 14px; height: 14px; }

  .event-panel-body {
    padding: 16px 20px;
    overflow-y: auto; flex: 1;
    display: flex; flex-direction: column; gap: 12px;
  }

  .panel-event-card {
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 14px 16px;
    border-left: 4px solid;
    transition: box-shadow 0.15s;
  }

  .panel-event-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,0.07); }

  .panel-event-title {
    font-family: 'Outfit', sans-serif; font-size: 14.5px; font-weight: 600;
    color: var(--ink); margin-bottom: 6px; letter-spacing: -0.01em;
  }

  .panel-event-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-family: 'Outfit', sans-serif; font-size: 10.5px; font-weight: 600;
    letter-spacing: 0.04em; text-transform: uppercase;
    padding: 3px 8px; border-radius: 100px; border: 1px solid;
    margin-bottom: 8px;
  }

  .panel-event-badge-dot { width: 5px; height: 5px; border-radius: 50%; }

  .panel-event-desc {
    font-size: 13px; color: var(--ink-3); line-height: 1.5;
  }

  .month-section { margin-bottom: 40px; animation: fadeUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
  .month-section:nth-child(1) { animation-delay: 0.10s; }
  .month-section:nth-child(2) { animation-delay: 0.16s; }
  .month-section:nth-child(3) { animation-delay: 0.22s; }
  .month-section:nth-child(n+4) { animation-delay: 0.28s; }

  .month-header { display: flex; align-items: center; gap: 14px; margin-bottom: 12px; }
  .month-label { font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: var(--ink-3); flex-shrink: 0; }
  .month-divider { flex: 1; height: 1px; background: var(--border); }
  .month-badge { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--ink-3); background: var(--surface-2); border: 1px solid var(--border); padding: 3px 8px; border-radius: var(--radius-xs); flex-shrink: 0; }

  .event-list { display: flex; flex-direction: column; gap: 4px; }

  .event-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); display: grid;
    grid-template-columns: 72px 1fr auto; overflow: hidden;
    position: relative; transition: box-shadow 0.2s, transform 0.18s, border-color 0.2s;
  }

  .event-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.08); transform: translateY(-2px); border-color: var(--border-2); z-index: 2; }

  .event-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--ev-dot); opacity: 0.8; }

  .ev-date { padding: 18px 14px 18px 18px; border-right: 1px solid var(--border); display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; flex-shrink: 0; }
  .ev-day { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 700; color: var(--ink); line-height: 1; letter-spacing: -0.02em; }
  .ev-wd { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: var(--ink-3); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 3px; }
  .ev-body { padding: 16px 20px; min-width: 0; display: flex; flex-direction: column; justify-content: center; }
  .ev-title { font-family: 'Outfit', sans-serif; font-size: 15.5px; font-weight: 600; color: var(--ink); line-height: 1.25; letter-spacing: -0.01em; margin-bottom: 4px; }
  .ev-desc { font-size: 13px; color: var(--ink-3); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  .ev-type { padding: 16px 18px 16px 12px; display: flex; align-items: center; justify-content: flex-end; flex-shrink: 0; }
  .ev-badge { display: inline-flex; align-items: center; gap: 5px; font-family: 'Outfit', sans-serif; font-size: 11px; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase; padding: 4px 10px; border-radius: 100px; border: 1px solid; white-space: nowrap; }
  .ev-badge-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }


  .empty-state { text-align: center; padding: 64px 40px; position: relative; z-index: 1; }
  .empty-icon { width: 48px; height: 48px; background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--ink-3); }
  .empty-state h2 { font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 600; color: var(--ink-2); margin-bottom: 6px; }
  .empty-state p { font-size: 14px; color: var(--ink-3); }


  .site-footer { border-top: 1px solid var(--border); background: var(--surface); padding: 20px 40px; text-align: center; position: relative; z-index: 1; }
  .site-footer p { font-family: 'JetBrains Mono', monospace; font-size: 11.5px; color: var(--ink-3); letter-spacing: 0.02em; }


  @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }


  @media (max-width: 700px) {
    .header-inner, .hero, .controls-bar, .content-area { padding-left: 16px; padding-right: 16px; }
    .hero { padding: 32px 16px 28px; }
    .hero-inner { flex-direction: column; gap: 20px; }
    .hero-stats { display: none; }
    h1 { font-size: 24px; }
    .header-inner { height: auto; padding: 12px 16px; }
    .brand img { height: 32px; }
    .brand-name { font-size: 13px; }
    .brand-sub, .brand-divider { display: none; }
    .btn-admin { padding: 7px 12px; font-size: 11px; }
    .controls-bar { flex-direction: column; align-items: flex-start; gap: 12px; }
    .cal-day { min-height: 70px; padding: 5px 3px 3px; }
    .cal-day-num { font-size: 11px; width: 20px; height: 20px; }
    .cal-wd { font-size: 9px; padding: 7px 2px; }
    .cal-event-chip { font-size: 9px; padding: 1px 3px; }
    .event-panel { width: 100vw; border-left: none; border-top: 1px solid var(--border); top: auto; border-radius: var(--radius) var(--radius) 0 0; }
    .event-card { grid-template-columns: 58px 1fr; border-radius: var(--radius-sm); }
    .ev-type { display: none; }
    .ev-date { padding: 14px 10px 14px 14px; }
    .ev-day { font-size: 18px; }
    .ev-body { padding: 13px 14px; }
    .ev-title { font-size: 14px; }
    .site-footer { padding: 16px 20px; }
    .site-footer p { font-size: 10.5px; }
  }
</style>
</head>
<body>


<header class="site-header">
  <div class="header-inner">
    <div class="brand">
      <img src="logo.png" alt="ADBU Logo">
      <div class="brand-divider"></div>
      <div>
        <span class="brand-name">Assam Don Bosco University</span>
        <span class="brand-sub">Academic Registry &amp; Events</span>
      </div>
    </div>
    <a href="login.php" class="btn-admin">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
        <polyline points="10 17 15 12 10 7"/>
        <line x1="15" y1="12" x2="3" y2="12"/>
      </svg>
      Admin Login
    </a>
  </div>
</header>


<div class="hero">
  <div class="hero-inner">
    <div class="hero-left">
      <div class="hero-tag">Academic Calendar <?= date('Y') ?></div>
      <h1>University <em>Events</h1>
      <p class="hero-desc">Browse scheduled examinations, holidays, and academic occasions for the current year.</p>
    </div>
    <div class="hero-stats">
      <div class="stat-card">
        <div class="stat-dot" style="background:#10b981"></div>
        <div class="stat-body"><div class="stat-num"><?= $totalEvents ?></div><div class="stat-lbl">Total events</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-dot" style="background:#3b82f6"></div>
        <div class="stat-body"><div class="stat-num"><?= $upcoming ?></div><div class="stat-lbl">Upcoming</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-dot" style="background:#f59e0b"></div>
        <div class="stat-body"><div class="stat-num"><?= $monthCount ?></div><div class="stat-lbl">Months</div></div>
      </div>
    </div>
  </div>
</div>


<div class="controls-bar">
  <div class="legend">
    <span class="legend-label">Type</span>
    <?php foreach($typeConfig as $type => $c): ?>
      <span class="type-pill" style="color:<?= $c['color'] ?>;background:<?= $c['bg'] ?>;border-color:<?= $c['border'] ?>">
        <span style="background:<?= $c['dot'] ?>;border-radius:50%;width:6px;height:6px;display:inline-block;flex-shrink:0;"></span>
        <?= $type ?>
      </span>
    <?php endforeach; ?>
  </div>
  <div class="view-toggle">
    <button class="view-btn active" id="btnCalendar" onclick="setView('calendar')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      Calendar
    </button>
    <button class="view-btn" id="btnList" onclick="setView('list')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
        <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
      </svg>
      List
    </button>
  </div>
</div>


<div class="content-area">

  <div id="calendarView">
    <div class="cal-nav">
      <div class="cal-nav-btns">
        <button class="cal-nav-btn" onclick="prevMonth()" title="Previous month">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button class="cal-nav-btn" onclick="nextMonth()" title="Next month">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>
      <span class="cal-month-title" id="calMonthTitle"></span>
      <button class="cal-today-btn" onclick="goToToday()">Today</button>
    </div>
    <div class="cal-grid">
      <div class="cal-weekdays">
        <?php foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $wd): ?>
          <div class="cal-wd"><?= $wd ?></div>
        <?php endforeach; ?>
      </div>
      <div class="cal-days" id="calDays"></div>
    </div>
  </div>

  <div id="listView">
    <?php if(empty($events)): ?>
      <div class="empty-state">
        <div class="empty-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <h2>No Events Recorded</h2>
        <p>The register contains no scheduled events at this time.</p>
      </div>
    <?php else: ?>
      <?php foreach($grouped as $month => $monthEvents): ?>
      <div class="month-section">
        <div class="month-header">
          <span class="month-label"><?= $month ?></span>
          <div class="month-divider"></div>
          <span class="month-badge"><?= count($monthEvents) ?> <?= count($monthEvents) === 1 ? 'event' : 'events' ?></span>
        </div>
        <div class="event-list">
          <?php foreach($monthEvents as $e):
            $c  = $typeConfig[$e['type']] ?? $typeConfig['Academic'];
            $ts = strtotime($e['event_date']);
            $isPast = $ts < strtotime('today');
          ?>
          <div class="event-card" style="--ev-dot:<?= $c['dot'] ?>;<?= $isPast ? 'opacity:0.6;' : '' ?>">
            <div class="ev-date">
              <span class="ev-day"><?= date('j', $ts) ?></span>
              <span class="ev-wd"><?= date('D', $ts) ?></span>
            </div>
            <div class="ev-body">
              <div class="ev-title"><?= htmlspecialchars($e['title']) ?></div>
              <?php if(!empty($e['description'])): ?>
                <div class="ev-desc"><?= htmlspecialchars($e['description']) ?></div>
              <?php endif; ?>
            </div>
            <div class="ev-type">
              <span class="ev-badge" style="color:<?= $c['color'] ?>;background:<?= $c['bg'] ?>;border-color:<?= $c['border'] ?>">
                <span class="ev-badge-dot" style="background:<?= $c['dot'] ?>"></span>
                <?= htmlspecialchars($e['type']) ?>
              </span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

<div class="event-panel-overlay" id="panelOverlay" onclick="closePanel()"></div>
<div class="event-panel" id="eventPanel" style="display:none;">
  <div class="event-panel-header">
    <span class="event-panel-date" id="panelDate"></span>
    <button class="event-panel-close" onclick="closePanel()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="event-panel-body" id="panelBody"></div>
</div>

<script>
const allEvents = <?= json_encode($byDate) ?>;
const typeConfig = <?= json_encode($typeConfig) ?>;


function setView(v) {
  document.getElementById('calendarView').style.display = v === 'calendar' ? 'block' : 'none';
  document.getElementById('listView').style.display     = v === 'list'     ? 'block' : 'none';
  document.getElementById('btnCalendar').classList.toggle('active', v === 'calendar');
  document.getElementById('btnList').classList.toggle('active', v === 'list');
  localStorage.setItem('adbu_view', v);
}

const savedView = localStorage.getItem('adbu_view') || 'calendar';
setView(savedView);


const today = new Date();
let curYear  = today.getFullYear();
let curMonth = today.getMonth(); // 0-indexed

function pad(n) { return String(n).padStart(2,'0'); }

function renderCalendar() {
  const firstDay  = new Date(curYear, curMonth, 1);
  const lastDay   = new Date(curYear, curMonth + 1, 0);
  const startDow  = firstDay.getDay(); // 0=Sun
  const totalDays = lastDay.getDate();

  document.getElementById('calMonthTitle').textContent =
    firstDay.toLocaleString('default', { month: 'long', year: 'numeric' });

  const container = document.getElementById('calDays');
  container.innerHTML = '';

  
  const totalCells = Math.ceil((startDow + totalDays) / 7) * 7;

  for (let i = 0; i < totalCells; i++) {
    const dayNum   = i - startDow + 1;
    const isThisMonth = dayNum >= 1 && dayNum <= totalDays;
    const isToday  = isThisMonth &&
      dayNum === today.getDate() &&
      curMonth === today.getMonth() &&
      curYear  === today.getFullYear();

  
    let cellDate, displayNum;
    if (!isThisMonth) {
      if (dayNum < 1) {
      
        const d = new Date(curYear, curMonth, dayNum);
        cellDate   = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
        displayNum = d.getDate();
      } else {
     
        const d = new Date(curYear, curMonth + 1, dayNum - totalDays);
        cellDate   = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
        displayNum = d.getDate();
      }
    } else {
      cellDate   = `${curYear}-${pad(curMonth+1)}-${pad(dayNum)}`;
      displayNum = dayNum;
    }

    const dayEvents = allEvents[cellDate] || [];
    const isLastRow = i >= totalCells - 7;
    const hasEvents = dayEvents.length > 0;

    const cell = document.createElement('div');
    cell.className = `cal-day${!isThisMonth ? ' other-month' : ''}${isToday ? ' today' : ''}${isLastRow ? ' last-row' : ''}${hasEvents ? ' has-events' : ''}`;
    if (hasEvents) cell.onclick = () => openPanel(cellDate, dayEvents);

    const numEl = document.createElement('div');
    numEl.className = 'cal-day-num';
    numEl.textContent = displayNum;
    cell.appendChild(numEl);

    if (dayEvents.length > 0) {
      const evWrap = document.createElement('div');
      evWrap.className = 'cal-events';

      const maxShow = 3;
      dayEvents.slice(0, maxShow).forEach(ev => {
        const cfg = typeConfig[ev.type] || typeConfig['Academic'];
        const chip = document.createElement('div');
        chip.className = 'cal-event-chip';
        chip.style.background  = cfg.bg;
        chip.style.color       = cfg.color;
        chip.style.borderColor = cfg.dot;
        chip.textContent = ev.title;
        evWrap.appendChild(chip);
      });

      if (dayEvents.length > maxShow) {
        const more = document.createElement('div');
        more.className = 'cal-more';
        more.textContent = `+${dayEvents.length - maxShow} more`;
        evWrap.appendChild(more);
      }

      cell.appendChild(evWrap);
    }

    container.appendChild(cell);
  }
}

function prevMonth() {
  curMonth--;
  if (curMonth < 0) { curMonth = 11; curYear--; }
  renderCalendar();
}

function nextMonth() {
  curMonth++;
  if (curMonth > 11) { curMonth = 0; curYear++; }
  renderCalendar();
}

function goToToday() {
  curYear  = today.getFullYear();
  curMonth = today.getMonth();
  renderCalendar();
}


function openPanel(dateStr, events) {
  const d = new Date(dateStr + 'T00:00:00');
  const label = d.toLocaleDateString('default', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
  document.getElementById('panelDate').textContent = label;

  const body = document.getElementById('panelBody');
  body.innerHTML = '';

  events.forEach(ev => {
    const cfg = typeConfig[ev.type] || typeConfig['Academic'];
    const card = document.createElement('div');
    card.className = 'panel-event-card';
    card.style.borderLeftColor = cfg.dot;

    card.innerHTML = `
      <div class="panel-event-title">${escHtml(ev.title)}</div>
      <span class="panel-event-badge" style="color:${cfg.color};background:${cfg.bg};border-color:${cfg.border}">
        <span class="panel-event-badge-dot" style="background:${cfg.dot}"></span>
        ${escHtml(ev.type)}
      </span>
      ${ev.description ? `<div class="panel-event-desc">${escHtml(ev.description)}</div>` : ''}
    `;
    body.appendChild(card);
  });

  document.getElementById('panelOverlay').style.display = 'block';
  document.getElementById('eventPanel').style.display   = 'flex';
  document.getElementById('eventPanel').style.flexDirection = 'column';
}

function closePanel() {
  document.getElementById('panelOverlay').style.display = 'none';
  document.getElementById('eventPanel').style.display   = 'none';
}

function escHtml(str) {
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closePanel(); });


renderCalendar();
</script>


<footer class="site-footer">
  <p>Assam Don Bosco University &nbsp;&middot;&nbsp; Academic Registry &nbsp;&middot;&nbsp; <?= date('Y') ?> &nbsp;&middot;&nbsp; All dates subject to revision by the Academic Board</p>
</footer>

</body>
</html>