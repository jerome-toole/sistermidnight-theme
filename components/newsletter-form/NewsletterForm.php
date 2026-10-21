<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * NewsletterForm Component
 *
 * Inline Gravity Forms render (ajax off) of the newsletter form: a single
 * email field in the underline style and a primary "Sign up" button. The
 * form ID comes from the `newsletter_form_id` option unless given. The
 * Brevo add-on pushes entries to the mailing list; no theme code is involved.
 *
 * Usage:
 *   use Gust\Components\NewsletterForm;
 *
 *   echo NewsletterForm::make();            // form from options
 *   echo NewsletterForm::make(form_id: 2);  // explicit
 *
 * The same form can appear twice on a page (the News archive's Card and the
 * Newsletter Banner): every render after the first gets its ids suffixed so
 * ids stay unique and each label still points at its own input.
 */
class NewsletterForm extends ComponentBase
{
    protected static string $name = 'newsletter-form';

    /**
     * Create a new NewsletterForm component.
     *
     * @param  int|null  $form_id  Gravity Forms ID. Defaults to the `newsletter_form_id` option.
     * @return static|null Returns null when Gravity Forms or the form is unavailable.
     */
    public static function make(
        ?int $form_id = null,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return static::formExists(static::resolveFormId($args['form_id'] ?? null));
    }

    protected static function transform(array $args): array
    {
        $args['form_id'] = static::resolveFormId($args['form_id'] ?? null);
        $args['classes'] = array_merge(['newsletter-form'], $args['classes'] ?? []);

        return $args;
    }

    /** Renders so far this request, per form ID. */
    protected static array $renders = [];

    /**
     * The Gravity Forms markup. Repeat renders of the same form get `-{n}`
     * appended to every id and to the attributes / fragments that point at them.
     */
    public function formHtml(): string
    {
        $html = (string) gravity_form(
            $this->form_id,
            display_title: false,
            display_description: false,
            display_inactive: false,
            field_values: null,
            ajax: false,
            tabindex: 0,
            echo: false,
        );

        $count = static::$renders[$this->form_id] = (static::$renders[$this->form_id] ?? 0) + 1;

        return $count > 1 ? static::suffixIds($html, '-'.$count) : $html;
    }

    protected static function suffixIds(string $html, string $suffix): string
    {
        preg_match_all('/\bid=([\'"])([^\'"]+)\1/', $html, $matches);

        foreach (array_unique($matches[2]) as $id) {
            $quoted = preg_quote($id, '/');
            $html = preg_replace(
                [
                    '/\b((?:id|for|aria-describedby|aria-labelledby|aria-controls)=([\'"])(?:[^\'"]*\s)?)'.$quoted.'(?=[\s\'"])/',
                    '/#'.$quoted.'(?![\w-])/',
                ],
                ['${1}'.$id.$suffix, '#'.$id.$suffix],
                $html,
            );
        }

        return $html;
    }

    /**
     * The form ID to render: the given one, else the `newsletter_form_id` option.
     */
    public static function resolveFormId(?int $form_id = null): int
    {
        if ($form_id > 0) {
            return $form_id;
        }

        $option = \function_exists('get_field') ? \get_field('newsletter_form_id', 'option') : null;

        return is_numeric($option) ? (int) $option : 0;
    }

    /**
     * Whether Gravity Forms is active and the form exists and is active.
     */
    public static function formExists(int $form_id): bool
    {
        if ($form_id < 1 || ! \function_exists('gravity_form') || ! class_exists('\GFAPI')) {
            return false;
        }

        $form = \GFAPI::get_form($form_id);

        return is_array($form) && ! empty($form['is_active']) && empty($form['is_trash']);
    }

    /**
     * `gform_submit_button` filter: swap the newsletter form's <input type="submit">
     * for the primary Button (label + arrow). Other forms are left alone.
     */
    public static function submitButton(string $button, array $form): string
    {
        if ((int) ($form['id'] ?? 0) !== static::resolveFormId()) {
            return $button;
        }

        $label = trim((string) ($form['button']['text'] ?? '')) ?: __('Sign up', 'gust');

        return (string) Button::make(
            content: esc_html($label),
            variant: 'primary',
            type: 'submit',
            classes: ['gform_button'],
            attributes: ['id' => 'gform_submit_button_'.(int) $form['id']],
        );
    }
}
