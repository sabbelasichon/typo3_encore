<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

final readonly class TypoScriptFrontendControllerEventListener
{
    public function __construct(
        private AssetRegistryInterface $assetRegistry,
        private SettingsServiceInterface $settingsService
    ) {
    }

    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        $registeredFiles = $this->assetRegistry->getRegisteredFiles();
        if ([] === $registeredFiles) {
            return;
        }

        $request = $event->getRequest();
        $typoScript = $request->getAttribute('frontend.typoscript');
        if (! $typoScript instanceof FrontendTypoScript) {
            return;
        }

        $configArray = $typoScript->getConfigArray();
        $configArray['encore_asset_registry'] = [
            'registered_files' => $this->assetRegistry->getRegisteredFiles(),
            'default_attributes' => $this->assetRegistry->getDefaultAttributes(),
            'settings' => $this->settingsService->getSettings(),
        ];
        $typoScript->setConfigArray($configArray);
    }
}
