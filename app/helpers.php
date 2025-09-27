<?php

use Illuminate\Support\Str;

if (!function_exists('asset_url')) {
    /**
     * Resolve a URL for a file path that may be stored on the public disk or be a plain public asset path.
     * - If path starts with http(s):// return as is.
     * - If path already starts with storage/ assume it's a public disk path.
     * - Otherwise treat as a path under public/.
     */
    function asset_url(?string $path, ?string $default = null): string
    {
        $p = $path ?: '';
        if ($p === '') {
            return $default ? asset($default) : asset('');
        }
        if (Str::startsWith($p, ['http://', 'https://'])) {
            return $p;
        }
        if (Str::startsWith($p, ['storage/', '/storage/'])) {
            return asset(ltrim($p, '/'));
        }
        // Heuristic: if it contains a directory we uploaded to (settings/...), it's on public disk
        if (Str::startsWith($p, ['settings/', '/settings/'])) {
            return asset('storage/' . ltrim($p, '/'));
        }
        return asset(ltrim($p, '/'));
    }
}

