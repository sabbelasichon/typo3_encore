<?php

return [
    'frontend' => [
        'ssch/typo3-encore-handler' => [
            'target' => Ssch\Typo3Encore\Middleware\PreloadAssetsMiddleware::class,
            'description' => 'Add HTTP/2 Push functionality for assets managed by encore',
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
        ],
    ],
];
