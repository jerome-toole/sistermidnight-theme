<?php

/**
 * Icon Template
 *
 * Inline SVG with the component's attributes injected onto the root <svg> element.
 *
 * @var \Gust\Components\Icon $this
 */
?>
<?= preg_replace('/^<svg\b/', '<svg '.attributes($this->attributes), $this->svg, 1); ?>
