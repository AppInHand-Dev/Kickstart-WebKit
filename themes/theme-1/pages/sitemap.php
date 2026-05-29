<?php
/**
 * v1.0.0
 * 29/05/2026
 * 
 */


$bodyClasses = "page";

$base = rtrim(BASE_URL, '/');
$langPrefix = isset($lang) ? '/' . $lang : '/it'; // fallback to /it if $lang not set

$categoriesPageLink = get_page_link($lang, "categories", $baseUrl, $routingInverse);
$portfolioPageLink = get_page_link($lang, "portfolio", $baseUrl, $routingInverse);
$contactsPageLink = get_page_link($lang, "contacts", $baseUrl, $routingInverse);
$privacyPageLink = get_page_link($lang, "privacy", $baseUrl, $routingInverse);
$cookiePageLink = get_page_link($lang, "cookie", $baseUrl, $routingInverse);
$creditsPageLink = get_page_link($lang, "credits", $baseUrl, $routingInverse);
$sitemapPageLink = get_page_link($lang, "sitemap", $baseUrl, $routingInverse);
$category1PageLink = get_page_link($lang, "category-1", $baseUrl, $routingInverse);
$category2PageLink = get_page_link($lang, "category-2", $baseUrl, $routingInverse);
$category3PageLink = get_page_link($lang, "category-3", $baseUrl, $routingInverse);
$element1PageLink = get_page_link($lang, ["category-1", "item-1"], $baseUrl, $routingInverse);
$element2PageLink = get_page_link($lang, ["category-1", "item-2"], $baseUrl, $routingInverse);

?>
<?php // ↓ NO EMPTY LINES FOR A CORRECT HTML OUTPUT ?>
<?php include THEME_PARTS_PATH . "/header.php"; ?>

<section class="sitemap section-highlight" aria-labelledby="sitemap-title">
  <h1 id="sitemap-title" class="title title-1"><?php echo strtoupper($PageContents->pageTitle);?></h1>

  <p class="paragraph paragraph-1"><?php echo $PageContents->pageDescription;?></p>

  <ul class="sitemap-list">
    <li><a href="<?php echo $base . $langPrefix . '/'; ?>"><?php echo $PageContents->linkTexts->home; ?></a></li>

    <li>
      <a href="<?php echo $categoriesPageLink; ?>"><?php echo $PageContents->linkTexts->categories; ?></a>
      <ul>
        <li>
          <a href="<?php echo $category1PageLink; ?>"><?php echo $PageContents->linkTexts->category1; ?></a>
          <ul>
            <li><a href="<?php echo $element1PageLink; ?>"><?php echo $PageContents->linkTexts->item1; ?></a></li>
            <li><a href="<?php echo $element2PageLink; ?>"><?php echo $PageContents->linkTexts->item2; ?></a></li>
          </ul>
        </li>
        <li><a href="<?php echo $category2PageLink; ?>"><?php echo $PageContents->linkTexts->category2; ?></a></li>
        <li><a href="<?php echo $category3PageLink; ?>"><?php echo $PageContents->linkTexts->category3; ?></a></li>
      </ul>
    </li>

    <li><a href="<?php echo $portfolioPageLink; ?>"><?php echo $PageContents->linkTexts->portfolio; ?></a></li>

    <li><a href="<?php echo $contactsPageLink; ?>"><?php echo $PageContents->linkTexts->contacts; ?></a></li>

    <li>
      <a href="<?php echo $privacyPageLink; ?>"><?php echo $PageContents->linkTexts->privacy; ?></a>
    </li>

    <li>
      <a href="<?php echo $cookiePageLink; ?>"><?php echo $PageContents->linkTexts->cookie; ?></a>
    </li>

    <li>
      <a href="<?php echo $creditsPageLink; ?>"><?php echo $PageContents->linkTexts->credits; ?></a>
    </li>

    <li>
      <a href="<?php echo $sitemapPageLink; ?>"><?php echo $PageContents->linkTexts->sitemap; ?></a>
    </li>
  </ul>

</section>

<?php include THEME_PARTS_PATH . "/footer.php"; ?>