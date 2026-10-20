<?php

/**
 * Illustration Template
 *
 * Inline SVG with the component's attributes injected onto the root <svg> element.
 *
 * @var \Gust\Components\Illustration $this
 */
?>
<?= preg_replace('/^<svg\b/', '<svg '.attributes($this->attributes), $this->svg, 1); ?>
