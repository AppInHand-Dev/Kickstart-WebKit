<?php
/**
 * v1.0.0
 * 27/05/2026
 * 
 */


$bodyClasses = "page";

?>
<?php // ↓ NO EMPTY LINES FOR A CORRECT HTML OUTPUT ?>
<?php include THEME_PARTS_PATH . "/header.php"; ?>

<section class="section-highlight section-highlight-1">
  <h1 class="title title-1"><?php echo strtoupper($PageContents->pageTitle);?></h1>
  <p class="paragraph paragraph-1"><?php echo trim($PageContents->pageDescription);?></p>
</section>

<?php include THEME_PARTS_PATH . "/footer.php"; ?>