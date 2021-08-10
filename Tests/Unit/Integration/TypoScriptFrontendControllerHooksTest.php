<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use Ssch\Typo3Encore\Integration\TypoScriptFrontendControllerHooks;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Integration\TypoScriptFrontendControllerHooks
 */
final class TypoScriptFrontendControllerHooksTest extends UnitTestCase
{
    protected TypoScriptFrontendControllerHooks $subject;

    /**
     * @var MockObject|TypoScriptFrontendController
     */
    protected $typoScriptFrontendController;

    /**
     * @var MockObject|SettingsServiceInterface
     */
    protected $settingsService;

    /**
     * @var AssetRegistryInterface|MockObject
     */
    protected $assetRegistry;

    protected function setUp(): void
    {
        $this->typoScriptFrontendController = $this->getMockBuilder(TypoScriptFrontendController::class)->disableOriginalConstructor()->getMock();
        $this->settingsService = $this->getMockBuilder(SettingsServiceInterface::class)->getMock();
        $this->assetRegistry = $this->getMockBuilder(AssetRegistryInterface::class)->getMock();
        $GLOBALS['TSFE'] = $this->typoScriptFrontendController;
        $this->subject = new TypoScriptFrontendControllerHooks($this->assetRegistry, $this->settingsService);
    }

    /**
     * @test
     */
    public function registryDoesNotContainFiles(): void
    {
        $this->assetRegistry->expects(self::once())->method('getRegisteredFiles')->willReturn([]);
        $this->assetRegistry->expects(self::never())->method('getDefaultAttributes');
        $this->settingsService->expects(self::never())->method('getSettings');
        $this->subject->contentPostProcAll([], $this->typoScriptFrontendController);

        self::assertArrayNotHasKey('encore_asset_registry', $this->typoScriptFrontendController->config);
    }

    /**
     * @test
     */
    public function registryContainsFiles(): void
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

        $this->assetRegistry->expects(self::exactly(2))->method('getRegisteredFiles')->willReturn($registeredFiles);
        $this->assetRegistry->expects(self::once())->method('getDefaultAttributes')->willReturn($defaultAttributes);
        $this->settingsService->expects(self::once())->method('getSettings')->willReturn($settings);
        $this->subject->contentPostProcAll([], $this->typoScriptFrontendController);

        self::assertArrayHasKey('encore_asset_registry', $this->typoScriptFrontendController->config);
        self::assertSame($registeredFiles, $this->typoScriptFrontendController->config['encore_asset_registry']['registered_files']);
        self::assertSame($defaultAttributes, $this->typoScriptFrontendController->config['encore_asset_registry']['default_attributes']);
        self::assertSame($settings, $this->typoScriptFrontendController->config['encore_asset_registry']['settings']);
    }
}
