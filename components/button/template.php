<?php
$tag = $this->is_link ? 'a' : 'button';
$styled = ! empty($this->variant);
?>
<<?= $tag ?> class="<?= classes('button', $this->classes) ?>" <?= attributes($this->attributes) ?>><?php
    if ($styled) {
        ?><span class="button__label"><?= wp_kses_post($this->content); ?></span><?php
    } else {
        echo wp_kses_post($this->content);
    }

if ($this->arrow) {
    ?><svg class="button__icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M3 12h17M13 5l7 7-7 7" /></svg><?php
}
?></<?= $tag ?>>
