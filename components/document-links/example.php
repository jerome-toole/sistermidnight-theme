<?php

use Geum\Components\DocumentLinks;

?>

<h2>Document Links</h2>

<h3>Basic Usage</h3>
<?= DocumentLinks::make([
    'heading' => 'Important Documents',
    'documents' => [
        [
            'title' => 'Share Offer Document',
            'file' => [
                'url' => '#',
                'filename' => 'share-offer-2026.pdf',
                'filesize' => 2048000,
                'subtype' => 'pdf',
            ],
        ],
        [
            'title' => 'Terms & Conditions',
            'file' => [
                'url' => '#',
                'filename' => 'terms-and-conditions.pdf',
                'filesize' => 512000,
                'subtype' => 'pdf',
            ],
        ],
        [
            'title' => 'Business Plan',
            'file' => [
                'url' => '#',
                'filename' => 'business-plan-2026.pdf',
                'filesize' => 5120000,
                'subtype' => 'pdf',
            ],
        ],
    ],
]) ?>

<h3>Without Heading</h3>
<?= DocumentLinks::make([
    'documents' => [
        [
            'title' => 'Annual Report',
            'file' => [
                'url' => '#',
                'filename' => 'annual-report.pdf',
                'filesize' => 1024000,
                'subtype' => 'pdf',
            ],
        ],
    ],
]) ?>

<h3>Using Filename as Title</h3>
<?= DocumentLinks::make([
    'heading' => 'Downloads',
    'documents' => [
        [
            'title' => '',
            'file' => [
                'url' => '#',
                'filename' => 'membership-form.docx',
                'filesize' => 256000,
                'subtype' => 'docx',
            ],
        ],
    ],
]) ?>

<h3>With Background Color</h3>
<div class="color-context-paper">
    <?= DocumentLinks::make([
        'heading' => 'Financial Documents',
        'documents' => [
            [
                'title' => 'Investment Summary',
                'file' => [
                    'url' => '#',
                    'filename' => 'investment-summary.xlsx',
                    'filesize' => 128000,
                    'subtype' => 'xlsx',
                ],
            ],
            [
                'title' => 'Asset Register',
                'file' => [
                    'url' => '#',
                    'filename' => 'assets.zip',
                    'filesize' => 10240000,
                    'subtype' => 'zip',
                ],
            ],
        ],
    ]) ?>
</div>
