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
use Ssch\Typo3Encore\ViewHelpers\AssetViewHelper;

class AssetViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var AssetViewHelper
     */
    protected $viewHelper;

    /**
     * @var VersionStrategyInterface
     */
    protected $versionStrategy;

    protected function setUp()
    {
        parent::setUp();
        $this->versionStrategy = $this->getMockBuilder(VersionStrategyInterface::class)->getMock();
        $this->viewHelper = new AssetViewHelper($this->versionStrategy);
    }

    /**
     * @test
     */
    public function returnResolvedPathForFile()
    {
        $pathToFile = 'EXT:typo3_encore/Tests/Build/UnitTests.xml';
        $this->viewHelper->setArguments(['pathToFile' => $pathToFile]);

        $this->versionStrategy->expects($this->once())->method('applyVersion')->willReturn($pathToFile);
        $this->assertEquals($pathToFile, $this->viewHelper->render());
    }
}
