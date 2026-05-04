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
use Ssch\Typo3Encore\Service\CacheService;
use TYPO3\CMS\Core\Cache\CacheDataCollector;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class AssetsMiddlewareTest extends UnitTestCase
{
    protected AssetsMiddleware $subject;

    /**
     * @var MockObject|SettingsServiceInterface
     */
    protected $settingsService;

    /**
     * @var AssetRegistryInterface|MockObject
     */
    protected $assetRegistry;

    /**
     * @var ServerRequestInterface|MockObject
     */
    protected $request;

    /**
     * @var CacheService|MockObject
     */
    protected $cacheService;

    /**
     * @var object
     */
    protected $cacheDataCollector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingsService = $this->getMockBuilder(SettingsServiceInterface::class)->getMock();
        $this->assetRegistry = $this->getMockBuilder(AssetRegistryInterface::class)->getMock();
        $this->request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

        $pageCache = $this->getMockBuilder(FrontendInterface::class)->getMock();
        $pageCache->method('get')
            ->willReturn([]);
        $runtimeCache = $this->getMockBuilder(FrontendInterface::class)->getMock();
        $runtimeCache->method('get')
            ->willReturn('');

        $this->cacheService = new CacheService($pageCache, $runtimeCache);

        $cacheDataCollector = new CacheDataCollector();
        // Set a page cache identifier to avoid LogicException
        // @phpstan-ignore-next-line - method exists in TYPO3 14 but not in TYPO3 13
        if (method_exists($cacheDataCollector, 'setPageCacheIdentifier')) {
            $cacheDataCollector->setPageCacheIdentifier('test-cache-identifier');
        }

        // Setup request mock to return different values for different attributes
        $this->request->method('getAttribute')
            ->willReturnCallback(function ($attributeName) use ($cacheDataCollector) {
                if ('frontend.cache.collector' === $attributeName) {
                    return $cacheDataCollector;
                }
                return null;
            });

        $this->subject = new AssetsMiddleware($this->assetRegistry, $this->settingsService, $this->cacheService);
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

        $returnedResponse = $this->subject->process($this->request, $handler);

        $links = $returnedResponse->getHeader('Link');
        self::assertCount(0, $links);
    }

    public function testNullResponseAndControllerIsNotOutputting(): void
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = $this->getMockBuilder(NullResponse::class)->getMock();
        $handler->method('handle')
            ->willReturn($response);
        $this->assetRegistry->expects(self::never())->method('getRegisteredFiles');

        $returnedResponse = $this->subject->process($this->request, $handler);

        self::assertEquals($response, $returnedResponse);
    }

    public function testNoAssetsRegistered(): void
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $handler->method('handle')
            ->willReturn($response);
        $this->assetRegistry->expects(self::once())->method('getRegisteredFiles')->willReturn([]);
        $this->assetRegistry->expects(self::never())->method('getDefaultAttributes');

        $returnedResponse = $this->subject->process($this->request, $handler);

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

        $returnedResponse = $this->subject->process($this->request, $handler);

        $links = $returnedResponse->getHeader('Link');
        self::assertCount(1, $links);
    }
}
