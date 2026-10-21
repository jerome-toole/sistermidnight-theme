<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * Team Component (ACF block `theme/team`)
 *
 * Stone-beige section with a vertical label and a horizontal, snapping row
 * of portraits. Each portrait carries a first-name caption placed and
 * rotated per member (name_x / name_y as a percentage of the portrait,
 * name_rotation in degrees, rendered as CSS custom properties); the caption
 * is a <button aria-expanded> that reveals a panel with the full name and
 * bio. CSS reveals the panel on hover and :focus-within; scripts.js keeps it
 * open on click/tap and makes it follow the pointer on hover.
 *
 * Usage:
 *   use Gust\Components\Team;
 *
 *   echo Team::make(label: 'The Team', members: [
 *       ['image' => 12, 'first_name' => 'Sophie', 'full_name' => 'Sophie Farrell', 'bio' => '…', 'name_x' => 27, 'name_y' => 32, 'name_rotation' => -30],
 *   ]);
 */
class Team extends ComponentBase
{
    protected static string $name = 'team';

    /** Caption defaults when a member has no position saved (percent, percent, degrees). */
    public const NAME_X = 8.0;

    public const NAME_Y = 8.0;

    public const NAME_ROTATION = 0.0;

    protected static function getDefaults(): array
    {
        return [
            'label' => '',
            'members' => [],
        ];
    }

    /**
     * Create a new Team component.
     *
     * @param  string  $label  Vertical gutter label, rendered as the <h2>. Optional.
     * @param  array  $members  Rows of image (ACF image array or ID), first_name, full_name, bio, name_x, name_y, name_rotation.
     * @return static|null Null when there are no members (outside the editor).
     */
    public static function make(
        string $label = '',
        array $members = [],
        bool $is_preview = false,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        $args = static::fromBlockFields($args);

        return ! empty($args['is_preview']) || static::members($args['members'] ?? [], 'x') !== [];
    }

    protected static function transform(array $args): array
    {
        $args = static::fromBlockFields($args);

        $args['label'] = trim((string) ($args['label'] ?? ''));
        $args['id'] = \wp_unique_id('team-');
        $args['members'] = static::members($args['members'] ?? [], $args['id']);
        $args['attributes'] ??= [];
        $args['classes'] = array_merge([
            'team',
            'alignfull',
            'has-background',
            'has-stone-beige-background-color',
        ], $args['classes'] ?? []);

        if ($args['label'] !== '' || ! empty($args['is_preview'])) {
            $args['classes'][] = 'team--labelled';
        }

        return $args;
    }

    /** Keep rows with a first name; render the image; drop the panel when it would only repeat the caption. */
    protected static function members(mixed $members, string $prefix): array
    {
        if (! is_array($members)) {
            return [];
        }

        $clean = [];

        foreach ($members as $index => $member) {
            $first_name = trim((string) ($member['first_name'] ?? ''));

            if ($first_name === '') {
                continue;
            }

            $image = $member['image'] ?? null;
            $image_id = is_array($image) ? (int) ($image['ID'] ?? $image['id'] ?? 0) : (int) $image;
            $full_name = trim((string) ($member['full_name'] ?? ''));
            $bio = trim((string) ($member['bio'] ?? ''));

            $clean[] = [
                'first_name' => $first_name,
                'full_name' => $full_name !== '' ? $full_name : $first_name,
                'bio' => $bio,
                'has_panel' => $bio !== '' || ($full_name !== '' && $full_name !== $first_name),
                'panel_id' => "{$prefix}-".($index + 1),
                'style' => sprintf(
                    '--team--name-x: %s%%; --team--name-y: %s%%; --team--name-rotation: %sdeg;',
                    static::number($member['name_x'] ?? null, self::NAME_X, 0, 100),
                    static::number($member['name_y'] ?? null, self::NAME_Y, 0, 100),
                    static::number($member['name_rotation'] ?? null, self::NAME_ROTATION, -90, 90),
                ),
                'image' => $image_id > 0 && \wp_attachment_is_image($image_id)
                    ? Image::make(
                        id: $image_id,
                        size: 'medium_large',
                        sizes: '260px',
                        attributes: ['class' => 'team__img'],
                    )
                    : null,
            ];
        }

        return $clean;
    }

    /** A numeric field value clamped to its range, or the default when empty or not numeric. Formatted for CSS. */
    protected static function number(mixed $value, float $default, float $min, float $max): string
    {
        $number = is_numeric($value) ? (float) $value : $default;

        return rtrim(rtrim(number_format(max($min, min($max, $number)), 2, '.', ''), '0'), '.');
    }

    /** Block fields are prefixed (team_label…) so their names stay unique for inline editing. */
    protected static function fromBlockFields(array $args): array
    {
        foreach (['label', 'members'] as $field) {
            if (array_key_exists("team_{$field}", $args)) {
                $args[$field] = $args["team_{$field}"] ?? $args[$field] ?? null;
                unset($args["team_{$field}"]);
            }
        }

        return $args;
    }

    /** ACF inline text editing attributes for a block field (name without the team_ prefix). Empty outside an editor preview. */
    public function inlineText(string $field, string $placeholder): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_text_editing_attrs')) {
            return '';
        }

        return \acf_inline_text_editing_attrs("team_{$field}", ['placeholder' => $placeholder]);
    }

    /** ACF inline toolbar attributes for a set of block fields (names without the team_ prefix). */
    public function inlineToolbar(array $fields, string $title): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_toolbar_editing_attrs')) {
            return '';
        }

        $fields = array_map(fn (string $field) => "team_{$field}", $fields);

        return (string) \acf_inline_toolbar_editing_attrs($fields, ['toolbar_title' => $title]);
    }
}
