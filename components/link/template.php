<?php if (! empty($this->url) || ! empty($this->content)) { ?>
    <a class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>><?php
        $filter = $this->content_filter;
    echo trim(
        is_callable($filter)
            ? $filter($this->content)
            : wp_kses_post($this->content ?? '')
    );
    echo $this->external_markup ?? '';
    ?></a>
<?php } ?>
