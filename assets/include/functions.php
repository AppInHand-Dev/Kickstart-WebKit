<?php
/**
 * v1.2.0
 * 29/05/2026
 * 
 * Helper functions
 *
 */

/**
 * Derive BASE_PATH from config if not defined.
 * Returns a normalized path (no trailing slash, leading slash preserved if present in BASE_URL).
 */
function derive_base_path() {
		if (defined('BASE_PATH')) {
				$p = BASE_PATH;
		} else {
				$parsedPath = parse_url(BASE_URL, PHP_URL_PATH);
				$p = $parsedPath === null ? '' : rtrim($parsedPath, '/');
				// define for other code that might expect it
				if (!defined('BASE_PATH')) {
						define('BASE_PATH', $p);
				}
		}
		return rtrim($p, '/');
}

/**
 * Load languages.xml and return SimpleXMLElement (fallback minimal if missing).
 */
function load_languages() {
		$filePathLanguages = APP_DATA_PATH . "/languages.xml";
		if (file_exists($filePathLanguages)) {
				return simplexml_load_file($filePathLanguages);
		}
		return new SimpleXMLElement('<languages><default>it</default></languages>');
}

/**
 * Build an array of valid language codes from the languages XML (exclude <default>).
 */
function build_valid_langs($Languages) {
		$valid = [];
		foreach ($Languages as $code => $label) {
				if ($code !== 'default') $valid[] = (string)$code;
		}
		return $valid;
}

/**
 * Ensure the default language from languages.xml is valid; otherwise pick first valid or 'it'.
 */
function validate_default_lang($Languages, $validLangs) {
		$defaultLang = (string)$Languages->default;
		if (!in_array($defaultLang, $validLangs, true)) {
				$defaultLang = !empty($validLangs) ? $validLangs[0] : 'it';
		}
		return $defaultLang;
}

/**
 * Detect language code in the first segment if it matches a valid language.
 * Returns the detected language code or null.
 */
function detect_lang_in_segments($segments, $validLangs) {
		if (isset($segments[0]) && preg_match('#^[a-z]{2}(?:-[A-Z]{2})?$#', $segments[0])) {
				$candidate = $segments[0];
				if (in_array($candidate, $validLangs, true)) {
						return $candidate;
				}
		}
		return null;
}

/**
 * Load routing XML and build two fast lookup maps:
 *  - routingMap[lang][localized] = internal
 *  - routingInverse[lang][internal] = localized
 *
 * Returns array: [SimpleXMLElement|null, routingMap, routingInverse]
 */
function load_routing_maps($filePathRouting) {
		$Routing = null;
		$routingMap = [];
		$routingInverse = [];
		if (file_exists($filePathRouting)) {
				$Routing = simplexml_load_file($filePathRouting);
				foreach ($Routing->children() as $langNodeName => $langNode) {
						$langCode = (string)$langNodeName;
						$routingMap[$langCode] = [];
						$routingInverse[$langCode] = [];
						foreach ($langNode->children() as $local => $internal) {
								$localKey = trim((string)$local);
								$internalVal = trim((string)$internal);
								$routingMap[$langCode][$localKey] = $internalVal;
								$routingInverse[$langCode][$internalVal] = $localKey;
						}
				}
		}
		return [$Routing, $routingMap, $routingInverse];
}

/**
 * Convert an array of localized segments (current language) into internal segments using routingMap.
 * Returns array of internal segments.
 */
function localized_to_internal_segments($localizedSegments, $currentLang, $routingMap) {
		$internal = [];
		if (empty($localizedSegments)) return $internal;
		foreach ($localizedSegments as $seg) {
				$seg = trim($seg);
				if ($seg === '') continue;
				$internal[] = translate_slug_to_internal_using_map($currentLang, $seg, $routingMap);
		}
		return $internal;
}

/**
 * Build language HTML meta links from internal segments.
 * Uses routingInverse to map internal -> localized for each target language.
 */
function build_language_meta_links($Languages, $internalSegments, $baseUrl, $routingInverse) {
		$links = '';
		$count = count($Languages) - 1;
		foreach ($Languages as $code => $label) {
				if ($code === 'default') continue;
				$count--;
				$localizedForTarget = [];
				foreach ($internalSegments as $internal) {
						$localizedForTarget[] = translate_internal_to_local_using_map($code, $internal, $routingInverse);
				}
				$localizedPath = implode('/', $localizedForTarget);
				$url = rtrim($baseUrl, '/') . '/' . $code . '/';
				if ($localizedPath !== '') {
						$url .= $localizedPath . '/';
				}
				$links .= '<link rel="alternate" hreflang="' . $code . '" href="' . htmlspecialchars($url) . '">';
				$links .= ($count>0)?"\r\n\t":"\r\n";
		}
		return $links;
}

