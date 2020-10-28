<?php

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase

use PHPUnit\Framework\MockObject\MockObject;
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
        $this->setArgumentsUnderTest($this->viewHelper, ['pathToFile' => $pathToFile, 'package' => '_default']);

        $this->filesystem->expects($this->once())->method('getRelativeFilePath')->willReturn($pathToFile);
        $this->package->expects($this->once())->method('getUrl')->willReturn($pathToFile);
        $this->assertEquals($pathToFile, $this->viewHelper->initializeArgumentsAndRender());
    }
}
