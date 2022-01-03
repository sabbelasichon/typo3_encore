<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\MockObject\MockObject;
use Prophecy\PhpUnit\ProphecyTrait;
use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\ViewHelpers\WebpackJsFilesViewHelper;

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\WebpackJsFilesViewHelper
 */
final class WebpackJsFilesViewHelperTest extends ViewHelperBaseTestcase
{
    use ProphecyTrait;

    protected WebpackJsFilesViewHelper $viewHelper;

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
        $this->setArgumentsUnderTest($this->viewHelper, ['entryName' => 'app', 'buildName' => EntrypointLookupInterface::DEFAULT_BUILD]);
        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $this->entrypointLookupCollection->expects(self::once())->method('getEntrypointLookup')->with(EntrypointLookupInterface::DEFAULT_BUILD)->willReturn($entrypointLookup);
        $entrypointLookup->expects(self::once())->method('getJavaScriptFiles')->with('app');
        $this->viewHelper->initializeArgumentsAndRender();
    }
}
