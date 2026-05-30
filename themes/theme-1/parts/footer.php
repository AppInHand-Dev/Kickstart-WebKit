<?php
/**
 * v1.0.1
 * 30/05/2026
 * 
 */


$creditsPageLink = get_page_link($lang, "credits", $baseUrl, $routingInverse);

?>

	</main>

	<footer class="footer footer-1" role="contentinfo">
		<div class="footer-inner">
			<div class="footer-brand">
				<strong class="brand-name">App In Hand</strong>
				<div class="brand-sub">© <span id="year">2026</span></div>
			</div>

			<div class="footer-contacts">
				<div class="contact-item">📧 <a href="mailto:info@example.com">info@example.com</a></div>
				<div class="contact-item">📞 <a href="tel:+390612345678">+39 06 1234 5678</a></div>
				<div class="contact-item">📍 <a href="https://maps.google.com/?q=Via+Roma+10+Roma" target="_blank" rel="noopener">Via Roma 10, Roma</a></div>
			</div>

			<nav class="footer-nav" aria-label="<?php echo htmlspecialchars($FooterContents["navigationAriaLabel"]);?>">
				<a href="<?php echo rtrim(BASE_URL,'/') . '/' . $lang; ?>/privacy/">Privacy</a>
				<a href="<?php echo rtrim(BASE_URL,'/') . '/' . $lang; ?>/cookie/">Cookie</a>
				<a href="<?php echo rtrim(BASE_URL,'/') . '/' . $lang; ?>/sitemap/">Sitemap</a>
			</nav>

			<div class="footer-social">
				<a href="#" aria-label="Twitter">🐦 Twitter</a>
				<a href="#" aria-label="LinkedIn">🔗 LinkedIn</a>
			</div>

			<div class="footer-meta">
				<small><?php echo htmlspecialchars($FooterContents["themeVersionText"]);?> <strong>v1.0.1</strong> • <a href="<?php echo $creditsPageLink;?>"><?php echo htmlspecialchars($FooterContents["creditsText"]);?></a></small>
			</div>
		</div>
	</footer>

	<script type="text/javascript" src="<?php echo ($_SERVER["SERVER_NAME"]!='localhost')?THEME_JS_PATH . '/min/index-min.js':THEME_JS_PATH . '/index.js';?>"></script>

</body>

</html>