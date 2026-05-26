<?php

declare(strict_types=1);

use Core\UI\View;

$authenticate = static function (): ?array {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        return null;
    }

    /** @var array{name:string,email:string} */
    return $_SESSION['user'];
};

$renderLogin = static function (?string $error = null): string {
    $errorBlock = $error ? sprintf('<p style="color:#b91c1c;">%s</p>', htmlspecialchars($error, ENT_QUOTES, 'UTF-8')) : '';

    return <<<HTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login · NovaCore UI</title>
  <style>
    body { margin: 0; font-family: Inter, Segoe UI, Roboto, sans-serif; background: #0f172a; color: #e2e8f0; display:grid; place-items:center; min-height:100vh; }
    .card { width:min(420px,90vw); background:#111827; border:1px solid #374151; border-radius:12px; padding:24px; }
    label { display:block; margin:10px 0 4px; font-size:14px; }
    input { width:100%; padding:10px; border-radius:8px; border:1px solid #4b5563; background:#1f2937; color:#f9fafb; }
    button { width:100%; margin-top:14px; padding:10px; border:0; border-radius:8px; background:#2563eb; color:white; cursor:pointer; }
    .hint { color:#94a3b8; font-size:13px; margin-top:8px; }
  </style>
</head>
<body>
  <div class="card">
    <h2>NovaCore Admin Login</h2>
    {$errorBlock}
    <form method="POST" action="/login">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required />
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required />
      <button type="submit">Sign in</button>
    </form>
    <p class="hint">Demo credentials: admin@novacore.local / admin123</p>
  </div>
</body>
</html>
HTML;
};

$router->add('GET', '/', function () {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        return '';
    }

    header('Location: /dashboard');
    return '';
});

$router->add('GET', '/login', function () use ($renderLogin) {
    if (isset($_SESSION['user'])) {
        header('Location: /dashboard');
        return '';
    }

    return $renderLogin();
});

$router->add('POST', '/login', function () use ($renderLogin) {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    $validEmail = strtolower((string) env('ADMIN_EMAIL', 'admin@novacore.local'));
    $validPassword = (string) env('ADMIN_PASSWORD', 'admin123');

    if ($email !== $validEmail || $password !== $validPassword) {
        http_response_code(422);
        return $renderLogin('Invalid email or password.');
    }

    $_SESSION['user'] = ['name' => 'Administrator', 'email' => $validEmail];
    header('Location: /dashboard');
    return '';
});

$router->add('POST', '/logout', function () {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
    header('Location: /login');
    return '';
});

$router->add('GET', '/dashboard', fn() => ($user = $authenticate()) ? View::render('Dashboard', '
<div class="header"><h2 class="title">Dashboard</h2><span class="muted">Last updated just now</span></div>
<div class="grid">
  <div class="card"><h3>Active Users</h3><p><strong>1,284</strong></p></div>
  <div class="card"><h3>Open Notifications</h3><p><strong>37</strong></p></div>
  <div class="card"><h3>Media Files</h3><p><strong>5,902</strong></p></div>
  <div class="card"><h3>Audit Events</h3><p><strong>20,149</strong></p></div>
</div>
', 'dashboard', $user['name']) : '');

$router->add('GET', '/users', fn() => ($user = $authenticate()) ? View::render('User Management', '
<div class="header"><h2 class="title">Users</h2></div>
<table><thead><tr><th>Name</th><th>Role</th><th>Status</th></tr></thead><tbody>
<tr><td>Alex Carter</td><td>Admin</td><td>Active</td></tr>
<tr><td>Jamie Diaz</td><td>Editor</td><td>Pending</td></tr>
<tr><td>Priya Singh</td><td>Viewer</td><td>Active</td></tr>
</tbody></table>
', 'users', $user['name']) : '');

$router->add('GET', '/media', fn() => ($user = $authenticate()) ? View::render('Media Library', '
<div class="header"><h2 class="title">Media Library</h2></div>
<div class="grid">
  <div class="card"><h3>Brand Assets</h3><p class="muted">142 files</p></div>
  <div class="card"><h3>Campaigns</h3><p class="muted">91 files</p></div>
  <div class="card"><h3>User Uploads</h3><p class="muted">5,669 files</p></div>
</div>
', 'media', $user['name']) : '');

$router->add('GET', '/notifications', fn() => ($user = $authenticate()) ? View::render('Notifications', '
<div class="header"><h2 class="title">Notifications</h2></div>
<div class="card"><h3>Queue Health</h3><p>Email: 12 pending · Push: 3 pending · SMS: 0 pending</p></div>
', 'notifications', $user['name']) : '');

$router->add('GET', '/settings', fn() => ($user = $authenticate()) ? View::render('Settings', '
<div class="header"><h2 class="title">Settings</h2></div>
<div class="grid">
  <div class="card"><h3>General</h3><p class="muted">Locale, timezone, branding.</p></div>
  <div class="card"><h3>Security</h3><p class="muted">Password policy, 2FA, sessions.</p></div>
  <div class="card"><h3>Integrations</h3><p class="muted">API keys and webhooks.</p></div>
</div>
', 'settings', $user['name']) : '');

$router->add('GET', '/audit-logs', fn() => ($user = $authenticate()) ? View::render('Audit Logs', '
<div class="header"><h2 class="title">Audit Logs</h2></div>
<table><thead><tr><th>When</th><th>Actor</th><th>Action</th></tr></thead><tbody>
<tr><td>2026-05-26 12:32</td><td>system</td><td>Cache rebuilt</td></tr>
<tr><td>2026-05-26 12:14</td><td>alex.carter</td><td>Updated role permissions</td></tr>
<tr><td>2026-05-26 11:58</td><td>jamie.diaz</td><td>Uploaded hero-banner.png</td></tr>
</tbody></table>
', 'audit', $user['name']) : '');
