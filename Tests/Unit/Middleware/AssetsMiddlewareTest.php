<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Middleware;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use Ssch\Typo3Encore\Middleware\AssetsMiddleware;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class AssetsMiddlewareTest extends UnitTestCase
{
    protected AssetsMiddleware $subject;

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
        parent::setUp();
        $this->typoScriptFrontendController = $this->getMockBuilder(
            TypoScriptFrontendController::class
        )->disableOriginalConstructor()
            ->getMock();
        $this->settingsService = $this->getMockBuilder(SettingsServiceInterface::class)->getMock();
        $this->assetRegistry = $this->getMockBuilder(AssetRegistryInterface::class)->getMock();
        $GLOBALS['TSFE'] = $this->typoScriptFrontendController;
        $this->subject = new AssetsMiddleware($this->assetRegistry, $this->settingsService);
    }

    public function testPreloadingIsDisabled(): void
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

        $request = new ServerRequest();
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = new Response();
        $handler->method('handle')
            ->willReturn($response);
        $this->settingsService->expects(self::once())->method('getSettings')->willReturn([]);
        $this->assetRegistry->method('getRegisteredFiles')
            ->willReturn($registeredFiles);
        $defaultAttributes = [
            'crossorigin' => true,
        ];
        $this->assetRegistry->expects(self::once())->method('getDefaultAttributes')->willReturn($defaultAttributes);

        $returnedResponse = $this->subject->process($request, $handler);

        $links = $returnedResponse->getHeader('Link');
        self::assertCount(0, $links);
    }

    public function testNullResponseAndControllerIsNotOutputting(): void
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = $this->getMockBuilder(NullResponse::class)->getMock();
        $handler->method('handle')
            ->willReturn($response);
        $this->assetRegistry->expects(self::never())->method('getRegisteredFiles');

        $returnedResponse = $this->subject->process($request, $handler);

        self::assertEquals($response, $returnedResponse);
    }

    public function testNoAssetsRegistered(): void
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $handler->method('handle')
            ->willReturn($response);
        $this->assetRegistry->expects(self::once())->method('getRegisteredFiles')->willReturn([]);
        $this->assetRegistry->expects(self::never())->method('getDefaultAttributes');

        $returnedResponse = $this->subject->process($request, $handler);

        self::assertEquals($response, $returnedResponse);
    }

    public function testAddPreloadingHeader(): void
    {
        $registeredFiles = [
            'preload' => [
                'files' => [
                    'style' => [
                        'file1.css' => [
                            'crossorigin' => true,
                        ],
                        'file2.css' => [
                            'crossorigin' => true,
                        ],
                    ],
                    'script' => [
                        'file1.js' => [
                            'crossorigin' => true,
                        ],
                        'file2.js' => [
                            'crossorigin' => true,
                        ],
                    ],
                ],
            ],
            'dns-prefetch' => [
                'files' => [
                    'style' => [
                        'file1.css' => [],
                        'file2.css' => [],
                    ],
                    'script' => [
                        'file1.js' => [],
                        'file2.js' => [],
                    ],
                ],
            ],
        ];

        $request = new ServerRequest();
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = new Response();
        $handler->method('handle')
            ->willReturn($response);
        $this->settingsService->method('getSettings')
            ->willReturn([
                'array' => 'should-not-be-empty',
            ]);
        $this->settingsService->method('getBooleanByPath')
            ->willReturn(true);
        $this->assetRegistry->method('getRegisteredFiles')
            ->willReturn($registeredFiles);
        $defaultAttributes = [
            'crossorigin' => true,
        ];
        $this->assetRegistry->expects(self::once())->method('getDefaultAttributes')->willReturn($defaultAttributes);

        $returnedResponse = $this->subject->process($request, $handler);

        $links = $returnedResponse->getHeader('Link');
        self::assertCount(1, $links);
    }
}
