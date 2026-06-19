<?php
/**
 * v1.1.1
 * 19/06/2026
 * 
 */

define("BASE_URL", "http://localhost/sites/kickstart-webkit");
define("APP_DATA_PATH", "./data");
define("APP_CACHE_PATH", APP_DATA_PATH . "/cache");
define("APP_IMAGES_URL", BASE_URL . "/assets/images");
define("APP_CSS_PATH", "./assets/css");
define("APP_JS_PATH", "./assets/js");
define("APP_INCLUDE_PATH", "./assets/include");

define("THEME_DIR_NAME", "theme-1");
define("THEME_URL", BASE_URL . "/tamplates/" . THEME_DIR_NAME);
define("THEME_PATH", "./themes/" . THEME_DIR_NAME);
define("THEME_PARTS_PATH", THEME_PATH . "/parts");
define("THEME_PAGES_PATH", THEME_PATH . "/pages");
define("THEME_ASSETS_PATH", THEME_PATH . "/assets");
define("THEME_CSS_PATH", THEME_ASSETS_PATH . "/css");
define("THEME_JS_PATH", THEME_ASSETS_PATH . "/js");

define("CATEGORIES_URL", BASE_URL . "/categories");
define("CATEGORIES_PATH", THEME_PAGES_PATH . "/categories");

define("USE_CACHE", true);

define("ROBOTS_CONTENT", "noindex, nofollow");

$parsedPath = parse_url(BASE_URL, PHP_URL_PATH);
$parsedPath = $parsedPath === null ? '' : rtrim($parsedPath, '/');
define('BASE_PATH', $parsedPath);

?>