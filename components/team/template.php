<?php
/**
 * Team Template
 *
 * Vertical label and a horizontal snapping list of portraits. Each caption
 * is placed on its portrait by the member's CSS custom properties (set on
 * the <li>) and is a disclosure button (aria-expanded / aria-controls) for
 * the panel that follows it; CSS shows the panel on hover and :focus-within,
 * scripts.js toggles it on click and moves it with the pointer on hover.
 * Members without a full name or bio get a plain caption.
 * In an editor preview the label is always printed (placeholder) and the
 * members open from an inline toolbar on the list.
 *
 * @var \Gust\Components\Team $this
 */
?>
<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="team__inner">
        <?php if ($this->label !== '' || $this->is_preview) { ?>
            <h2 class="team__label" <?= $this->inlineText('label', __('The Team', 'gust')) ?>><?= esc_html($this->label) ?></h2>
        <?php } ?>

        <div class="team__body" <?= $this->inlineToolbar(['members'], __('Members', 'gust')) ?>>
            <?php if ($this->members === [] && $this->is_preview) { ?>
                <p class="team__placeholder"><?= esc_html__('Add team members from the toolbar', 'gust') ?></p>
            <?php } else { ?>
                <ul class="team__track">
                    <?php foreach ($this->members as $member) { ?>
                        <li class="team__member" style="<?= esc_attr($member['style']) ?>">
                            <?php if ($member['image']) { ?>
                                <?= $member['image'] ?>
                            <?php } else { ?>
                                <div class="team__img team__img--empty"></div>
                            <?php } ?>

                            <?php if ($member['has_panel']) { ?>
                                <div class="team__caption">
                                    <button type="button" class="team__toggle" aria-expanded="false" aria-controls="<?= esc_attr($member['panel_id']) ?>"><?= esc_html($member['first_name']) ?></button>
                                </div>
                                <div class="team__panel" id="<?= esc_attr($member['panel_id']) ?>" data-team-panel>
                                    <p class="team__name"><?= esc_html($member['full_name']) ?></p>
                                    <?php if ($member['bio'] !== '') { ?>
                                        <p class="team__bio"><?= esc_html($member['bio']) ?></p>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="team__caption">
                                    <span class="team__toggle team__toggle--static"><?= esc_html($member['first_name']) ?></span>
                                </div>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>
</div>
