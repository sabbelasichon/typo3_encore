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
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\PackageFactoryInterface;
use Ssch\Typo3Encore\ViewHelpers\AssetViewHelper;
use Symfony\Component\Asset\PackageInterface;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\AssetViewHelper
 */
final class AssetViewHelperTest extends ViewHelperBaseTestcase
{
    use ProphecyTrait;

    /**
     * @var AssetViewHelper
     */
    protected $viewHelper;

    /**
     * @var MockObject|PackageInterface
     */
    protected $package;

    /**
     * @var FilesystemInterface|MockObject
     */
    protected $filesystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->package = $this->getMockBuilder(PackageInterface::class)->getMock();
        $this->filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $packageFactory = $this->getMockBuilder(PackageFactoryInterface::class)->getMock();
        $packageFactory->method('getPackage')->willReturn($this->package);
        $this->viewHelper = new AssetViewHelper($packageFactory, $this->filesystem);
    }

    /**
     * @test
     */
    public function returnResolvedPathForFile(): void
    {
        $pathToFile = 'EXT:typo3_encore/Tests/Build/UnitTests.xml';
        $this->setArgumentsUnderTest($this->viewHelper, ['pathToFile' => $pathToFile, 'package' => EntrypointLookupInterface::DEFAULT_BUILD]);

        $this->filesystem->expects(self::once())->method('getRelativeFilePath')->willReturn($pathToFile);
        $this->package->expects(self::once())->method('getUrl')->willReturn($pathToFile);
        self::assertEquals($pathToFile, $this->viewHelper->initializeArgumentsAndRender());
    }
}
