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
    echo \Gust\Components\Icon::make(name: 'arrow', classes: ['button__icon']);
}
?></<?= $tag ?>>
