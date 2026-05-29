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

$CategoriesContents = get_xml_content(APP_DATA_PATH . "/{$lang}/elements/categories.xml");

$bodyClasses = "page";

?>
<?php // ↓ NO EMPTY LINES FOR A CORRECT HTML OUTPUT ?>
<?php include THEME_PARTS_PATH . "/header.php"; ?>

<?php include THEME_PARTS_PATH . "/breadcrumbs.php"; ?>

<section class="section-highlight">
	<h1 class="title title-1"><?php echo strtoupper($PageContents->pageTitle);?></h1>
  <p class="paragraph paragraph-1"><?php echo trim($PageContents->pageDescription);?></p>

	<section class="section-highlight section-highlight-2" aria-label="<?php echo trim($PageContents->section->ariaLabel);?>">

	<?php foreach($CategoriesContents->items->item as $item):
		$link = rtrim(BASE_URL, '/') . "/{$lang}/{$item->slug}/"; ?>

		<article class="box box-1">
			<a href="<?php echo $link ?>" class="link link-1" aria-label="<?php echo ucfirst(htmlspecialchars($item->text)); ?>">
				<?php echo ucfirst(htmlspecialchars($item->text)); ?>
			</a>
		</article>

	<?php endforeach;?>

	</section>
</section>

<?php include THEME_PARTS_PATH . "/footer.php"; ?>