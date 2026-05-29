<?php
/**
 * v1.0.0
 * 04/02/2026
 *
 * Breadcrumbs: build localized links using internal slugs ($_ARGS).
 * Uses routing inverse map if available to map internal -> localized slugs.
 */

$filePathBreadcrumbsRouting = APP_DATA_PATH . "/{$lang}/breadcrumbs-routing.xml";
$BreadcrumbsRouting = null;
if (file_exists($filePathBreadcrumbsRouting)) {
		$BreadcrumbsRouting = simplexml_load_file($filePathBreadcrumbsRouting);
}

// Try to reuse routing inverse map built by index.php if available
$routingInverseForLang = [];
if (isset($routingInverse) && is_array($routingInverse) && isset($routingInverse[$lang])) {
		// $routingInverse is expected to be global: [lang][internal] = localized
		$routingInverseForLang = $routingInverse[$lang];
} else {
		// Fallback: load page-routing.xml and build inverse map for current language only
		$filePathRouting = APP_DATA_PATH . "/page-routing.xml";
		if (file_exists($filePathRouting)) {
				$RoutingXml = simplexml_load_file($filePathRouting);
				if (isset($RoutingXml->{$lang})) {
						foreach ($RoutingXml->{$lang}->children() as $local => $internal) {
								$internalVal = trim((string)$internal);
								$localKey = trim((string)$local);
								$routingInverseForLang[$internalVal] = $localKey;
						}
				}
		}
}

// Helper: map internal slug -> localized slug for current language
function internal_to_localized($internalSlug, $routingInverseForLang) {
		$key = trim((string)$internalSlug);
		if ($key === '') return $key;
		if (isset($routingInverseForLang[$key]) && $routingInverseForLang[$key] !== '') {
				return $routingInverseForLang[$key];
		}
		// fallback: return internal slug if no localized mapping
		return $key;
}

?>

<?php if (isset($_ARGS) && is_array($_ARGS) && count($_ARGS) > 0): ?>
		<div id="breadcrumb" class="col-12 order-1" aria-label="Breadcrumb">
				<nav>
						<?php
						// Home link (localized root)
						$homeUrl = rtrim(BASE_URL, '/') . '/' . $lang . '/';
						$homeText = 'Home';
						if ($BreadcrumbsRouting && property_exists($BreadcrumbsRouting, 'home')) {
								$homeText = (string)$BreadcrumbsRouting->home;
						} else {
								// try to find a localized label for 'home' in routing or fallback to 'Home'
								if (isset($routingInverseForLang['home']) && $routingInverseForLang['home'] !== '') {
										$homeText = $routingInverseForLang['home'];
								}
						}
						?>
						<a href="<?php echo htmlspecialchars($homeUrl); ?>">
								<span><?php echo ucfirst(htmlspecialchars($homeText)); ?></span>
						</a>
						 »
						<?php
						// Build breadcrumbs progressively: for each index, map internal slugs to localized and build URL
						$accum = [];
						$total = count($_ARGS);
						foreach ($_ARGS as $i => $_ARG) {
								// $_ARG is internal slug (index i)
								$internal = (string)$_ARG;
								// localized slug for URL
								$localized = internal_to_localized($internal, $routingInverseForLang);
								$accum[] = $localized;

								// display text: prefer breadcrumbs-routing.xml (per-language), fallback to localized or internal
								$displayText = $localized;
								if ($BreadcrumbsRouting && property_exists($BreadcrumbsRouting, $internal)) {
										$displayText = (string)$BreadcrumbsRouting->{$internal};
								} elseif ($displayText === '') {
										$displayText = $internal;
								}

								// prepare final display (first letter uppercase)
								$displayTextEscaped = ucfirst(htmlspecialchars($displayText));

								// build URL for this crumb
								$url = rtrim(BASE_URL, '/') . '/' . $lang . '/' . implode('/', $accum) . '/';

								// if not last item, render as link
								if ($i < $total - 1) {
										?>
										<a href="<?php echo htmlspecialchars($url); ?>">
												<span><?php echo $displayTextEscaped; ?></span>
										</a> »
										<?php
								} else {
										// last item: plain text with active class
										?>
										<span class="breadcrumb-current" aria-current="page"><?php echo $displayTextEscaped; ?></span>
										<?php
								}
						}
						?>
				</nav>
		</div>
<?php endif; ?>