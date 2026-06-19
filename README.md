# Kickstart-WebKit

A minimalist, file-based PHP boilerplate for building fast, themeable websites. No bloat. Just fast, just light.

<img src="assets/images/screenshot-1.jpg">


[WATCH LIVE](https://www.appinhand.it/sites/kickstart-webkit)


---

### 💡 Philosophy

Kickstart-WebKit was born from the need to develop simple websites quickly, without the overhead of large frameworks, complex build processes, or databases. The core principles are:

*   **No Backend Bloat:** No databases, no admin panels, no heavy dependencies. This is for content that is edited directly in the code.
*   **Zero Plugins:** The project is intentionally lean to ensure the fastest possible page load times and a minimal footprint.
*   **Direct Control:** You are in full control. What you edit in the PHP/HTML/CSS files is what you see in the browser. No compilation, no magic.

---

### ✨ Features

*   **Clean, File-Based Routing:** Create SEO-friendly URLs like `/products/my-product` without complex `.htaccess` rules.
*   **Config-Driven Theming:** Easily switch the entire look and feel of your site (CSS, images, etc.) by changing a single line in the config file. Perfect for creating multiple brand variations or dark/light modes.
*   **Zero Dependencies & No Build Step:** Pure PHP and HTML. No `composer install`, no `npm build`. Just edit the files and see the changes instantly.
*   **Per-file cache layer** — Generic `load_or_cache_xml()` with atomic writes (tmp + rename) that stores parsed results to a fast cache.
*   **Cache backend fallback** — File cache (PHP/JSON), APCu in-memory, system temp dir fallback, and graceful degradation to no persistent cache when the host disallows writes.
*   **Routing maps cached** — `page-routing.xml` is parsed into `routingMap` and `routingInverse` and cached per-file to avoid repeated XML parsing.
*   **Content caching** — Per-language content files (main menu, pages, footer) are cached per-file to reduce I/O and parsing overhead.
*   **Safe atomic cache writes** — `safe_write_file()` helper to avoid partial writes and race conditions.
*   **Cache control flags** — `USE_CACHE` config, `?nocache=1` override, and `force` option to regenerate caches in development.
*   **Lightweight CDATA preservation** — Parser extracts CDATA for known nodes (e.g. `pageDescription`) and stores raw HTML strings in the cache so WYSIWYG content is preserved without heavy DOM processing on every request.
*   **Template routing from cache** — `get_template_page_name_from_array()` helper resolves template names from cached arrays to avoid re-parsing `template-routing.xml`.
*   **In-request local cache** — when persistent cache is unavailable, parsed results are kept for the duration of the request to avoid duplicate parsing.

---

### 🎯 Who is this for?

This boilerplate is designed for **web developers with some experience** who are comfortable working directly with PHP, HTML, CSS, JS, XML. It is the perfect starting point if you want a no-nonsense foundation for a landing page, a portfolio, or a small business website, and you value speed and simplicity above all else.

---

### 🚀 Getting Started

1.  Clone or download this repository.
2.  **Crucial:** Open the `assets/config/config.php` file and edit the configuration variables to match your local development environment.
3.  Start building your pages!

---

### 📦 Deploying to Production

Before deploying your site to a live server, it is **essential** to read and follow the instructions detailed in the `docs/migrate-to-production.txt` file to ensure security and proper functionality.

---

### 🛣️ Roadmap

Planned improvements and migration tasks:

* **Migration: XML → JSON** — provide migration scripts and tooling to convert existing XML content and routing files to JSON, simplifying parsing and reducing conversion overhead. The migration will include:
  * a safe conversion script that preserves CDATA/HTML content,
  * dual-read compatibility (prefer JSON if present, fallback to XML),
  * documentation and examples for updating templates to consume JSON.
* **Optional: Admin CLI** — a small command-line tool to regenerate caches, clear cache directory, and run the XML→JSON migration.
* **Optional: APCu/Redis integration guide** — documentation and examples for using APCu or Redis as a hot cache in high‑traffic deployments.

---

### License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
