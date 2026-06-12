<?php

if (! function_exists('media_url')) {
    /**
     * Resolve a stored media path to a public URL.
     * Accepts either a storage-relative path (uploads) or a full
     * http(s) URL (seeded/demo data) and returns a usable URL.
     */
    function media_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
