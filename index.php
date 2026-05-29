<?php
/**
 * v2.7.0
 * 29/05/2026
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
$_ARG2 = (!empty($_ARGS) && isset($_ARGS[1]) && $_ARGS[1] !== "") ? htmlspecialchars($_ARGS[1]) : $_ARG1;

// If page has a different template name as its name
// Load template routing and build maps
$filePathRouting = APP_DATA_PATH . "/template-routing.xml";
$TemplateRouting = simplexml_load_file($filePathRouting);
$templateName = get_template_page_name($TemplateRouting, $_ARG2);

$pagePath = THEME_PAGES_PATH . "/{$templateName}.php";
$pagePath = file_exists($pagePath) ? $pagePath : THEME_PAGES_PATH . "/404.php";

// 8) Load contents for the resolved language
$MainMenuContents = get_xml_content(APP_DATA_PATH . "/{$lang}/elements/main-menu.xml");
$PageContents = get_xml_content(APP_DATA_PATH . "/{$lang}/pages/{$_ARG1}.xml");
$FooterContents = get_xml_content(APP_DATA_PATH . "/{$lang}/elements/footer.xml");

// 9) Include page and handle 404 header
$bodyId = !empty($_ARGS) && isset($_ARGS[0]) && $_ARGS[0]!=""?htmlspecialchars($_ARGS[0]):"home";
$bodyId = !empty($_ARGS) && isset($_ARGS[1]) && $_ARGS[1]!=""?htmlspecialchars($_ARGS[1]):$bodyId;
$bodyId = !empty($_ARGS) && isset($_ARGS[2]) && $_ARGS[2]!=""?htmlspecialchars($_ARGS[2]):$bodyId;

$bodyClasses = "";

if (basename($pagePath) === '404.php') {
		header("HTTP/1.1 404 Not Found");

		$PageContents = get_xml_content(APP_DATA_PATH . "/{$lang}/pages/404.xml");
}

include $pagePath;

if (basename($pagePath) === '404.php') {
		exit();
}
?>