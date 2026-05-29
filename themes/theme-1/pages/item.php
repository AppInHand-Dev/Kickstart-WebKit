<?php
/**
 * v1.0.0
 * 29/05/2026
 * 
 */

/*
	IMPORTED
	(index.php)

*/

// item content (owerwrite existing)
$PageContents = get_xml_content(APP_DATA_PATH . "/{$lang}/pages/categories/{$_ARG1}/{$_ARG2}.xml");

$bodyClasses = "item";

?>
<?php // ↓ NO EMPTY LINES FOR A CORRECT HTML OUTPUT ?>
<?php include THEME_PARTS_PATH . "/header.php"; ?>

<?php include THEME_PARTS_PATH . "/breadcrumbs.php"; ?>

<section class="section-highlight">
	<h1 class="title title-1"><?php echo strtoupper($PageContents->pageTitle);?></h1>
	<p class="paragraph paragraph-1"><?php echo trim($PageContents->pageDescription);?></p>
</section>

<?php include THEME_PARTS_PATH . "/footer.php"; ?>