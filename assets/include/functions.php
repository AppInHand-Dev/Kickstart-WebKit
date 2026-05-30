<?php
/**
 * v1.3.0
 * 30/05/2026
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
 * Try to write a file atomically. Returns true on success.
 */
function safe_write_file(string $path, string $content): bool {
		$tmp = $path . '.tmp';
		if (@file_put_contents($tmp, $content, LOCK_EX) === false) {
				@unlink($tmp);
				return false;
		}
		// rename is atomic on POSIX
		if (!@rename($tmp, $path)) {
				@unlink($tmp);
				return false;
		}
		@chmod($path, 0644);
		return true;
}

/**
 * Determine a usable cache backend and return an array with:
 *  ['type'=>'file'|'apcu'|'sys'|'none', 'path'=>string|null]
 */
function detect_cache_backend(string $preferredDir = null): array {
		// 1) try preferred dir (APP_CACHE_PATH)
		if ($preferredDir) {
				// try create dir if missing
				if (!is_dir($preferredDir)) {
						@mkdir($preferredDir, 0755, true);
				}
				if (is_dir($preferredDir) && is_writable($preferredDir)) {
						return ['type' => 'file', 'path' => rtrim($preferredDir, '/\\')];
				}
		}

		// 2) try APCu
		if (function_exists('apcu_enabled') ? apcu_enabled() : (function_exists('apcu_fetch') && ini_get('apc.enabled'))) {
				return ['type' => 'apcu', 'path' => null];
		}

		// 3) try system temp dir
		$sys = sys_get_temp_dir();
		if ($sys) {
				$probe = rtrim($sys, '/\\') . '/app-cache';
				if (!is_dir($probe)) @mkdir($probe, 0755, true);
				if (is_dir($probe) && is_writable($probe)) {
						return ['type' => 'sys', 'path' => $probe];
				}
		}

		// 4) fallback: no persistent cache
		return ['type' => 'none', 'path' => null];
}

/**
 * load_or_cache_xml with backend fallback.
 * Options:
 *  - format: 'php'|'json'
 *  - ttl: seconds (optional)
 *  - force: bool
 *  - parser: callable($xmlPath, SimpleXMLElement): mixed
 *  - cachePrefix: string
 *  - cacheBackend: 'auto'|'file'|'apcu'|'sys'|'none' (default 'auto')
 */
