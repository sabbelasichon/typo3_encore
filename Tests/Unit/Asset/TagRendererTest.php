<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Asset;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\IntegrityDataProviderInterface;
use Ssch\Typo3Encore\Asset\TagRenderer;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\ValueObject\ScriptTag;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Asset\TagRenderer
 */
final class TagRendererTest extends UnitTestCase
{
    use ProphecyTrait;

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
    protected $assetRegistry;

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
        $this->entryLookupCollection->getEntrypointLookup(EntrypointLookupInterface::DEFAULT_BUILD)->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClass());

        $this->addJsFileShouldBeCalledOnce();

        $this->assetRegistry->registerFile(Argument::any(), Argument::any(), Argument::any(), Argument::any())->shouldBeCalledOnce();

        $scriptTag = new ScriptTag('app', 'header', EntrypointLookupInterface::DEFAULT_BUILD, $this->pageRenderer->reveal());
        $this->subject->renderWebpackScriptTags($scriptTag);
    }

    /**
     * @test
     */
    public function renderWebpackScriptTagsWithDefaultBuildWithoutAssetRegistration(): void
    {
        $this->entryLookupCollection->getEntrypointLookup(EntrypointLookupInterface::DEFAULT_BUILD)->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClass());

        $this->addJsFileShouldBeCalledOnce();

        $this->assetRegistry->registerFile(Argument::any(), Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();
        $scriptTag = new ScriptTag('app', 'header', EntrypointLookupInterface::DEFAULT_BUILD, $this->pageRenderer->reveal(), [], false);
        $this->subject->renderWebpackScriptTags($scriptTag);
    }

    /**
     * @test
     * @dataProvider scriptTagsWithPosition
     */
    public function renderWebpackScriptTagsWithDefaultBuildInPosition(string $position, $isLibrary, string $expectedPageRendererCall): void
    {
        $this->entryLookupCollection->getEntrypointLookup(EntrypointLookupInterface::DEFAULT_BUILD)->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClass());

        $arguments = [
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
        ];

        if ($isLibrary) {
            array_unshift($arguments, 'file.js');
        }

        $this->pageRenderer->{$expectedPageRendererCall}(...$arguments)->shouldBeCalledOnce();

        $this->assetRegistry->registerFile(Argument::any(), Argument::any(), Argument::any(), Argument::any())->shouldBeCalledOnce();
        $scriptTag = new ScriptTag('app', $position, EntrypointLookupInterface::DEFAULT_BUILD, $this->pageRenderer->reveal(), ['compress' => true, 'excludeFromConcatenation' => false], true, $isLibrary);
        $this->subject->renderWebpackScriptTags($scriptTag);
    }

    public function scriptTagsWithPosition(): array
    {
        return [
            // $position, $isLibrary, $expectedPageRendererCall
            ['footer', false, 'addJsFooterFile'],
            ['footer', true, 'addJsFooterLibrary'],
            ['', false, 'addJsFile'],
            ['', true, 'addJsLibrary'],
        ];
    }

    /**
     * @test
     */
    public function renderWebpackLinkTagsWithDefaultBuild(): void
    {
        $this->entryLookupCollection->getEntrypointLookup(EntrypointLookupInterface::DEFAULT_BUILD)->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClass());

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
        $this->subject->renderWebpackLinkTags('app', 'all', EntrypointLookupInterface::DEFAULT_BUILD, $this->pageRenderer->reveal(), ['forceOnTop' => true, 'compress' => true]);
    }

    /**
     * @test
     */
    public function renderWebpackLinkTagsWithDefaultBuildWithoutAssetRegistration(): void
    {
        $this->entryLookupCollection->getEntrypointLookup(EntrypointLookupInterface::DEFAULT_BUILD)->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClass());

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
        $this->subject->renderWebpackLinkTags('app', 'all', EntrypointLookupInterface::DEFAULT_BUILD, $this->pageRenderer->reveal(), ['forceOnTop' => true, 'compress' => true], false);
    }

    /**
     * Test if an entry point with multiple files wraps all files, if allWrap was supplied
     * @test
     */
    public function renderWebpackScriptTagsWithMultipleFilesAndAllWrap(): void
    {
        $this->entryLookupCollection->getEntrypointLookup(EntrypointLookupInterface::DEFAULT_BUILD)->shouldBeCalledOnce()->willReturn($this->createEntrypointLookUpClassWithMultipleEntries());

        $this->pageRenderer->addJsFile(
            'file1.js',
            Argument::any(),
            Argument::any(),
            Argument::any(),
            'BEFORE|',
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldBeCalled();

        $this->pageRenderer->addJsFile(
            'file2.js',
            Argument::any(),
            Argument::any(),
            Argument::any(),
            '|AFTER',
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldBeCalled();

        $scriptTag = new ScriptTag('app', 'header', EntrypointLookupInterface::DEFAULT_BUILD, $this->pageRenderer->reveal(), ['compress' => true, 'excludeFromConcatenation' => false, 'allWrap' => 'BEFORE|AFTER']);
        $this->subject->renderWebpackScriptTags($scriptTag);
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
        return new class() implements EntrypointLookupInterface, IntegrityDataProviderInterface {
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

    private function createEntrypointLookUpClassWithMultipleEntries(): EntrypointLookupInterface
    {
        return new class() implements EntrypointLookupInterface, IntegrityDataProviderInterface {
            public function getJavaScriptFiles(string $entryName): array
            {
                return ['file1.js', 'file2.js'];
            }

            public function getCssFiles(string $entryName): array
            {
                return ['file1.css', 'file2.css'];
            }

            public function reset()
            {
            }

            public function getIntegrityData(): array
            {
                return [
                    'file1.js' => 'foobarbaz1',
                    'file2.js' => 'foobarbaz2',
                ];
            }
        };
    }
}
