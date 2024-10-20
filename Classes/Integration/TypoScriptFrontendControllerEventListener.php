<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

final class TypoScriptFrontendControllerEventListener
{
    public function __construct(
        private readonly AssetRegistryInterface $assetRegistry,
        private readonly SettingsServiceInterface $settingsService
    ) {
    }

    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        $registeredFiles = $this->assetRegistry->getRegisteredFiles();
        if ($registeredFiles === []) {
            return;
        }

        $event->getController()
            ->config['encore_asset_registry'] = [
                'registered_files' => $this->assetRegistry->getRegisteredFiles(),
                'default_attributes' => $this->assetRegistry->getDefaultAttributes(),
                'settings' => $this->settingsService->getSettings(),
            ];
    }
}
