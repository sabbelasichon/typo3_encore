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
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\IntegrityDataProviderInterface;
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
     * @var ObjectProphecy|PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var EntrypointLookupCollectionInterface|ObjectProphecy
     */
    protected $entryLookupCollection;

    /**
     * @var AssetRegistryInterface|ObjectProphecy
     */
    private $assetRegistry;

    protected function setUp(): void
    {
        $this->pageRenderer = $this->prophesize(PageRenderer::class);
        $this->entryLookupCollection = $this->prophesize(EntrypointLookupCollectionInterface::class);
        $this->assetRegistry = $this->prophesize(AssetRegistryInterface::class);
        $this->subject = new TagRenderer($this->entryLookupCollection->reveal(), $this->assetRegistry->reveal());
    }

    /**
     * @test
     */
    public function renderWebpackScriptTagsWithDefaultBuild(): void
    {
        $this->entryLookupCollection->getEntrypointLookup('_default')->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClass());

        $this->addJsFileShouldBeCalledOnce();

        $this->assetRegistry->registerFile(Argument::any(), Argument::any(), Argument::any(), Argument::any())->shouldBeCalledOnce();
        $this->subject->renderWebpackScriptTags('app', 'header', '_default', $this->pageRenderer->reveal());
    }

    /**
     * @test
     */
    public function renderWebpackScriptTagsWithDefaultBuildWithoutAssetRegistration(): void
    {
        $this->entryLookupCollection->getEntrypointLookup('_default')->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClass());

        $this->addJsFileShouldBeCalledOnce();

        $this->assetRegistry->registerFile(Argument::any(), Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->subject->renderWebpackScriptTags('app', 'header', '_default', $this->pageRenderer->reveal(), [], false);
    }

    /**
     * @test
     */
    public function renderWebpackScriptTagsWithDefaultBuildInFooter(): void
    {
        $this->entryLookupCollection->getEntrypointLookup('_default')->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClass());

        $this->pageRenderer->addJsFooterFile(
            'file.js',
            'text/javascript',
            true,
            false,
            '',
            false,
            '|',
            false,
            'foobarbaz',
            false,
            ''
        )->shouldBeCalledOnce();

        $this->assetRegistry->registerFile(Argument::any(), Argument::any(), Argument::any(), Argument::any())->shouldBeCalledOnce();
        $this->subject->renderWebpackScriptTags('app', 'footer', '_default', $this->pageRenderer->reveal(), ['compress' => true, 'excludeFromConcatenation' => false]);
    }

    /**
     * @test
     */
    public function renderWebpackLinkTagsWithDefaultBuild(): void
    {
        $this->entryLookupCollection->getEntrypointLookup('_default')->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClass());

        $this->pageRenderer->addCssFile(
            'file.css',
            'stylesheet',
            'all',
            '',
            true,
            true,
            '',
            true,
            '|',
            false
        )->shouldBeCalledOnce();

        $this->assetRegistry->registerFile(Argument::any(), Argument::any(), Argument::any(), Argument::any())->shouldBeCalledOnce();
        $this->subject->renderWebpackLinkTags('app', 'all', '_default', $this->pageRenderer->reveal(), ['forceOnTop' => true, 'compress' => true]);
    }

    /**
     * @test
     */
    public function renderWebpackLinkTagsWithDefaultBuildWithoutAssetRegistration(): void
    {
        $this->entryLookupCollection->getEntrypointLookup('_default')->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClass());

        $this->pageRenderer->addCssFile(
            'file.css',
            'stylesheet',
            'all',
            '',
            true,
            true,
            '',
            true,
            '|',
            false
        )->shouldBeCalledOnce();

        $this->assetRegistry->registerFile(Argument::any(), Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->subject->renderWebpackLinkTags('app', 'all', '_default', $this->pageRenderer->reveal(), ['forceOnTop' => true, 'compress' => true], false);
    }

    private function addJsFileShouldBeCalledOnce(): void
    {
        $this->pageRenderer->addJsFile(
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldBeCalledOnce();
    }

    private function createEntrypointLookUpClass(): EntrypointLookupInterface
    {
        return new class implements EntrypointLookupInterface, IntegrityDataProviderInterface {
            public function getJavaScriptFiles(string $entryName): array
            {
                return ['file.js'];
            }

            public function getCssFiles(string $entryName): array
            {
                return ['file.css'];
            }

            public function reset()
            {
            }

            public function getIntegrityData(): array
            {
                return [
                    'file.js' => 'foobarbaz'
                ];
            }
        };
    }
}
