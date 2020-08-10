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

use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Asset\TagRendererInterface;
use Ssch\Typo3Encore\Integration\PageRendererHooks;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Integration\PageRendererHooks
 */
final class PageRendererHooksTest extends UnitTestCase
{
    /**
     * @var PageRendererHooks
     */
    protected $subject;

    /**
     * @var MockObject|TagRendererInterface
     */
    protected $tagRenderer;

    /**
     * @var MockObject|PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var bool
     */
    protected $resetSingletonInstances = true;

    protected function setUp(): void
    {
        $this->tagRenderer = $this->getMockBuilder(TagRendererInterface::class)->getMock();
        $this->pageRenderer = $this->getMockBuilder(PageRenderer::class)->getMock();
        $this->subject = new PageRendererHooks($this->tagRenderer);
    }

    /**
     * @test
     */
    public function renderPreProcessWithDefaultBuild(): void
    {
        $params = [
            'jsFiles' => [
                [
                    'file' => 'typo3_encore:app',
                    'forceOnTop' => true,
                    'section' => 2
                ],
            ],
            'cssFiles' => [
                [
                    'file' => 'typo3_encore:app',
                ],
            ],
        ];

        $this->tagRenderer->expects($this->once())->method('renderWebpackScriptTags')->with('app', 'footer', '_default', $this->pageRenderer, ['forceOnTop' => true]);
        $this->tagRenderer->expects($this->once())->method('renderWebpackLinkTags')->with('app', 'all', '_default', $this->pageRenderer);
        $this->subject->renderPreProcess($params, $this->pageRenderer);
    }

    /**
     * @test
     */
    public function renderPreProcessWithDefinedBuild(): void
    {
        $params = [
            'jsFiles' => [
                [
                    'file' => 'typo3_encore:config:app',
                ],
            ],
            'cssFiles' => [
                [
                    'file' => 'typo3_encore:config:app',
                ],
            ],
        ];

        $this->tagRenderer->expects($this->once())->method('renderWebpackScriptTags')->with('app', '', 'config', $this->pageRenderer);
        $this->tagRenderer->expects($this->once())->method('renderWebpackLinkTags')->with('app', 'all', 'config', $this->pageRenderer);
        $this->subject->renderPreProcess($params, $this->pageRenderer);
    }
}
