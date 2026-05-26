<?php
/**
 * v2.0.0a
 * 10/06/2025
 * 
 */

/*
	IMPORTED
	(index.php)

*/

$bodyClasses = "item";

$filePathItem = APP_DATA_PATH . "/{$lang}/item-1.xml";

if(file_exists($filePathItem)){
	$Item = simplexml_load_file($filePathItem);
}

?>

<?php include THEME_PARTS_PATH . "/header.php"; ?>

<h1>PAGINA <?php echo $Item->name;?></h1>

<?php include THEME_PARTS_PATH . "/footer.php"; ?>