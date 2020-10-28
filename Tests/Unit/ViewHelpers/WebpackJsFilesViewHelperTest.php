<?php

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase

use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\ViewHelpers\WebpackJsFilesViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\WebpackJsFilesViewHelper
 */
final class WebpackJsFilesViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var MockObject|WebpackJsFilesViewHelper
     */
    protected $viewHelper;

    /**
     * @var EntrypointLookupCollectionInterface|MockObject
     */
    protected $entrypointLookupCollection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entrypointLookupCollection = $this->getMockBuilder(EntrypointLookupCollectionInterface::class)->getMock();
        $this->viewHelper = new WebpackJsFilesViewHelper($this->entrypointLookupCollection);
    }

    /**
     * @test
     */
    public function render(): void
    {
        $this->setArgumentsUnderTest($this->viewHelper, ['entryName' => 'app', 'buildName' => '_default']);
        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $this->entrypointLookupCollection->expects($this->once())->method('getEntrypointLookup')->with('_default')->willReturn($entrypointLookup);
        $entrypointLookup->expects($this->once())->method('getJavaScriptFiles')->with('app');
        $this->viewHelper->initializeArgumentsAndRender();
    }
}
