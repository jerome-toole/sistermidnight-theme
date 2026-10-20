<?php

use Gust\Components\TeamGrid;

?>

<h2>Team Grid - With Images</h2>
<?= TeamGrid::make([
    'heading' => 'Our Team',
    'team_members' => [
        [
            'name' => 'Mazzy',
            'role' => 'Creative Director',
            'bio' => '<p>Mazzy is a born rumbled Catford resident who has immersed in the world industry in Lewisham for many years, she\'s now turned to promoting live music shows.</p>',
        ],
        [
            'name' => 'Ralph',
            'role' => 'Venue Operations',
            'bio' => '<p>Ralph brings fifteen years of experience in venue management and community programming.</p>',
        ],
        [
            'name' => 'Juno',
            'role' => 'Community Manager',
            'bio' => '<p>Juno connects with local artists and coordinates our community outreach programs.</p>',
        ],
    ],
]); ?>

<h2>Team Grid - 2 Column</h2>
<div class="color-context-paper">
<?= TeamGrid::make([
    'heading' => 'Leadership',
    'team_members' => [
        [
            'name' => 'Alex Chen',
            'role' => 'Board Chair',
            'bio' => '<p>Alex oversees strategic direction and governance.</p>',
        ],
        [
            'name' => 'Sam Okonkwo',
            'role' => 'Treasurer',
            'bio' => '<p>Sam manages our community share finances and reporting.</p>',
        ],
    ],
]); ?>
</div>
