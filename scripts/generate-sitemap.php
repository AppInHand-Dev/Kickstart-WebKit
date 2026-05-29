<?php
// scripts/generate-sitemap.php
define('BASE_URL', 'http://localhost/sites/kickstart-webkit');
define('APP_DATA_PATH', __DIR__ . '/../data');

$xmlPath = APP_DATA_PATH . '/page-routing.xml';
$outPath = __DIR__ . '/../sitemap.xml';
$date = date('Y-m-d');

if (!file_exists($xmlPath)) {
    echo "page-routing.xml non trovato\n";
    exit(1);
}

$Routing = simplexml_load_file($xmlPath);
$urls = [];

// build map: internal -> localized per lang
$localized = [];
foreach ($Routing->children() as $lang => $node) {
    foreach ($node->children() as $local => $internal) {
        $internalVal = trim((string)$internal);
        $localKey = trim((string)$local);
        if (!isset($localized[$internalVal])) $localized[$internalVal] = [];
        $localized[$internalVal][$lang] = $localKey;
    }
}

// ensure home exists for both langs
$langs = [];
foreach ($Routing->children() as $lang => $node) $langs[] = (string)$lang;

// add home for each lang
foreach ($langs as $lang) {
    $urls[] = [
        'loc' => rtrim(BASE_URL, '/') . '/' . $lang . '/',
        'lastmod' => $date,
        'changefreq' => 'daily',
        'priority' => '1.0',
        'alternates' => array_map(function($l){ return rtrim(BASE_URL,'/') . '/' . $l . '/'; }, $langs)
    ];
}

// add each internal slug found in localized map
foreach ($localized as $internal => $map) {
    foreach ($langs as $lang) {
        $localSlug = isset($map[$lang]) ? $map[$lang] : $internal;
        $loc = rtrim(BASE_URL, '/') . '/' . $lang . '/' . $localSlug . '/';
        // determine changefreq/priority heuristics
        $changefreq = in_array($internal, ['privacy','cookie','crediti','credits','sitemap']) ? 'yearly' : 'weekly';
        $priority = in_array($internal, ['privacy','cookie','crediti','credits','sitemap']) ? '0.2' : (strpos($internal,'item') === 0 ? '0.5' : '0.6');
        // build alternates
        $alternates = [];
        foreach ($langs as $l) {
            $altSlug = isset($map[$l]) ? $map[$l] : $internal;
            $alternates[$l] = rtrim(BASE_URL, '/') . '/' . $l . '/' . $altSlug . '/';
        }
        $urls[] = [
            'loc' => $loc,
            'lastmod' => $date,
            'changefreq' => $changefreq,
            'priority' => $priority,
            'alternates' => $alternates
        ];
    }
}

// write XML
$xml = new XMLWriter();
$xml->openMemory();
$xml->startDocument('1.0','UTF-8');
$xml->startElement('urlset');
$xml->writeAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
$xml->writeAttribute('xmlns:xhtml','http://www.w3.org/1999/xhtml');

foreach ($urls as $u) {
    $xml->startElement('url');
    $xml->writeElement('loc', $u['loc']);
    $xml->writeElement('lastmod', $u['lastmod']);
    $xml->writeElement('changefreq', $u['changefreq']);
    $xml->writeElement('priority', $u['priority']);
    foreach ($u['alternates'] as $lang => $href) {
        $xml->startElement('xhtml:link');
        $xml->writeAttribute('rel','alternate');
        $xml->writeAttribute('hreflang',$lang);
        $xml->writeAttribute('href',$href);
        $xml->endElement();
    }
    $xml->endElement();
}

$xml->endElement();
file_put_contents($outPath, $xml->outputMemory());
echo "Sitemap generata in: $outPath\n";
