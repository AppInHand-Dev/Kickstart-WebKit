<?php
/**
 * v2.0.0a
 * 10/06/2025
 * 
 */

/*
	IMPORTED
	(./assets/config/config.php)

*/

include "./assets/config/config.php";

$params = isset($_GET['params']) ? filter_input(INPUT_GET, 'params', FILTER_SANITIZE_URL) : '';
$params = isset($_GET['params']) ? trim($params, '/') : '';

$_ARGS = explode('/', $params);
$_ARG1 = !empty($_ARGS) && isset($_ARGS[0]) && $_ARGS[0]!=""?htmlspecialchars($_ARGS[0]):"home";

$pagePath = THEME_PAGES_PATH . "/{$_ARG1}.php";
$pagePath = file_exists($pagePath)?$pagePath:THEME_PAGES_PATH . "/404.php";

$lang = "it";
$bodyClasses = "";


if(strpos($pagePath, "404") === true){
	header("HTTP/1.1 404 Not Found");
}

include $pagePath;

if(strpos($pagePath, "404") === true){
	exit();
}

?>