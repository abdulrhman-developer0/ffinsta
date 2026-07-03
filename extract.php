<?php
$strings = [];

function scanDirRegex($dir) {
    global $strings;
    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iter as $file) {
        if ($file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            preg_match_all('/__\([\'](.*?)[\']\)/', $content, $matches);
            foreach ($matches[1] as $m) {
                $strings[$m] = $m;
            }
            preg_match_all('/__\(["](.*?)["]\)/', $content, $matches);
            foreach ($matches[1] as $m) {
                $strings[$m] = $m;
            }
        }
    }
}

scanDirRegex('resources/views');
scanDirRegex('app/Http/Controllers');
scanDirRegex('app/Services');
scanDirRegex('app/Models');

// Load existing translations if any
$existing = [];
if (file_exists('lang/ar.json')) {
    $existing = json_decode(file_get_contents('lang/ar.json'), true) ?: [];
}

$merged = array_merge($strings, $existing);
ksort($merged);

file_put_contents('lang/ar.json', json_encode((object)$merged, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
echo "Extracted " . count($strings) . " unique strings to lang/ar.json.\n";
