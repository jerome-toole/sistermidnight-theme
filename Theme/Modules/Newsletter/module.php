<?php

namespace Theme\Modules\Newsletter;

class NewsletterModule
{
    public static function init(): void
    {
        \add_action('rest_api_init', [__CLASS__, 'registerEndpoint']);
        \add_action('acf/include_fields', [__CLASS__, 'registerFields']);

        // Newsletter form: primary Button (label + arrow) instead of GF's <input type="submit">
        // https://docs.gravityforms.com/gform_submit_button/
        \add_filter('gform_submit_button', [\Gust\Components\NewsletterForm::class, 'submitButton'], 10, 2);
    }

    public static function registerEndpoint(): void
    {
        \register_rest_route('theme/v1', '/newsletter', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handleSubscribe'],
            'permission_callback' => '__return_true',
            'args' => [
                'email' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_email',
                    'validate_callback' => function ($email) {
                        return \is_email($email);
                    },
                ],
                'list_id' => [
                    'required' => false,
                    'type' => 'integer',
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);
    }

    public static function handleSubscribe(\WP_REST_Request $request): \WP_REST_Response
    {
        $email = $request->get_param('email');
        $list_id = $request->get_param('list_id');

        $api_key = \get_field('brevo_api_key', 'option');

        if (empty($api_key)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Newsletter service not configured.', 'gust'),
            ], 500);
        }

        // Get default list ID from settings if not provided
        if (empty($list_id)) {
            $list_id = \get_field('brevo_default_list_id', 'option');
        }

        if (empty($list_id)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Newsletter list not configured.', 'gust'),
            ], 500);
        }

        $result = self::subscribeToBrevo($email, (int) $list_id, $api_key);

        if (\is_wp_error($result)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => $result->get_error_message(),
            ], 400);
        }

        return new \WP_REST_Response([
            'success' => true,
            'message' => __('Thanks for subscribing!', 'gust'),
        ], 200);
    }

    private static function subscribeToBrevo(string $email, int $list_id, string $api_key): bool|\WP_Error
    {
        $response = \wp_remote_post('https://api.brevo.com/v3/contacts', [
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $api_key,
                'content-type' => 'application/json',
            ],
            'body' => \wp_json_encode([
                'email' => $email,
                'listIds' => [$list_id],
                'updateEnabled' => true,
            ]),
        ]);

        if (\is_wp_error($response)) {
            return new \WP_Error('brevo_error', __('Could not connect to newsletter service.', 'gust'));
        }

        $code = \wp_remote_retrieve_response_code($response);
        $body = \json_decode(\wp_remote_retrieve_body($response), true);

        // 201 = created, 204 = updated existing
        if ($code === 201 || $code === 204) {
            return true;
        }

        // Handle duplicate (already subscribed)
        if ($code === 400 && isset($body['code']) && $body['code'] === 'duplicate_parameter') {
            return true; // Treat as success
        }

        $message = $body['message'] ?? __('Subscription failed. Please try again.', 'gust');

        return new \WP_Error('brevo_error', $message);
    }

    public static function registerFields(): void
    {
        if (! \function_exists('acf_add_local_field_group')) {
            return;
        }

        \acf_add_local_field_group([
            'key' => 'group_newsletter_settings',
            'title' => 'Newsletter Settings',
            'fields' => [
                [
                    'key' => 'field_brevo_api_key',
                    'label' => 'Brevo API Key',
                    'name' => 'brevo_api_key',
                    'type' => 'text',
                    'instructions' => 'Get your API key from Brevo dashboard > SMTP & API > API Keys',
                ],
                [
                    'key' => 'field_brevo_default_list_id',
                    'label' => 'Default List ID',
                    'name' => 'brevo_default_list_id',
                    'type' => 'number',
                    'instructions' => 'The Brevo contact list ID for newsletter signups',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'acf-options-general',
                    ],
                ],
            ],
            'menu_order' => 50,
        ]);
    }
}
