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
