<?php

declare(strict_types=1);

namespace Core\UI;

final class Components
{
    public static function card(string $title, string $body, ?string $description = null): string
    {
        $desc = $description ? '<p class="text-sm text-slate-500 mb-4">' . $description . '</p>' : '';
        return '<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">'
            . '<h2 class="text-lg font-semibold text-slate-900">' . $title . '</h2>'
            . $desc
            . $body
            . '</section>';
    }

    /** @param list<string> $headers @param list<string> $rows */
    public static function table(array $headers, array $rows): string
    {
        $head = implode('', array_map(static fn (string $header): string => '<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">' . $header . '</th>', $headers));
        $body = $rows !== [] ? implode('', $rows) : '<tr><td class="px-4 py-10 text-center text-slate-500" colspan="' . count($headers) . '">No records available.</td></tr>';

        return '<div class="overflow-hidden rounded-xl border border-slate-200"><table class="min-w-full divide-y divide-slate-200"><thead class="bg-slate-50"><tr>' . $head . '</tr></thead><tbody class="divide-y divide-slate-100 bg-white">' . $body . '</tbody></table></div>';
    }
}
