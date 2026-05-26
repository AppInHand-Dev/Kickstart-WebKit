<?php
/**
 * v2.2.1a
 * 26/05/2026
 * 
 */

/*
	IMPORTED
	(categorie.php)

*/

$bodyClasses = "categories";

?>
<?php // ↓ NO EMPTY LINES FOR A CORRECT HTML OUTPUT ?>
<?php include THEME_PARTS_PATH . "/header.php"; ?>

<?php include THEME_PARTS_PATH . "/breadcrumbs.php"; ?>

<h1><?php echo strtoupper($PageContents->pageTitle);?></h1>

<a href="<?php echo rtrim(BASE_URL, '/') . '/' . $lang . '/' . CATEGORY_1_SLUG . '/'; ?>">
	<?php echo ucfirst(htmlspecialchars($Contents->category1Name)); ?>
</a>

<?php include THEME_PARTS_PATH . "/footer.php"; ?>