<?php

declare(strict_types=1);

use Core\UI\View;

$router->add('GET', '/', fn() => View::render('Home', '
<div class="header"><h2 class="title">Welcome to NovaCore</h2></div>
<div class="card">
  <p>NovaCore UI module is enabled with complete layouts and screens.</p>
  <p class="muted">Use the left navigation to move between modules.</p>
</div>
', 'dashboard'));

$router->add('GET', '/dashboard', fn() => View::render('Dashboard', '
<div class="header"><h2 class="title">Dashboard</h2><span class="muted">Last updated just now</span></div>
<div class="grid">
  <div class="card"><h3>Active Users</h3><p><strong>1,284</strong></p></div>
  <div class="card"><h3>Open Notifications</h3><p><strong>37</strong></p></div>
  <div class="card"><h3>Media Files</h3><p><strong>5,902</strong></p></div>
  <div class="card"><h3>Audit Events</h3><p><strong>20,149</strong></p></div>
</div>
', 'dashboard'));

$router->add('GET', '/users', fn() => View::render('User Management', '
<div class="header"><h2 class="title">Users</h2></div>
<table><thead><tr><th>Name</th><th>Role</th><th>Status</th></tr></thead><tbody>
<tr><td>Alex Carter</td><td>Admin</td><td>Active</td></tr>
<tr><td>Jamie Diaz</td><td>Editor</td><td>Pending</td></tr>
<tr><td>Priya Singh</td><td>Viewer</td><td>Active</td></tr>
</tbody></table>
', 'users'));

$router->add('GET', '/media', fn() => View::render('Media Library', '
<div class="header"><h2 class="title">Media Library</h2></div>
<div class="grid">
  <div class="card"><h3>Brand Assets</h3><p class="muted">142 files</p></div>
  <div class="card"><h3>Campaigns</h3><p class="muted">91 files</p></div>
  <div class="card"><h3>User Uploads</h3><p class="muted">5,669 files</p></div>
</div>
', 'media'));

$router->add('GET', '/notifications', fn() => View::render('Notifications', '
<div class="header"><h2 class="title">Notifications</h2></div>
<div class="card"><h3>Queue Health</h3><p>Email: 12 pending · Push: 3 pending · SMS: 0 pending</p></div>
', 'notifications'));

$router->add('GET', '/settings', fn() => View::render('Settings', '
<div class="header"><h2 class="title">Settings</h2></div>
<div class="grid">
  <div class="card"><h3>General</h3><p class="muted">Locale, timezone, branding.</p></div>
  <div class="card"><h3>Security</h3><p class="muted">Password policy, 2FA, sessions.</p></div>
  <div class="card"><h3>Integrations</h3><p class="muted">API keys and webhooks.</p></div>
</div>
', 'settings'));

$router->add('GET', '/audit-logs', fn() => View::render('Audit Logs', '
<div class="header"><h2 class="title">Audit Logs</h2></div>
<table><thead><tr><th>When</th><th>Actor</th><th>Action</th></tr></thead><tbody>
<tr><td>2026-05-26 12:32</td><td>system</td><td>Cache rebuilt</td></tr>
<tr><td>2026-05-26 12:14</td><td>alex.carter</td><td>Updated role permissions</td></tr>
<tr><td>2026-05-26 11:58</td><td>jamie.diaz</td><td>Uploaded hero-banner.png</td></tr>
</tbody></table>
', 'audit'));
