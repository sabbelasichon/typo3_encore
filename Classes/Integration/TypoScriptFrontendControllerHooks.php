<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Integration;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

final class TypoScriptFrontendControllerHooks
{
    /**
     * @var TypoScriptFrontendController
     */
    protected $controller;

    /**
     * @var AssetRegistryInterface
     */
    private $assetRegistry;

    /**
     * @var SettingsServiceInterface
     */
    private $settingsService;

    public function __construct(TypoScriptFrontendController $controller = null, AssetRegistryInterface $assetRegistry = null, SettingsServiceInterface $settingsService = null)
    {
        $this->controller = $controller ?? $GLOBALS['TSFE'];

        if (! $assetRegistry instanceof AssetRegistryInterface) {
            // @codeCoverageIgnoreStart
            /** @var AssetRegistryInterface $assetRegistry */
            $assetRegistry = GeneralUtility::makeInstance(ObjectManager::class)->get(AssetRegistryInterface::class);
            // @codeCoverageIgnoreEnd
        }

        if (! $settingsService instanceof SettingsServiceInterface) {
            // @codeCoverageIgnoreStart
            /** @var SettingsServiceInterface $settingsService */
            $settingsService = GeneralUtility::makeInstance(ObjectManager::class)->get(SettingsServiceInterface::class);
            // @codeCoverageIgnoreEnd
        }

        $this->settingsService = $settingsService;
        $this->assetRegistry = $assetRegistry;
    }

    public function contentPostProcAll(array $params, TypoScriptFrontendController $tsfe)
    {
        if (! $this->assetRegistry->getRegisteredFiles()) {
            return;
        }

        $this->controller->config['encore_asset_registry'] = [
            'registered_files' => $this->assetRegistry->getRegisteredFiles(),
            'default_attributes' => $this->assetRegistry->getDefaultAttributes(),
            'settings' => $this->settingsService->getSettings(),
        ];
    }
}
