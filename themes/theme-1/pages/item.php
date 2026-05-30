<?php
/**
 * v1.1.0
 * 30/05/2026
 * 
 */

/*
	IMPORTED
	(index.php)

*/

// item content (owerwrite existing)
$PageContents = load_or_cache_xml(APP_DATA_PATH . "/{$lang}/pages/categories/{$_ARG1}/{$_ARG2}.xml", APP_CACHE_PATH, [
		'format' => 'php',
		'cachePrefix' => 'content-page',
		'parser' => function($p, SimpleXMLElement $xml) { return json_decode(json_encode($xml), true); },
		'force' => $forceCacheRegen
]);

$bodyClasses = "item";

?>
<?php // ↓ NO EMPTY LINES FOR A CORRECT HTML OUTPUT ?>
<?php include THEME_PARTS_PATH . "/header.php"; ?>

<?php include THEME_PARTS_PATH . "/breadcrumbs.php"; ?>

<section class="section-highlight">
	<h1 class="title title-1"><?php echo strtoupper($PageContents['pageTitle']);?></h1>
	<p class="paragraph paragraph-1"><?php echo trim($PageContents['pageDescription']);?></p>
</section>

<?php include THEME_PARTS_PATH . "/footer.php"; ?>