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
            $navItems .= sprintf('<a class="group flex items-center rounded-xl px-3 py-2 text-sm text-slate-300 transition hover:bg-slate-800 hover:text-white" href="%s">%s</a>', htmlspecialchars($item['path'], ENT_QUOTES, 'UTF-8'), htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'));
        }

        $authSection = '';
        if ($currentUser !== null) {
            $safeUser = htmlspecialchars($currentUser, ENT_QUOTES, 'UTF-8');
            $authSection = "<p class=\"mt-6 border-t border-slate-700 pt-4 text-xs text-slate-400\">Signed in as {$safeUser}</p>";
        }

        return <<<HTML
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{$title} · NovaCore</title>
<style>
*{box-sizing:border-box}body{margin:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#f8fafc;color:#0f172a}.app{display:grid;grid-template-columns:280px minmax(0,1fr);min-height:100vh}.sidebar{background:#020617;color:#e2e8f0;padding:24px 18px;position:sticky;top:0;height:100vh}.brand{font-weight:700;font-size:1rem;letter-spacing:.01em}.brand-sub{font-size:.75rem;color:#94a3b8;margin-top:6px}.nav{display:grid;gap:6px;margin-top:22px}.main{padding:28px}.page-shell{max-width:1200px;margin:0 auto}.stack{display:grid;gap:20px}.metrics{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(160px,1fr))}.metric{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;box-shadow:0 1px 2px rgba(2,6,23,.06)}.metric-label{font-size:.75rem;color:#64748b;text-transform:uppercase}.metric-value{font-size:1.25rem;font-weight:700;margin-top:2px}.text-sm{font-size:.875rem}.text-xs{font-size:.75rem}.text-slate-500{color:#64748b}.text-slate-900{color:#0f172a}.font-semibold{font-weight:600}.rounded-2xl{border-radius:1rem}.rounded-xl{border-radius:.75rem}.border{border:1px solid #e2e8f0}.border-slate-200{border-color:#e2e8f0}.bg-white{background:#fff}.bg-slate-50{background:#f8fafc}.p-6{padding:1.5rem}.mb-4{margin-bottom:1rem}.shadow-sm{box-shadow:0 1px 2px rgba(2,6,23,.06)}.overflow-hidden{overflow:hidden}.min-w-full{min-width:100%}.divide-y tr+tr td{border-top:1px solid #f1f5f9}.px-4{padding-left:1rem;padding-right:1rem}.py-3{padding-top:.75rem;padding-bottom:.75rem}.py-10{padding-top:2.5rem;padding-bottom:2.5rem}.text-left{text-align:left}.text-center{text-align:center}.tracking-wide{letter-spacing:.03em}.uppercase{text-transform:uppercase}@media (max-width: 960px){.app{grid-template-columns:1fr}.sidebar{position:relative;height:auto}.main{padding:20px}}
</style></head>
<body><div class="app"><aside class="sidebar"><div class="brand">NovaCore Platform</div><div class="brand-sub">Modular Framework Kernel</div><nav class="nav">{$navItems}</nav>{$authSection}</aside><main class="main"><div class="page-shell">{$content}</div></main></div></body></html>
HTML;
    }
}
