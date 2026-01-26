<div class="<?= classes('social-icons', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <ul class="social-icons__icons">
        <?php foreach ($this->networks as $key => $network) { ?>
            <?php $value = $network['network']['value']; ?>
            <li class="social-icons__icon social-icons__icon--<?= esc_attr($value) ?>">
                <a class="mask-icon" target="_blank" rel="noopener noreferrer" href="<?= esc_url($network['url']) ?>" style="--mask-icon: url('<?= staticUrl("images/social/$value.svg") ?>')">

                    <?php if (! empty($network['title'])) { ?>
                        <span class="screen-reader-text">
                            <?= wp_kses_post($network['title']); ?>
                        </span>
                    <?php } ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