function load_or_cache_xml(string $xmlPath, string $cacheDir, array $opts = []) {
		$opts = array_merge([
				'format' => 'php',
				'ttl' => 0,
				'force' => false,
				'parser' => null,
				'cachePrefix' => '',
				'cacheBackend' => 'auto',
		], $opts);

		// choose backend
		$backend = $opts['cacheBackend'] === 'auto' ? detect_cache_backend($cacheDir) : ['type'=>$opts['cacheBackend'],'path'=>$cacheDir];

		// build cache key / filename
		$real = realpath($xmlPath) ?: $xmlPath;
		$hash = substr(md5($real), 0, 12);
		$baseName = ($opts['cachePrefix'] !== '' ? $opts['cachePrefix'] . '-' : '') . basename($xmlPath);
		$cacheFile = ($backend['type'] === 'file' || $backend['type'] === 'sys') ? ($backend['path'] . '/' . $baseName . '.' . $hash . ($opts['format'] === 'php' ? '.php' : '.json')) : null;
		$apcuKey = 'cache_' . ($opts['cachePrefix'] !== '' ? $opts['cachePrefix'] . '_' : '') . $hash;

		// force removal if requested
		if ($opts['force']) {
				if ($cacheFile && file_exists($cacheFile)) @unlink($cacheFile);
				if ($backend['type'] === 'apcu' && function_exists('apcu_delete')) @apcu_delete($apcuKey);
		}

		// try load from backend
		if (!$opts['force']) {
				if ($backend['type'] === 'file' || $backend['type'] === 'sys') {
						if ($cacheFile && file_exists($cacheFile) && file_exists($xmlPath)) {
								$xmlMtime = filemtime($xmlPath);
								$cacheMtime = filemtime($cacheFile);
								$ttlOk = ($opts['ttl'] > 0) ? (($cacheMtime + $opts['ttl']) >= time()) : true;
								if ($cacheMtime !== false && $xmlMtime !== false && $cacheMtime >= $xmlMtime && $ttlOk) {
										if ($opts['format'] === 'php') {
												$data = @include $cacheFile;
												if ($data !== false) return $data;
										} else {
												$json = @file_get_contents($cacheFile);
												if ($json !== false) return json_decode($json, true);
										}
								}
						}
				} elseif ($backend['type'] === 'apcu') {
						if (function_exists('apcu_fetch')) {
								$success = false;
								$data = apcu_fetch($apcuKey, $success);
								if ($success) {
										// optional TTL check stored inside value
										if (is_array($data) && isset($data['__meta'])) {
												$meta = $data['__meta'];
												if (isset($meta['xml_mtime']) && file_exists($xmlPath)) {
														$xmlMtime = filemtime($xmlPath);
														if ($meta['xml_mtime'] >= $xmlMtime) {
																return $data['value'];
														}
												} else {
														return $data['value'];
												}
										} else {
												return $data;
										}
								}
						}
				}
		}

		// parse XML
		if (!file_exists($xmlPath)) return null;
		libxml_use_internal_errors(true);
		$xml = simplexml_load_file($xmlPath, 'SimpleXMLElement', LIBXML_NOCDATA);
		if ($xml === false) return null;

		$value = is_callable($opts['parser']) ? call_user_func($opts['parser'], $xmlPath, $xml) : json_decode(json_encode($xml), true);

		// write to backend
		if ($backend['type'] === 'file' || $backend['type'] === 'sys') {
				if ($cacheFile) {
						if ($opts['format'] === 'php') {
								$export = var_export($value, true);
								$php = "<?php\n// Auto-generated cache for " . addslashes($xmlPath) . "\nreturn " . $export . ";\n";
								@safe_write_file($cacheFile, $php);
						} else {
								$json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
								@safe_write_file($cacheFile, $json);
						}
				}
		} elseif ($backend['type'] === 'apcu') {
				if (function_exists('apcu_store')) {
						$meta = ['xml_mtime' => file_exists($xmlPath) ? filemtime($xmlPath) : time()];
						$store = ['__meta' => $meta, 'value' => $value];
						@apcu_store($apcuKey, $store, ($opts['ttl'] > 0 ? $opts['ttl'] : 0));
				}
		} else {
				// none: keep in-request cache (static) to avoid reparsing multiple times in same request
				static $localCache = [];
				$localCache[$apcuKey] = $value;
		}

		return $value;
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

function load_routing_maps_cached(string $filePathRouting, string $cacheDir, array $opts = []) {
		// prefer PHP cache for arrays
		$opts = array_merge(['format' => 'php', 'cachePrefix' => 'routing'], $opts);

		// parser: build maps from SimpleXMLElement
		$parser = function($xmlPath, SimpleXMLElement $xml) {
				$routingMap = [];
				$routingInverse = [];
				foreach ($xml->children() as $langNodeName => $langNode) {
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
				return ['routingMap' => $routingMap, 'routingInverse' => $routingInverse];
		};

		$opts['parser'] = $parser;

		$data = load_or_cache_xml($filePathRouting, $cacheDir, $opts);
		if (!is_array($data)) {
				return [null, [], []];
		}

		// We still keep $Routing null to indicate cache used; if you need the SimpleXMLElement, you can parse separately
		return [null, $data['routingMap'] ?? [], $data['routingInverse'] ?? []];
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

function get_xml_content_cached(string $xmlPath, string $cacheDir, array $opts = []) {
		// default: cache per lingua/pagina in formato php
		$opts = array_merge(['format' => 'php', 'cachePrefix' => 'content'], $opts);
		// parser: return array representation of XML
		$opts['parser'] = function($path, SimpleXMLElement $xml) {
				return json_decode(json_encode($xml), true);
		};
		return load_or_cache_xml($xmlPath, $cacheDir, $opts);
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

function get_xml_contents_cached(string $dirPath, string $cacheDir, array $opts = []){
		if (!file_exists($dirPath)) return null;
		try {
			$iterator = new \DirectoryIterator($dirPath);
			foreach($iterator as $fileinfo){
				if(!$fileinfo->isDot()){ // not "." and ".."
					yield get_xml_content_cached("{$dirPath}/{$fileinfo->getFilename()}", $cacheDir, $opts);
				}
			}
		} catch (\Exception $e) {
			//echo "Errore: " . $e->getMessage();
		}
}

function load_or_cache_xmls(string $dirPath, string $cacheDir, array $opts = []){
		if (!file_exists($dirPath)) return null;
		try {
			$iterator = new \DirectoryIterator($dirPath);
			foreach($iterator as $fileinfo){
				if(!$fileinfo->isDot()){ // not "." and ".."
					yield load_or_cache_xml("{$dirPath}/{$fileinfo->getFilename()}", $cacheDir, $opts);
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
 * Resolve template name from cached array of template-routing.xml
 *
 * @param array|null $arr Parsed array from template-routing.xml
 * @param string $pageKey The page key to resolve (e.g. $_ARG2)
 * @return string|null Template name or null if not found
 */
function get_template_page_name_from_array($arr, string $pageKey) {
		if (!is_array($arr) || $pageKey === '') return null;

		// direct case: top-level 'routing' node
		if (isset($arr['routing']) && is_array($arr['routing'])) {
				if (array_key_exists($pageKey, $arr['routing'])) {
						return (string)$arr['routing'][$pageKey];
				}
		}

		// alternative case: mapping directly at root (no 'routing' wrapper)
		if (array_key_exists($pageKey, $arr)) {
				return (string)$arr[$pageKey];
		}

		// fallback: simple recursive search (if structure differs)
		$found = null;
		$walker = function($node) use (&$walker, $pageKey, &$found) {
				if ($found !== null || !is_array($node)) return;
				foreach ($node as $k => $v) {
						if ($k === $pageKey && (is_string($v) || is_numeric($v))) {
								$found = (string)$v;
								return;
						}
						if (is_array($v)) $walker($v);
				}
		};
		$walker($arr);

		return $found;
}

/**
 * Get a template page name by its ID, otherwise $pageId
 *
 */
function get_template_page_name($TemplateRouting, $pageId) {
		if(!property_exists($TemplateRouting, $pageId)) return $pageId;
		return (string)$TemplateRouting->{$pageId};
}

function clear_cache_dir($dir) {
		foreach (glob(rtrim($dir,'/') . '/*') as $f) {
				if (is_file($f)) @unlink($f);
		}
}

?>