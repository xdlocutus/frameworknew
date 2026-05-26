<?php

declare(strict_types=1);

namespace Core\UI;

final class View
{
    public static function render(string $title, string $content, string $active = 'dashboard', ?string $currentUser = null): string
    {
        $navigation = [
            'dashboard' => ['/dashboard', 'Dashboard'],
            'users' => ['/users', 'Users'],
            'media' => ['/media', 'Media Library'],
            'notifications' => ['/notifications', 'Notifications'],
            'settings' => ['/settings', 'Settings'],
            'audit' => ['/audit-logs', 'Audit Logs'],
        ];

        $navItems = '';
        foreach ($navigation as $key => [$path, $label]) {
            $class = $key === $active ? 'nav-link active' : 'nav-link';
            $navItems .= sprintf('<a class="%s" href="%s">%s</a>', $class, $path, $label);
        }

        $authSection = '';
        if ($currentUser !== null) {
            $safeUser = htmlspecialchars($currentUser, ENT_QUOTES, 'UTF-8');
            $authSection = <<<HTML
<div class="auth-box">
  <span class="muted">Signed in as {$safeUser}</span>
  <form method="POST" action="/logout">
    <button type="submit" class="btn btn-secondary">Logout</button>
  </form>
</div>
HTML;
        }

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$title} · NovaCore UI</title>
  <style>
    :root { color-scheme: light; }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Inter, Segoe UI, Roboto, sans-serif; background: #f5f7fb; color: #1f2937; }
    .app { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
    .sidebar { background: #111827; color: #e5e7eb; padding: 24px 16px; }
    .brand { font-size: 20px; font-weight: 700; margin: 0 0 20px; }
    .nav { display: grid; gap: 8px; }
    .nav-link { color: #d1d5db; text-decoration: none; padding: 10px 12px; border-radius: 8px; }
    .nav-link:hover { background: #1f2937; }
    .nav-link.active { background: #2563eb; color: white; }
    .main { padding: 28px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
    .title { margin: 0; font-size: 26px; }
    .grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); }
    .card { background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
    th, td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { background: #f9fafb; }
    .muted { color: #6b7280; }
    .auth-box { display: grid; gap: 10px; margin-top: 20px; }
    .btn { border: 0; border-radius: 8px; padding: 8px 12px; cursor: pointer; }
    .btn-secondary { background: #374151; color: #f3f4f6; }
    @media (max-width: 900px) { .app { grid-template-columns: 1fr; } .sidebar { position: sticky; top: 0; z-index: 1; } }
  </style>
</head>
<body>
  <div class="app">
    <aside class="sidebar">
      <h1 class="brand">NovaCore Admin</h1>
      <nav class="nav">{$navItems}</nav>
      {$authSection}
    </aside>
    <main class="main">
      {$content}
    </main>
  </div>
</body>
</html>
HTML;
    }
}
