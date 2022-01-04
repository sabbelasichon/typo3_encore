<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'frontend' => [
        'ssch/typo3-encore-handler' => [
            'target' => Ssch\Typo3Encore\Middleware\AssetsMiddleware::class,
            'description' => 'Add HTTP/2 Push functionality for assets managed by encore',
            'after' => ['typo3/cms-frontend/prepare-tsfe-rendering'],
        ],
    ],
];
