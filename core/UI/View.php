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
            $navItems .= sprintf('<a class="nav-link" href="%s"><span>%s</span><i>↗</i></a>', htmlspecialchars($item['path'], ENT_QUOTES, 'UTF-8'), htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'));
        }

        $authSection = '';
        if ($currentUser !== null) {
            $safeUser = htmlspecialchars($currentUser, ENT_QUOTES, 'UTF-8');
            $authSection = "<p class=\"profile-pill\">Signed in as {$safeUser}</p>";
        }

        return <<<HTML
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{$title} · NovaCore</title>
<style>
*{box-sizing:border-box}body{margin:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,sans-serif;color:#dbeafe;background:radial-gradient(circle at 10% 10%,#1e3a8a 0%,#0b1022 35%,#020617 100%)}
.app{display:grid;grid-template-columns:320px minmax(0,1fr);min-height:100vh}
.sidebar{background:linear-gradient(180deg,rgba(15,23,42,.95),rgba(2,6,23,.97));backdrop-filter:blur(8px);border-right:1px solid rgba(148,163,184,.2);padding:26px 18px;position:sticky;top:0;height:100vh}
.brand{font-weight:800;font-size:1.08rem;letter-spacing:.02em;background:linear-gradient(120deg,#93c5fd,#a78bfa 55%,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent}
.brand-sub{font-size:.73rem;color:#94a3b8;margin-top:8px;line-height:1.45}
.nav{display:grid;gap:8px;margin-top:22px}
.nav-link{display:flex;justify-content:space-between;align-items:center;text-decoration:none;padding:11px 12px;border-radius:12px;background:rgba(15,23,42,.5);border:1px solid rgba(59,130,246,.1);color:#bfdbfe;font-size:.9rem;transition:all .2s ease}
.nav-link:hover{transform:translateY(-1px);background:rgba(30,41,59,.8);border-color:rgba(96,165,250,.45);box-shadow:0 10px 20px rgba(15,23,42,.35);color:#fff}
.nav-link i{font-style:normal;opacity:.7;font-size:.8rem}
.profile-pill{margin-top:20px;padding:10px 12px;border-radius:12px;background:rgba(30,41,59,.7);border:1px solid rgba(148,163,184,.3);font-size:.75rem;color:#cbd5e1}
.main{padding:30px 28px}
.page-shell{max-width:1240px;margin:0 auto;padding:20px;border-radius:24px;background:linear-gradient(160deg,rgba(15,23,42,.8),rgba(15,23,42,.45));border:1px solid rgba(148,163,184,.25);box-shadow:0 30px 70px rgba(2,6,23,.5)}
.stack{display:grid;gap:20px}.metrics{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(160px,1fr))}
.metric{background:linear-gradient(160deg,rgba(30,41,59,.85),rgba(15,23,42,.95));border:1px solid rgba(96,165,250,.22);border-radius:14px;padding:14px 16px;box-shadow:inset 0 1px 0 rgba(255,255,255,.08),0 12px 25px rgba(15,23,42,.35)}
.metric-label{font-size:.72rem;color:#93c5fd;text-transform:uppercase;letter-spacing:.05em}.metric-value{font-size:1.25rem;font-weight:700;margin-top:2px;color:#f8fafc}
.text-sm{font-size:.875rem}.text-xs{font-size:.75rem}.text-slate-500{color:#93c5fd}.text-slate-700{color:#bfdbfe}.text-slate-900{color:#f8fafc}.font-semibold{font-weight:600}
.rounded-2xl{border-radius:1rem}.rounded-xl{border-radius:.75rem}.border{border:1px solid rgba(148,163,184,.25)}.border-slate-200{border-color:rgba(148,163,184,.25)}
.bg-white{background:linear-gradient(165deg,rgba(30,41,59,.78),rgba(15,23,42,.98))}.bg-slate-50{background:rgba(15,23,42,.55)}.p-6{padding:1.5rem}.mb-4{margin-bottom:1rem}.shadow-sm{box-shadow:0 14px 30px rgba(2,6,23,.45)}
.overflow-hidden{overflow:hidden}.min-w-full{min-width:100%}.divide-y tr+tr td{border-top:1px solid rgba(148,163,184,.15)}.px-4{padding-left:1rem;padding-right:1rem}.py-3{padding-top:.75rem;padding-bottom:.75rem}.py-10{padding-top:2.5rem;padding-bottom:2.5rem}
.text-left{text-align:left}.text-center{text-align:center}.tracking-wide{letter-spacing:.03em}.uppercase{text-transform:uppercase}
@media (max-width: 960px){.app{grid-template-columns:1fr}.sidebar{position:relative;height:auto}.main{padding:16px}.page-shell{padding:14px;border-radius:16px}}
</style></head>
<body><div class="app"><aside class="sidebar"><div class="brand">NovaCore Platform</div><div class="brand-sub">Modular Framework Kernel</div><nav class="nav">{$navItems}</nav>{$authSection}</aside><main class="main"><div class="page-shell">{$content}</div></main></div></body></html>
HTML;
    }
}
