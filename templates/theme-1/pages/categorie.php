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

$_ARG2 = !empty($_ARGS) && isset($_ARGS[1]) && $_ARGS[1]!=""?htmlspecialchars($_ARGS[1]):"";
$_ARG3 = !empty($_ARGS) && isset($_ARGS[2]) && $_ARGS[2]!=""?htmlspecialchars($_ARGS[2]):"";

?>

<?php if($_ARG3!=""):?>
	<?php include CATEGORIES_PATH . "/{$_ARG2}/{$_ARG3}.php";?>
<?php elseif($_ARG2!=""):?>
	<?php include CATEGORIES_PATH . "/{$_ARG2}.php";?>
<?php else:?>
	<?php include CATEGORIES_PATH . "/all.php";?>
<?php endif;?>