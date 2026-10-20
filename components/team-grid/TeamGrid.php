<?php

namespace Gust\Components;

use Gust\ComponentBase;

class TeamGrid extends ComponentBase
{
    protected static string $name = 'team-grid';

    public static function make(
        array $args = [],
    ): ?static {
        return static::createFromArgs($args);
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['team_members']);
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['team_members'])) {
            $args['items'] = [];
            foreach ($args['team_members'] as $member) {
                $args['items'][] = [
                    'name' => $member['name'] ?? '',
                    'role' => $member['role'] ?? '',
                    'bio' => $member['bio'] ?? '',
                    'image' => $member['image'] ?? null,
                ];
            }
        }

        return $args;
    }
}
