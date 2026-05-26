<?php
/**
 * v2.5.0
 * 26/05/2026
 *
 * Main front controller: language detection, routing translation, page include.
 *
 * IMPORTED
 * (./assets/config/config.php)
 */

include "./assets/config/config.php";
include "./assets/include/functions.php";

/*
	Main flow (high level):
	1. derive base path / base url
	2. load languages and validate default
	3. parse request URI into segments and detect language
	4. load page routing and build fast lookup maps
	5. build language switcher links (localized <-> internal)
	6. translate all URL segments localized->internal
	7. build page path, load contents and include page
*/

// 1) Base path and base url
$basePath = derive_base_path();
$baseUrl = rtrim(BASE_URL, '/');

// 2) Languages
$Languages = load_languages();
$validLangs = build_valid_langs($Languages);
$defaultLang = validate_default_lang($Languages, $validLangs);

// 3) Request segments and language detection
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$relative = preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $uri);
$relative = trim($relative, '/');
$segments = $relative === '' ? [] : explode('/', $relative);
$uriAbs = $baseUrl . '/' . $relative . '/';

$detectedLang = detect_lang_in_segments($segments, $validLangs);

// determine $lang with priority: GET lang -> detected in path -> default
if (isset($_GET['lang']) && $_GET['lang'] !== '') {
		$langCandidate = $_GET['lang'];
		$lang = in_array($langCandidate, $validLangs, true) ? $langCandidate : $defaultLang;
} elseif ($detectedLang !== null) {
		$lang = $detectedLang;
} else {
		$lang = $defaultLang;
}

// remove language segment if present
if ($detectedLang !== null) {
		array_shift($segments);
}

// path without lang (localized form) used for optional redirect or debugging
$pathWithoutLang = implode('/', $segments);
$pathWithoutLang = trim($pathWithoutLang, '/');

// optional redirect to enforce language in URL (disabled by default)
$forceRedirect = false;
if ($forceRedirect && $detectedLang === null) {
		$target = $baseUrl . '/' . $lang . '/';
		if ($pathWithoutLang !== '') {
				$target .= $pathWithoutLang . '/';
		}
		header("Location: " . $target, true, 302);
		exit;
}

// 4) Load routing and build maps
$filePathRouting = APP_DATA_PATH . "/page-routing.xml";
list($Routing, $routingMap, $routingInverse) = load_routing_maps($filePathRouting);

// 5) Build language meta and switcher links
// localizedSegments are the segments after removing the language prefix
$localizedSegments = $segments;
$internalSegments = localized_to_internal_segments($localizedSegments, $lang, $routingMap);
$LangMetaLinks = build_language_meta_links($Languages, $internalSegments, $baseUrl, $routingInverse);
$LangLinks = build_language_links($Languages, $internalSegments, $baseUrl, $routingInverse);

// 6) Translate all segments (all levels) localized -> internal
$segments = translate_all_segments($segments, $lang, $routingMap);

// 7) Rebuild params / args and resolve page path
$params = implode('/', $segments);
$params = trim($params, '/');
$_ARGS = $params === '' ? [] : explode('/', $params);
$_ARG1 = (!empty($_ARGS) && isset($_ARGS[0]) && $_ARGS[0] !== "") ? htmlspecialchars($_ARGS[0]) : "home";

$pagePath = THEME_PAGES_PATH . "/{$_ARG1}.php";
$pagePath = file_exists($pagePath) ? $pagePath : THEME_PAGES_PATH . "/404.php";

// 8) Load contents for the resolved language
$filePathContentsByLang = APP_DATA_PATH . "/{$lang}/contents.xml";
$Contents = null;
if (file_exists($filePathContentsByLang)) {
		$Contents = simplexml_load_file($filePathContentsByLang);
}

// main menu
$filePathMainMenuContentsByLang = APP_DATA_PATH . "/{$lang}/elements/main-menu.xml";
$MainMenuContents = null;
if (file_exists($filePathMainMenuContentsByLang)) {
		$MainMenuContents = simplexml_load_file($filePathMainMenuContentsByLang);
}

// page content
$filePathPageContentsByLang = APP_DATA_PATH . "/{$lang}/pages/{$_ARG1}.xml";
$PageContents = null;
if (file_exists($filePathPageContentsByLang)) {
		$PageContents = simplexml_load_file($filePathPageContentsByLang);
}

// 9) Include page and handle 404 header
$bodyClasses = "";

if (basename($pagePath) === '404.php') {
		header("HTTP/1.1 404 Not Found");
}

include $pagePath;

if (basename($pagePath) === '404.php') {
		exit();
}
?>