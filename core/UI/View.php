<?php

declare(strict_types=1);

namespace Core\UI;

final class View
{
    /** @param array<int,array{label:string,path:string}> $navigation */
    public static function render(string $title, string $content, array $navigation = [], ?string $currentUser = null): string
    {
        $navItems = '';
        foreach ($navigation as $item) {
            $navItems .= sprintf('<a class="nav-link" href="%s">%s</a>', htmlspecialchars($item['path'], ENT_QUOTES, 'UTF-8'), htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'));
        }

        $authSection = '';
        if ($currentUser !== null) {
            $safeUser = htmlspecialchars($currentUser, ENT_QUOTES, 'UTF-8');
            $authSection = "<p class=\"muted\">Signed in as {$safeUser}</p>";
        }

        return <<<HTML
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{$title} · NovaCore</title>
<style>
body{margin:0;font-family:Inter,Segoe UI,Roboto,sans-serif;background:#f5f7fb;color:#1f2937}.app{display:grid;grid-template-columns:240px 1fr;min-height:100vh}.sidebar{background:#111827;color:#e5e7eb;padding:20px}.brand{font-weight:700}.nav{display:grid;gap:8px;margin-top:16px}.nav-link{color:#d1d5db;text-decoration:none;padding:8px 10px;border-radius:8px}.nav-link:hover{background:#1f2937}.main{padding:24px}.card{background:white;border:1px solid #e5e7eb;border-radius:10px;padding:16px}.muted{color:#6b7280}
</style></head>
<body><div class="app"><aside class="sidebar"><div class="brand">NovaCore Framework</div><nav class="nav">{$navItems}</nav>{$authSection}</aside><main class="main">{$content}</main></div></body></html>
HTML;
    }
}
