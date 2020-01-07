<?php

namespace Ssch\Typo3Encore\Tests\Unit\Middleware;

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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use Ssch\Typo3Encore\Middleware\PreloadAssetsMiddleware;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class PreloadAssetsMiddlewareTest extends UnitTestCase
{
    /**
     * @var PreloadAssetsMiddleware
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
        $this->subject = new PreloadAssetsMiddleware($this->typoScriptFrontendController, $this->assetRegistry, $this->settingsService);
    }

    /**
     * @test
     */
    public function preloadingIsDisabled(): void
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $handler->method('handle')->willReturn($response);
        $this->settingsService->expects($this->once())->method('getByPath')->willReturn(false);
        $this->assetRegistry->expects($this->never())->method('getRegisteredFiles');

        $returnedResponse = $this->subject->process($request, $handler);

        $this->assertEquals($response, $returnedResponse);
    }

    /**
     * @test
     */
    public function nullResponseAndControllerIsNotOutputting(): void
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = $this->getMockBuilder(NullResponse::class)->getMock();
        $handler->method('handle')->willReturn($response);
        $this->settingsService->expects($this->once())->method('getByPath')->willReturn(true);
        $this->typoScriptFrontendController->expects($this->once())->method('isOutputting')->willReturn(false);
        $this->assetRegistry->expects($this->never())->method('getRegisteredFiles');

        $returnedResponse = $this->subject->process($request, $handler);

        $this->assertEquals($response, $returnedResponse);
    }

    /**
     * @test
     */
    public function noAssetsRegistered(): void
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $handler->method('handle')->willReturn($response);
        $this->settingsService->expects($this->once())->method('getByPath')->willReturn(true);
        $this->assetRegistry->expects($this->once())->method('getRegisteredFiles')->willReturn([]);
        $this->assetRegistry->expects($this->never())->method('getDefaultAttributes');

        $returnedResponse = $this->subject->process($request, $handler);

        $this->assertEquals($response, $returnedResponse);
    }

    /**
     * @test
     */
    public function addPreloadingHeader(): void
    {
        $registeredFiles = [
            'style' => [
                'file1.css' => [],
                'file2.css' => [],
            ],
            'script' => [
                'file1.js' => [],
                'file2.js' => [],
            ],
        ];

        $request = new ServerRequest();
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = new Response();
        $handler->method('handle')->willReturn($response);
        $this->settingsService->expects($this->once())->method('getByPath')->willReturn(true);
        $this->assetRegistry->method('getRegisteredFiles')->willReturn($registeredFiles);
        $this->assetRegistry->expects($this->once())->method('getDefaultAttributes');

        $returnedResponse = $this->subject->process($request, $handler);

        $links = $returnedResponse->getHeader('Link');
        $firstLink = array_shift($links);
        $this->assertEquals('<typo3/sysext/core/bin/file1.css>; rel="preload"; as="style",<typo3/sysext/core/bin/file2.css>; rel="preload"; as="style",<typo3/sysext/core/bin/file1.js>; rel="preload"; as="script",<typo3/sysext/core/bin/file2.js>; rel="preload"; as="script"', $firstLink);
    }
}
