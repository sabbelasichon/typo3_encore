<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase

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
    /**
     * @var TypoScriptFrontendControllerHooks
     */
    protected $subject;

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
        $this->subject = new TypoScriptFrontendControllerHooks($this->typoScriptFrontendController, $this->assetRegistry, $this->settingsService);
    }

    /**
     * @test
     */
    public function registryDoesNotContainFiles(): void
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
