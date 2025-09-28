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
        // Heuristics for directories saved via FileUpload to the public disk (need /storage/ prefix when generating URL)
        $publicDiskDirs = [
            'settings/',
            '/settings/',
            'books/covers',
            '/books/covers',
            'books/images',
            '/books/images',
            'workshops/',
            '/workshops/',
            'literature_workshops/',
            '/literature_workshops/',
        ];
        foreach ($publicDiskDirs as $dir) {
            if (Str::startsWith($p, $dir)) {
                return asset('storage/' . ltrim($p, '/'));
            }
        }
        return asset(ltrim($p, '/'));
    }
}

