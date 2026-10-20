<form class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <label class="header-search__group input-group" for="<?= esc_attr($this->input_id); ?>">
        <input
            id="<?= esc_attr($this->input_id); ?>"
            class="header-search__input"
            type="text"
            name="s"
            aria-label="<?= esc_attr__('Search', 'gust'); ?>"
            placeholder="<?= esc_attr__('Search...', 'gust'); ?>"
            required
        >

        <button class="header-search__submit btn" type="submit">
            <span class="screen-reader-text">
                <?= esc_html__('Submit', 'gust'); ?>
            </span>
            <span class="header-search__submit__text">
                <?= esc_html__('Search', 'gust'); ?>
            </span>
        </button>
    </label>
</form>
