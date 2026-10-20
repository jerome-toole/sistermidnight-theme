<?php

use Gust\Components\Illustration;

/**
 * PageHeader Template
 *
 * @var \Gust\Components\PageHeader $this
 */
?>
<header class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="page-header__inner">
        <h1 class="page-header__label"><?= esc_html($this->label) ?></h1>

        <div class="page-header__layout content-layout">
            <?php if ($this->illustration || $this->image) { ?>
                <div class="page-header__media content-layout__media content-layout__media--always">
                    <?php if ($this->illustration) { ?>
                        <?= Illustration::make(name: $this->illustration, classes: ['page-header__illustration']); ?>
                    <?php } else { ?>
                        <?= $this->image; ?>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if ($this->intro) { ?>
                <div class="page-header__intro content-layout__text">
                    <?= wp_kses_post($this->intro) ?>
                </div>
            <?php } ?>
        </div>
    </div>
</header>
