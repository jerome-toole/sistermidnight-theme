<?php

namespace Theme\Modules\GravityForms;

class GravityFormsModule
{
    public static function init(): void
    {
        // Prevent <head> GF hooks script on pages it's not needed
        // https://docs.gravityforms.com/gform_force_hooks_js_output/
        \add_filter('gform_force_hooks_js_output', '__return_false');

        // Move header GF scripts to footer (includes jQuery)
        // https://docs.gravityforms.com/gform_enqueue_scripts/
        \add_filter('gform_enqueue_scripts', [__CLASS__, 'moveScriptsToFooter']);

        // Disable default theme CSS
        // https://docs.gravityforms.com/gform_disable_form_theme_css/
        \add_filter('gform_disable_form_theme_css', '__return_true');

        // Override default GF initial settings
        \add_filter('gform_form_settings_initial_values', [__CLASS__, 'setInitialSettings']);

        // Sanitize confirmation message (uses wp_kses_post)
        // https://docs.gravityforms.com/gform_sanitize_confirmation_message/
        \add_filter('gform_sanitize_confirmation_message', '__return_true');

        // Auto-scroll to confirmation/validation on submission
        // https://docs.gravityforms.com/gform_confirmation_anchor/
        \add_filter('gform_confirmation_anchor', '__return_true');

        // Disable automatic updates
        // https://docs.gravityforms.com/gform_disable_auto_update/
        \add_filter('gform_disable_auto_update', '__return_true');
        \add_filter('option_gform_enable_background_updates', '__return_false');

        /* Change Gravity Forms' Ajax Spinner into a transparent image */
        add_filter('gform_ajax_spinner_url', [__CLASS__, 'spinnerUrl'], 10);

        // Allow the form block (the editor allow list only adds theme/* blocks automatically)
        \add_filter('allowed_block_types_all', [__CLASS__, 'allowFormBlock'], 20);

        // Entries and form editing for Site Managers (no delete, settings, add-ons or uninstall).
        // Feed add-ons (e.g. gravityforms_brevo) are granted per project.
        \add_filter('gust/roles/site_manager/capabilities', [__CLASS__, 'siteManagerCapabilities']);
    }

    public static function siteManagerCapabilities(array $caps): array
    {
        return array_merge($caps, [
            'gravityforms_view_entries',
            'gravityforms_edit_entries',
            'gravityforms_delete_entries',
            'gravityforms_export_entries',
            'gravityforms_view_entry_notes',
            'gravityforms_edit_entry_notes',
            'gravityforms_edit_forms',
            'gravityforms_create_form',
            'gravityforms_preview_forms',
        ]);
    }

    public static function allowFormBlock(bool|array $allowedBlocks): bool|array
    {
        if (is_array($allowedBlocks)) {
            $allowedBlocks[] = 'gravityforms/form';
        }

        return $allowedBlocks;
    }

    public static function moveScriptsToFooter(): void
    {
        \wp_script_add_data('gform_gravityforms', 'group', 1);
        \wp_script_add_data('gform_json', 'group', 1);
    }

    public static function setInitialSettings(array $initial_values): array
    {
        $initial_values['labelPlacement'] = 'top_label';
        $initial_values['descriptionPlacement'] = 'above';
        $initial_values['subLabelPlacement'] = 'above';
        $initial_values['validationSummary'] = true;
        $initial_values['enableHoneypot'] = true;
        $initial_values['enableAnimation'] = false;

        return $initial_values;
    }

    public static function spinnerUrl()
    {
        return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    }
}
