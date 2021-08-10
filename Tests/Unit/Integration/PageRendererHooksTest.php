<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\TagRendererInterface;
use Ssch\Typo3Encore\Integration\PageRendererHooks;
use Ssch\Typo3Encore\ValueObject\LinkTag;
use Ssch\Typo3Encore\ValueObject\ScriptTag;
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
    public function doNothingNoEncoreFiles(): void
    {
        $params = [
            'jsFiles' => [
                [
                    'file' => 'somefile_not_managed_by_encore.js',
                    'forceOnTop' => true,
                    'section' => 2
                ],
            ],
            'cssFiles' => [
                [
                    'file' => 'somefile_not_managed_by_encore.css',
                ],
            ],
        ];

        $this->tagRenderer->expects(self::never())->method('renderWebpackScriptTags');
        $this->tagRenderer->expects(self::never())->method('renderWebpackLinkTags');
        $this->subject->renderPreProcess($params, $this->pageRenderer);
    }

    /**
     * @test
     */
    public function doNothingNoMatchingType(): void
    {
        $params = [
            'foo' => [
                [
                    'file' => 'somefile_not_managed_by_encore.css',
                ],
            ],
        ];

        $this->tagRenderer->expects(self::never())->method('renderWebpackScriptTags');
        $this->tagRenderer->expects(self::never())->method('renderWebpackLinkTags');
        $this->subject->renderPreProcess($params, $this->pageRenderer);
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

        $scriptTag = new ScriptTag('app', 'footer', EntrypointLookupInterface::DEFAULT_BUILD, $this->pageRenderer, ['forceOnTop' => true]);
        $this->tagRenderer->expects(self::once())->method('renderWebpackScriptTags')->with($scriptTag);
        $linkTag = new LinkTag('app', 'all', EntrypointLookupInterface::DEFAULT_BUILD, $this->pageRenderer);
        $this->tagRenderer->expects(self::once())->method('renderWebpackLinkTags')->with($linkTag);
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

        $scriptTag = new ScriptTag('app', '', 'config', $this->pageRenderer);
        $this->tagRenderer->expects(self::once())->method('renderWebpackScriptTags')->with($scriptTag);
        $linkTag = new LinkTag('app', 'all', 'config', $this->pageRenderer);
        $this->tagRenderer->expects(self::once())->method('renderWebpackLinkTags')->with($linkTag);
        $this->subject->renderPreProcess($params, $this->pageRenderer);
    }
}
