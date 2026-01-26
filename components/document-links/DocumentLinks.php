<?php

namespace Geum\Components;

use Geum\ComponentBase;

class DocumentLinks extends ComponentBase
{
    protected static string $name = 'document-links';

    public static function make(
        array $args = [],
    ): ?static {
        return static::createFromArgs($args);
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['documents']);
    }

    protected static function transform(array $args): array
    {
        // Process documents to add file metadata
        if (! empty($args['documents'])) {
            foreach ($args['documents'] as &$doc) {
                if (! empty($doc['file'])) {
                    $file = $doc['file'];
                    $doc['url'] = $file['url'] ?? '';
                    $doc['filename'] = $file['filename'] ?? '';
                    $doc['filesize'] = isset($file['filesize']) ? size_format($file['filesize']) : '';
                    $doc['subtype'] = strtoupper($file['subtype'] ?? '');
                }
            }
        }

        return $args;
    }
}
