<?php

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

/**
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
