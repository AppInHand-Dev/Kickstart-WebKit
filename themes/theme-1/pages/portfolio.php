<?php
/**
 * v1.0.1
 * 30/05/2026
 * 
 */


$bodyClasses = "page";

$currentLink = get_page_link($lang, "portfolio", $baseUrl, $routingInverse);

?>
<?php // ↓ NO EMPTY LINES FOR A CORRECT HTML OUTPUT ?>
<?php include THEME_PARTS_PATH . "/header.php"; ?>

<?php include THEME_PARTS_PATH . "/breadcrumbs.php"; ?>

<section class="section-highlight section-highlight-1">
	<h1 class="title title-1"><?php echo strtoupper($PageContents['pageTitle']);?></h1>
  <p class="paragraph paragraph-1"><?php echo trim($PageContents['pageDescription']);?></p>

	<section class="cards-grid">
		<?php foreach($PageContents['works']['work'] as $work):?>

			<article class="card card-2">
				<h2 class="title title-2"><?php echo ucfirst(htmlspecialchars($work['title'])); ?></h2>
				<p class="paragraph paragraph-2"><?php echo ucfirst(htmlspecialchars($work['description'])); ?></p>
				<div class="card-meta">
					<span class="pill"><?php echo htmlspecialchars($work['projectText']); ?></span>
					<a class="action" href="<?php echo $currentLink;?>"><?php echo htmlspecialchars($work['watchText']); ?></a>
				</div>
				<div class="card-meta">
					<span class="pill"><?php echo htmlspecialchars($work['clientText']); ?></span>
					<a class="action" href="<?php echo $currentLink;?>"><?php echo htmlspecialchars($work['detailsText']); ?></a>
				</div>
			</article>

		<?php endforeach;?>
	</section>

</section>

<?php include THEME_PARTS_PATH . "/footer.php"; ?>