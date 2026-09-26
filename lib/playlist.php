<?php
declare(strict_types=1);

function countryNames(): array
{
    return [
        'FR' => 'France',
        'TN' => 'Tunisie',
        'LU' => 'Luxembourg',
        'MA' => 'Maroc',
        'DZ' => 'Algérie',
        'BE' => 'Belgique',
        'DE' => 'Allemagne',
        'ES' => 'Espagne',
        'IT' => 'Italie',
        'PT' => 'Portugal',
        'GB' => 'Royaume-Uni',
        'US' => 'États-Unis',
        'CA' => 'Canada',
        'CH' => 'Suisse',
        'TR' => 'Turquie',
        'EG' => 'Égypte',
        'SA' => 'Arabie saoudite',
        'AE' => 'Émirats arabes unis',
        'LY' => 'Libye',
        'ZZ' => 'Pays non identifié'
    ];
}

function normalizeText(string $text): string
{
    $text = mb_strtolower($text, 'UTF-8');
    $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    if ($ascii !== false) {
        $text = $ascii;
    }
    return trim(preg_replace('/[^a-z0-9]+/i', ' ', $text) ?? $text);
}

function detectCountry(string $extinf, string $fallback = 'ZZ'): string
{
    if (preg_match('/tvg-country\s*=\s*"([^"]+)"/i', $extinf, $m)) {
        $code = strtoupper(trim(preg_split('/[,;|\/]/', $m[1])[0] ?? ''));
        if (preg_match('/^[A-Z]{2}$/', $code)) {
            return $code;
        }
    }

    $aliases = [
        'luxembourg'=>'LU','tunisia'=>'TN','tunisie'=>'TN','france'=>'FR',
        'morocco'=>'MA','maroc'=>'MA','algeria'=>'DZ','algerie'=>'DZ',
        'belgium'=>'BE','belgique'=>'BE','germany'=>'DE','allemagne'=>'DE',
        'spain'=>'ES','espagne'=>'ES','italy'=>'IT','italie'=>'IT',
        'portugal'=>'PT','united kingdom'=>'GB','royaume uni'=>'GB',
        'united states'=>'US','etats unis'=>'US','usa'=>'US',
        'canada'=>'CA','switzerland'=>'CH','suisse'=>'CH',
        'turkey'=>'TR','turquie'=>'TR','egypt'=>'EG','egypte'=>'EG',
        'saudi arabia'=>'SA','arabie saoudite'=>'SA',
        'united arab emirates'=>'AE','emirats arabes unis'=>'AE',
        'libya'=>'LY','libye'=>'LY'
    ];

    $haystack = ' ' . normalizeText($extinf) . ' ';
    foreach ($aliases as $alias => $code) {
        $needle = ' ' . normalizeText($alias) . ' ';
        if (str_contains($haystack, $needle)) {
            return $code;
        }
    }

    $fallback = strtoupper($fallback);
    return preg_match('/^[A-Z]{2}$/', $fallback) ? $fallback : 'ZZ';
}

function parsePlaylist(string $content, string $fallback = 'ZZ'): array
{
    $lines = preg_split('/\R/', $content) ?: [];
    $entries = [];

    for ($i = 0; $i < count($lines); $i++) {
        $info = trim($lines[$i]);
        if (!str_starts_with($info, '#EXTINF:')) {
            continue;
        }

        $url = '';
        for ($j = $i + 1; $j < count($lines); $j++) {
            $candidate = trim($lines[$j]);
            if ($candidate === '') {
                continue;
            }
            if (str_starts_with($candidate, '#')) {
                continue;
            }
            $url = $candidate;
            $i = $j;
            break;
        }

        if ($url === '' || !preg_match('#^https?://#i', $url)) {
            continue;
        }

        $country = detectCountry($info, $fallback);
        $entries[] = ['country' => $country, 'info' => $info, 'url' => $url];
    }

    return $entries;
}

function savePlaylists(array $entries, string $directory): array
{
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    $groups = [];
    foreach ($entries as $entry) {
        $groups[$entry['country']][] = $entry;
    }

    $stats = [];
    foreach ($groups as $country => $items) {
        $path = $directory . '/' . strtolower($country) . '.m3u';
        $known = [];

        if (is_file($path)) {
            foreach (preg_split('/\R/', (string) file_get_contents($path)) ?: [] as $line) {
                $line = trim($line);
                if (preg_match('#^https?://#i', $line)) {
                    $known[$line] = true;
                }
            }
        }

        $blocks = [];
        foreach ($items as $item) {
            if (isset($known[$item['url']])) {
                continue;
            }
            $known[$item['url']] = true;
            $blocks[] = $item['info'] . "\n" . $item['url'];
        }

        if ($blocks) {
            $prefix = is_file($path) && filesize($path) > 0 ? "\n" : "#EXTM3U\n";
            file_put_contents($path, $prefix . implode("\n", $blocks) . "\n", FILE_APPEND | LOCK_EX);
        }

        $stats[$country] = ['added' => count($blocks), 'total' => count($known)];
    }

    ksort($stats);
    return $stats;
}

function listPlaylists(string $directory): array
{
    $names = countryNames();
    $rows = [];

    foreach (glob($directory . '/*.m3u') ?: [] as $file) {
        $code = strtoupper(pathinfo($file, PATHINFO_FILENAME));
        $count = 0;
        foreach (preg_split('/\R/', (string) file_get_contents($file)) ?: [] as $line) {
            if (preg_match('#^https?://#i', trim($line))) {
                $count++;
            }
        }
        $rows[] = [
            'code' => $code,
            'name' => $names[$code] ?? $code,
            'count' => $count,
            'file' => 'countries/' . basename($file)
        ];
    }

    usort($rows, fn(array $a, array $b): int => strcasecmp($a['name'], $b['name']));
    return $rows;
}
