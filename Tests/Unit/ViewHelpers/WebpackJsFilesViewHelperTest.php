<?php

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

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

use Nimut\TestingFramework\TestCase\ViewHelperBaseTestcase;
use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\ViewHelpers\WebpackJsFilesViewHelper;

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\WebpackJsFilesViewHelper
 */
class WebpackJsFilesViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var MockObject|WebpackJsFilesViewHelper
     */
    protected $viewHelper;
    /**
     * @var EntrypointLookupCollectionInterface|MockObject
     */
    private $entrypointLookupCollection;

    protected function setUp()
    {
        parent::setUp();
        $this->entrypointLookupCollection = $this->getMockBuilder(EntrypointLookupCollectionInterface::class)->getMock();
        $this->viewHelper = new WebpackJsFilesViewHelper($this->entrypointLookupCollection);
    }

    /**
     * @test
     */
    public function render()
    {
        $this->viewHelper->setArguments(['entryName' => 'app', 'buildName' => '_default']);
        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $this->entrypointLookupCollection->expects($this->once())->method('getEntrypointLookup')->with('_default')->willReturn($entrypointLookup);
        $entrypointLookup->expects($this->once())->method('getJavaScriptFiles')->with('app');
        $this->viewHelper->render();
    }
}
