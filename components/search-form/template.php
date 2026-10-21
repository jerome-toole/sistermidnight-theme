<?php

/**
 * SearchForm Template
 *
 * @var \Gust\Components\SearchForm $this
 */
?>
<form role="search" method="get" action="<?= esc_url(home_url('/')) ?>" class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="search-form__field">
        <label class="search-form__label" for="<?= esc_attr($this->input_id) ?>"><?= esc_html__('Search the site', 'gust') ?></label>
        <input class="search-form__input" type="search" id="<?= esc_attr($this->input_id) ?>" name="s" value="<?= esc_attr($this->value) ?>" autocomplete="off">
    </div>

    <?= \Gust\Components\Button::make(
        content: __('Search', 'gust'),
        variant: 'primary',
        type: 'submit',
    ); ?>
</form>
