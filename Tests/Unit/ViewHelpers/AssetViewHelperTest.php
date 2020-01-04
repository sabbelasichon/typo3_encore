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
use Ssch\Typo3Encore\Asset\VersionStrategyInterface;
use Ssch\Typo3Encore\Integration\PackageFactoryInterface;
use Ssch\Typo3Encore\ViewHelpers\AssetViewHelper;
use Symfony\Component\Asset\PackageInterface;

class AssetViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var AssetViewHelper
     */
    protected $viewHelper;

    protected $package;

    protected function setUp()
    {
        parent::setUp();
        $this->package = $this->getMockBuilder(PackageInterface::class)->getMock();

        $packageFactory = $this->getMockBuilder(PackageFactoryInterface::class)->getMock();
        $packageFactory->method('getPackage')->willReturn($this->package);
        $this->viewHelper = new AssetViewHelper($packageFactory);
    }

    /**
     * @test
     */
    public function returnResolvedPathForFile()
    {
        $pathToFile = 'EXT:typo3_encore/Tests/Build/UnitTests.xml';
        $this->viewHelper->setArguments(['pathToFile' => $pathToFile]);

        $this->package->expects($this->once())->method('getUrl')->willReturn($pathToFile);
        $this->assertEquals($pathToFile, $this->viewHelper->render());
    }
}
