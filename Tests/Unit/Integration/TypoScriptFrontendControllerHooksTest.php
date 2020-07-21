<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

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

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use Ssch\Typo3Encore\Integration\TypoScriptFrontendControllerHooks;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @covers \Ssch\Typo3Encore\Integration\TypoScriptFrontendControllerHooks
 */
final class TypoScriptFrontendControllerHooksTest extends UnitTestCase
{
    /**
     * @var TypoScriptFrontendControllerHooks
     */
    protected $subject;

    /**
     * @var MockObject|TypoScriptFrontendController
     */
    private $typoScriptFrontendController;

    /**
     * @var MockObject|SettingsServiceInterface
     */
    private $settingsService;

    /**
     * @var AssetRegistryInterface|MockObject
     */
    private $assetRegistry;

    protected function setUp()
    {
        $this->typoScriptFrontendController = $this->getMockBuilder(TypoScriptFrontendController::class)->disableOriginalConstructor()->getMock();
        $this->settingsService = $this->getMockBuilder(SettingsServiceInterface::class)->getMock();
        $this->assetRegistry = $this->getMockBuilder(AssetRegistryInterface::class)->getMock();
        $this->subject = new TypoScriptFrontendControllerHooks($this->typoScriptFrontendController, $this->assetRegistry, $this->settingsService);
    }

    /**
     * @test
     */
    public function registryDoesNotContainFiles()
    {
        $this->assetRegistry->expects($this->once())->method('getRegisteredFiles')->willReturn([]);
        $this->assetRegistry->expects($this->never())->method('getDefaultAttributes');
        $this->settingsService->expects($this->never())->method('getSettings');
        $this->subject->contentPostProcAll([], $this->typoScriptFrontendController);

        $this->assertArrayNotHasKey('encore_asset_registry', $this->typoScriptFrontendController->config);
    }

    /**
     * @test
     */
    public function registryContainsFiles()
    {
        $registeredFiles = [
            'preload' => [
                'files' => [
                    'style' => [
                        'file1.css' => [
                            'crossorigin' => true,
                        ],
                        'script' => [
                            'file2.js' => [
                                'crossorigin' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $defaultAttributes = ['crossorigin' => true];
        $settings = [
            'preload' => [
                'enable' => true,
            ],
        ];

        $this->assetRegistry->expects($this->exactly(2))->method('getRegisteredFiles')->willReturn($registeredFiles);
        $this->assetRegistry->expects($this->once())->method('getDefaultAttributes')->willReturn($defaultAttributes);
        $this->settingsService->expects($this->once())->method('getSettings')->willReturn($settings);
        $this->subject->contentPostProcAll([], $this->typoScriptFrontendController);

        $this->assertArrayHasKey('encore_asset_registry', $this->typoScriptFrontendController->config);
        $this->assertSame($registeredFiles, $this->typoScriptFrontendController->config['encore_asset_registry']['registered_files']);
        $this->assertSame($defaultAttributes, $this->typoScriptFrontendController->config['encore_asset_registry']['default_attributes']);
        $this->assertSame($settings, $this->typoScriptFrontendController->config['encore_asset_registry']['settings']);
    }
}
