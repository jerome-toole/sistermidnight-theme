<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * FAQ Component (ACF block `theme/faq`)
 *
 * Heading ("FAQ", h2 styled type-h3) and a list of questions, each a native
 * <details>/<summary> disclosure. No `name` attribute, so several answers
 * can be open at once.
 *
 * In the editor the heading is typed in place; the items repeater lives in
 * an inline toolbar on the list and every item renders open.
 *
 * Usage:
 *   use Gust\Components\Faq;
 *
 *   echo Faq::make(heading: 'FAQ', items: [
 *       ['question' => 'Will there be food?', 'answer' => '<p>Yes.</p>'],
 *   ]);
 */
class Faq extends ComponentBase
{
    protected static string $name = 'faq';

    protected static function getDefaults(): array
    {
        return [
            'heading' => '',
            'items' => [],
        ];
    }

    /**
     * Create a new FAQ component.
     *
     * @param  string  $heading  Heading above the items.
     * @param  array  $items  Rows of question (text) and answer (HTML).
     * @param  bool  $open  Render every item open (used in the editor preview).
     * @return static|null Null when there are no items (outside the editor).
     */
    public static function make(
        string $heading = '',
        array $items = [],
        bool $open = false,
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

        return ! empty($args['is_preview']) || static::cleanItems($args['items'] ?? []) !== [];
    }

    protected static function transform(array $args): array
    {
        $args = static::fromBlockFields($args);

        $args['heading'] = trim((string) ($args['heading'] ?? ''));
        $args['items'] = static::cleanItems($args['items'] ?? []);
        $args['open'] = ! empty($args['open']) || ! empty($args['is_preview']);
        $args['attributes'] ??= [];
        $args['classes'] = array_merge(['faq'], $args['classes'] ?? []);

        return $args;
    }

    /** Keep rows with a question, trimmed. */
    protected static function cleanItems(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        $clean = [];

        foreach ($items as $item) {
            $question = trim((string) ($item['question'] ?? ''));

            if ($question === '') {
                continue;
            }

            $clean[] = [
                'question' => $question,
                'answer' => trim((string) ($item['answer'] ?? '')),
            ];
        }

        return $clean;
    }

    /** Block fields are prefixed (faq_heading…) so their names stay unique for inline editing. */
    protected static function fromBlockFields(array $args): array
    {
        foreach (['heading', 'items'] as $field) {
            if (array_key_exists("faq_{$field}", $args)) {
                $args[$field] = $args["faq_{$field}"] ?? $args[$field] ?? null;
                unset($args["faq_{$field}"]);
            }
        }

        return $args;
    }

    /** ACF inline text editing attributes for a block field (name without the faq_ prefix). Empty outside an editor preview. */
    public function inlineText(string $field, string $placeholder): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_text_editing_attrs')) {
            return '';
        }

        return \acf_inline_text_editing_attrs("faq_{$field}", ['placeholder' => $placeholder]);
    }

    /** ACF inline toolbar attributes for a set of block fields (names without the faq_ prefix). */
    public function inlineToolbar(array $fields, string $title): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_toolbar_editing_attrs')) {
            return '';
        }

        $fields = array_map(fn (string $field) => "faq_{$field}", $fields);

        return (string) \acf_inline_toolbar_editing_attrs($fields, ['toolbar_title' => $title]);
    }
}
