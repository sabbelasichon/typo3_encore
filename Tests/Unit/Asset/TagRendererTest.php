<?php

namespace Ssch\Typo3Encore\Tests\Unit\Asset;

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
use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\TagRenderer;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * @covers \Ssch\Typo3Encore\Asset\TagRenderer
 */
class TagRendererTest extends UnitTestCase
{
    /**
     * @var TagRenderer
     */
    protected $subject;

    /**
     * @var MockObject|PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var EntrypointLookupCollectionInterface|MockObject
     */
    protected $entryLookupCollection;

    protected function setUp()
    {
        $this->pageRenderer = $this->getMockBuilder(PageRenderer::class)->getMock();
        $this->entryLookupCollection = $this->getMockBuilder(EntrypointLookupCollectionInterface::class)->getMock();
        $this->subject = new TagRenderer($this->pageRenderer, $this->entryLookupCollection);
    }

    /**
     * @test
     */
    public function renderWebpackScriptTagsWithDefaultBuild()
    {
        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $entrypointLookup->method('getJavaScriptFiles')->with('app')->willReturn(['file.js']);
        $this->entryLookupCollection->expects($this->once())->method('getEntrypointLookup')->with('_default')->willReturn($entrypointLookup);
        $this->pageRenderer->expects($this->once())->method('addJsFile');

        $this->subject->renderWebpackScriptTags('app', 'header');
    }

    /**
     * @test
     */
    public function renderWebpackScriptTagsWithDefaultBuildInFooter()
    {
        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $entrypointLookup->method('getJavaScriptFiles')->with('app')->willReturn(['file.js']);
        $this->entryLookupCollection->expects($this->once())->method('getEntrypointLookup')->with('_default')->willReturn($entrypointLookup);
        $this->pageRenderer->expects($this->once())->method('addJsFooterFile');

        $this->subject->renderWebpackScriptTags('app');
    }

    /**
     * @test
     */
    public function renderWebpackLinkTagsWithDefaultBuild()
    {
        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $entrypointLookup->method('getCssFiles')->with('app')->willReturn(['file.css']);
        $this->entryLookupCollection->expects($this->once())->method('getEntrypointLookup')->with('_default')->willReturn($entrypointLookup);
        $this->pageRenderer->expects($this->once())->method('addCssFile');

        $this->subject->renderWebpackLinkTags('app');
    }
}
