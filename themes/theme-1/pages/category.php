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

// category content (owerwrite existing)
$PageContents = get_xml_content(APP_DATA_PATH . "/{$lang}/pages/categories/{$_ARG1}.xml");
$ItemContents = get_xml_contents(APP_DATA_PATH . "/{$lang}/pages/categories/{$_ARG1}");

$items = iterator_to_array($ItemContents, false); // false = reindex numerically
$items = array_filter($items, function($it){
    $enabled = is_object($it) ? ($it->enabled ?? 0) : ($it['enabled'] ?? 0);
    return (int)$enabled === 1;
});
usort($items, function($a,$b){ return (int)$a->position <=> (int)$b->position; });

$bodyClasses = "category";

?>
<?php // ↓ NO EMPTY LINES FOR A CORRECT HTML OUTPUT ?>
<?php include THEME_PARTS_PATH . "/header.php"; ?>

<?php include THEME_PARTS_PATH . "/breadcrumbs.php"; ?>

<section class="section-highlight">
	<h1 class="title title-1"><?php echo strtoupper($PageContents->pageTitle);?></h1>
	<p class="paragraph paragraph-1"><?php echo trim($PageContents->pageDescription);?></p>

	<section class="section-highlight section-highlight-2" aria-label="<?php echo trim($PageContents->section->ariaLabel);?>">

	<?php 
	if($items != null):
		foreach($items as $_ItemContents):
			$link = rtrim(BASE_URL, '/') . '/' . $lang . '/' . $_ItemContents->categories->primary->slug . '/' . $_ItemContents->slug . '/'; ?>

			<article class="box box-1">
				<a href="<?php echo $link ?>" class="link link-1" aria-label="<?php echo ucfirst(htmlspecialchars($_ItemContents->link->ariaLabel)); ?>">
					<?php echo ucfirst(htmlspecialchars($_ItemContents->link->text)); ?>
				</a>
			</article>

		<?php endforeach;?>
	<?php endif;?>

	</section>
</section>

<?php include THEME_PARTS_PATH . "/footer.php"; ?>