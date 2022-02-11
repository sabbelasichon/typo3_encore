<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

final class TypoScriptFrontendControllerHooks
{
    /**
     * @var TypoScriptFrontendController
     */
    private $controller;

    private AssetRegistryInterface $assetRegistry;

    private SettingsServiceInterface $settingsService;

    public function __construct(AssetRegistryInterface $assetRegistry, SettingsServiceInterface $settingsService)
    {
        $this->controller = $GLOBALS['TSFE'];
        $this->settingsService = $settingsService;
        $this->assetRegistry = $assetRegistry;
    }

    public function contentPostProcAll(array $params, TypoScriptFrontendController $tsfe): void
    {
        $registeredFiles = $this->assetRegistry->getRegisteredFiles();
        if ([] === $registeredFiles) {
            return;
        }

        $this->controller->config['encore_asset_registry'] = [
            'registered_files' => $this->assetRegistry->getRegisteredFiles(),
            'default_attributes' => $this->assetRegistry->getDefaultAttributes(),
            'settings' => $this->settingsService->getSettings(),
        ];
    }
}
