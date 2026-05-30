<?php
/**
 * v1.1.0
 * 30/05/2026
 * 
 */


$bodyClasses = "page";

?>
<?php // ↓ NO EMPTY LINES FOR A CORRECT HTML OUTPUT ?>
<?php include THEME_PARTS_PATH . "/header.php"; ?>

<?php include THEME_PARTS_PATH . "/breadcrumbs.php"; ?>

<section class="section-highlight section-highlight-1">
  <h1 class="title title-1"><?php echo strtoupper($PageContents['pageTitle']);?></h1>
  <p class="paragraph paragraph-1"><?php echo trim($PageContents['pageDescription']);?></p>

  <div class="contact-grid">

    <div class="contact-card">
      <div class="contact-icon" aria-hidden="true">📧</div>
      <div class="contact-body">
        <div class="contact-label">
          <?php echo htmlspecialchars($PageContents['contactCard']['email']['text']);?>
        </div>
        <div class="contact-value">
          <a href="mailto:info@example.com" class="contact-link" data-copy="info@example.com">info@example.com</a>
          <button 
                  class="btn-copy" 
                  data-copy="info@example.com" 
                  aria-label="<?php echo htmlspecialchars($PageContents['contactCard']['email']['btnAriaLabel']);?>">📋</button>
        </div>
        <div class="contact-note">
          <?php echo htmlspecialchars($PageContents['contactCard']['email']['note']);?>
        </div>
      </div>
    </div>

    <div class="contact-card">
      <div class="contact-icon" aria-hidden="true">📞</div>
      <div class="contact-body">
        <div class="contact-label">
          <?php echo htmlspecialchars($PageContents['contactCard']['telephone']['text']);?>
        </div>
        <div class="contact-value">
          <a href="tel:+390612345678" class="contact-link" data-copy="+390612345678">+39 06 1234 5678</a>
          <button 
                  class="btn-copy" 
                  data-copy="+390612345678" 
                  aria-label="<?php echo htmlspecialchars($PageContents['contactCard']['telephone']['btnAriaLabel']);?>">📋</button>
        </div>
        <div class="contact-note">
          <?php echo htmlspecialchars($PageContents['contactCard']['telephone']['note']);?>
        </div>
      </div>
    </div>

    <div class="contact-card">
      <div class="contact-icon" aria-hidden="true">📍</div>
      <div class="contact-body">
        <div class="contact-label"><?php echo htmlspecialchars($PageContents['contactCard']['address']['text']);?></div>
        <div class="contact-value"><?php echo htmlspecialchars($PageContents['contactCard']['address']['street']);?></div>
        <div class="contact-note">
          <a 
              href="https://www.google.com/maps/search/?api=1&query=Via+Roma+10+Roma" 
              target="_blank" 
              rel="noopener" 
              class="contact-link">
                <?php echo htmlspecialchars($PageContents['contactCard']['address']['openMapText']);?>
          </a>
        </div>
      </div>
    </div>

    <div class="contact-card">
      <div class="contact-icon" aria-hidden="true">💬</div>
      <div class="contact-body">
        <div class="contact-label">
          <?php echo htmlspecialchars($PageContents['contactCard']['social']['text']);?>
        </div>
        <div class="contact-value social-links">
          <a 
              href="#" 
              class="contact-link" 
              aria-label="<?php echo htmlspecialchars($PageContents['contactCard']['social']['twitter']['ariaLabel']);?>">
                🐦 <?php echo htmlspecialchars($PageContents['contactCard']['social']['twitter']['text']);?>
          </a>
          <a 
              href="#" 
              class="contact-link" 
              aria-label="<?php echo htmlspecialchars($PageContents['contactCard']['social']['linkedIn']['ariaLabel']);?>">
                🔗 <?php echo htmlspecialchars($PageContents['contactCard']['social']['linkedIn']['text']);?>
          </a>
        </div>
        <div class="contact-note"><?php echo htmlspecialchars($PageContents['contactCard']['social']['note']);?></div>
      </div>
    </div>

  </div>

</section>

<?php include THEME_PARTS_PATH . "/footer.php"; ?>