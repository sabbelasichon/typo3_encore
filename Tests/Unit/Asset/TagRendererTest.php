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
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * @covers \Ssch\Typo3Encore\Asset\TagRenderer
 */
final class TagRendererTest extends UnitTestCase
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

    /**
     * @var AssetRegistryInterface|MockObject
     */
    private $assetRegistry;

    protected function setUp()
    {
        $this->pageRenderer = $this->getMockBuilder(PageRenderer::class)->getMock();
        $this->entryLookupCollection = $this->getMockBuilder(EntrypointLookupCollectionInterface::class)->getMock();
        $this->assetRegistry = $this->getMockBuilder(AssetRegistryInterface::class)->getMock();
        $this->subject = new TagRenderer($this->entryLookupCollection, $this->assetRegistry);
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

        $this->assetRegistry->expects($this->once())->method('registerFile');
        $this->subject->renderWebpackScriptTags('app', 'header', '_default', $this->pageRenderer);
    }

    /**
     * @test
     */
    public function renderWebpackScriptTagsWithDefaultBuildWithoutAssetRegistration()
    {
        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $entrypointLookup->method('getJavaScriptFiles')->with('app')->willReturn(['file.js']);
        $this->entryLookupCollection->expects($this->once())->method('getEntrypointLookup')->with('_default')->willReturn($entrypointLookup);
        $this->pageRenderer->expects($this->once())->method('addJsFile');

        $this->assetRegistry->expects($this->never())->method('registerFile');
        $this->subject->renderWebpackScriptTags('app', 'header', '_default', $this->pageRenderer, [], false);
    }

    /**
     * @test
     */
    public function renderWebpackScriptTagsWithDefaultBuildInFooter()
    {
        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $entrypointLookup->method('getJavaScriptFiles')->with('app')->willReturn(['file.js']);
        $this->entryLookupCollection->expects($this->once())->method('getEntrypointLookup')->with('_default')->willReturn($entrypointLookup);
        $this->pageRenderer->expects($this->once())->method('addJsFooterFile')->with('file.js', 'text/javascript', true, false, '', false, '|', false, '', false, '');

        $this->assetRegistry->expects($this->once())->method('registerFile');
        $this->subject->renderWebpackScriptTags('app', 'footer', '_default', $this->pageRenderer, ['compress' => true, 'excludeFromConcatenation' => false]);
    }

    /**
     * @test
     */
    public function renderWebpackLinkTagsWithDefaultBuild()
    {
        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $entrypointLookup->method('getCssFiles')->with('app')->willReturn(['file.css']);
        $this->entryLookupCollection->expects($this->once())->method('getEntrypointLookup')->with('_default')->willReturn($entrypointLookup);
        $this->pageRenderer->expects($this->once())->method('addCssFile')->with('file.css', 'stylesheet', 'all', '', true, true, '', true, '|', false);

        $this->assetRegistry->expects($this->once())->method('registerFile');
        $this->subject->renderWebpackLinkTags('app', 'all', '_default', $this->pageRenderer, ['forceOnTop' => true, 'compress' => true]);
    }

    /**
     * @test
     */
    public function renderWebpackLinkTagsWithDefaultBuildWithoutAssetRegistration()
    {
        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $entrypointLookup->method('getCssFiles')->with('app')->willReturn(['file.css']);
        $this->entryLookupCollection->expects($this->once())->method('getEntrypointLookup')->with('_default')->willReturn($entrypointLookup);
        $this->pageRenderer->expects($this->once())->method('addCssFile')->with('file.css', 'stylesheet', 'all', '', true, true, '', true, '|', false);

        $this->assetRegistry->expects($this->never())->method('registerFile');
        $this->subject->renderWebpackLinkTags('app', 'all', '_default', $this->pageRenderer, ['forceOnTop' => true, 'compress' => true], false);
    }
}