/**
 * Build language switcher HTML links from internal segments.
 * Uses routingInverse to map internal -> localized for each target language.
 */
function build_language_links($Languages, $internalSegments, $baseUrl, $routingInverse) {
		$links = '';
		foreach ($Languages as $code => $label) {
				if ($code === 'default') continue;
				$localizedForTarget = [];
				foreach ($internalSegments as $internal) {
						$localizedForTarget[] = translate_internal_to_local_using_map($code, $internal, $routingInverse);
				}
				$localizedPath = implode('/', $localizedForTarget);
				$url = rtrim($baseUrl, '/') . '/' . $code . '/';
				if ($localizedPath !== '') {
						$url .= $localizedPath . '/';
				}
				$links .= '<a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($label) . '</a> ';
		}
		return $links;
}

/**
 * Translate all segments (all levels) from localized -> internal using routingMap.
 * Returns the translated segments array.
 */
function translate_all_segments($segments, $currentLang, $routingMap) {
		if (empty($segments)) return $segments;
		for ($i = 0, $len = count($segments); $i < $len; $i++) {
				if (isset($segments[$i]) && $segments[$i] !== '') {
						$segments[$i] = translate_slug_to_internal_using_map($currentLang, $segments[$i], $routingMap);
				}
		}
		return $segments;
}

/**
 * Lookup helper: localized -> internal using prebuilt map.
 * Falls back to original slug if no mapping exists.
 */
function translate_slug_to_internal_using_map($lang, $slug, $routingMap) {
		$key = trim($slug);
		if (empty($key)) return $key;
		if (!isset($routingMap) || !isset($routingMap[$lang])) return $key;
		return isset($routingMap[$lang][$key]) ? $routingMap[$lang][$key] : $key;
}

/**
 * Lookup helper: internal -> localized using prebuilt inverse map.
 * Falls back to internal slug if no mapping exists.
 */
function translate_internal_to_local_using_map($targetLang, $internalSlug, $routingInverse) {
		$key = trim($internalSlug);
		if (empty($key)) return $key;
		if (!isset($routingInverse) || !isset($routingInverse[$targetLang])) return $key;
		return isset($routingInverse[$targetLang][$key]) ? $routingInverse[$targetLang][$key] : $key;
}

/**
 * Backwards-compatible wrappers for older function names used elsewhere in the code.
 * These wrappers rely on the maps built earlier (global scope).
 */
function translate_slug_to_internal($lang, $slug, $Routing) {
		global $routingMap;
		return translate_slug_to_internal_using_map($lang, $slug, $routingMap);
}

function translate_internal_to_local($targetLang, $internalSlug, $Routing) {
		global $routingInverse;
		return translate_internal_to_local_using_map($targetLang, $internalSlug, $routingInverse);
}

/**
 * Load XML content from $filePath.
 * Return null if $filePath doesn't exist
 */
function get_xml_content($filePath){
		if (!file_exists($filePath)) return null;
		return simplexml_load_file($filePath);
}

/**
 * Iterates files in $dirPath and load XML contents.
 * yield return to caller. Return null if $dirPath doesn't exist
 */
function get_xml_contents($dirPath){
		if (!file_exists($dirPath)) return null;
		try {
			$iterator = new \DirectoryIterator($dirPath);
			foreach($iterator as $fileinfo){
				if(!$fileinfo->isDot()){ // not "." and ".."
					yield get_xml_content("{$dirPath}/{$fileinfo->getFilename()}");
				}
			}
		} catch (\Exception $e) {
			//echo "Errore: " . $e->getMessage();
		}
}

/**
 * Get a page link for the current languange
 * param (array|string) $slug
 */
function get_page_link($languageCode, $slugs, $baseUrl, $routingInverse) {
		$link = rtrim($baseUrl, '/') . "/{$languageCode}/";
		if(is_array($slugs)){
			foreach($slugs as $slug){
				$link .= "{$routingInverse[$languageCode][$slug]}/";
			}
			return $link;
		}
		$link .= "{$routingInverse[$languageCode][$slugs]}/";
		return $link;
}

/**
 * Get a template page name by its ID, otherwise $pageId
 * 
 */
function get_template_page_name($TemplateRouting, $pageId) {
		if(!property_exists($TemplateRouting, $pageId)) return $pageId;
		return (string)$TemplateRouting->{$pageId};
}

?>