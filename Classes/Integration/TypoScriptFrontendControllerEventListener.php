<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

use Ssch\Typo3Encore\Service\CacheService;
use TYPO3\CMS\Core\Cache\CacheDataCollector;
use TYPO3\CMS\Frontend\Event\AfterCachedPageIsPersistedEvent;

final readonly class TypoScriptFrontendControllerEventListener
{
    public function __construct(
        private AssetRegistryInterface $assetRegistry,
        private SettingsServiceInterface $settingsService,
        private CacheService $cacheService,
    ) {
    }

    public function __invoke(AfterCachedPageIsPersistedEvent $event): void
    {
        $registeredFiles = $this->assetRegistry->getRegisteredFiles();
        if ([] === $registeredFiles) {
            return;
        }

        $cacheEntry = [
            'registered_files' => $this->assetRegistry->getRegisteredFiles(),
            'default_attributes' => $this->assetRegistry->getDefaultAttributes(),
            'settings' => $this->settingsService->getSettings(),
        ];
        $cacheDataCollector = $event->getRequest()
            ->getAttribute('frontend.cache.collector');
        if ($cacheDataCollector instanceof CacheDataCollector) {
            $this->cacheService->set(
                $cacheDataCollector,
                $cacheEntry,
                $event->getCacheData()['tags'] ?? [],
                $event->getCacheLifetime()
            );
        }
    }
}
