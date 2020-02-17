<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

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
use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\PackageFactory;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use Symfony\Component\Asset\PackageInterface;

/**
 * @covers \Ssch\Typo3Encore\Integration\PackageFactory
 */
final class PackageFactoryTest extends UnitTestCase
{
    /**
     * @var PackageFactory
     */
    protected $subject;

    /**
     * @var MockObject|SettingsServiceInterface
     */
    private $settingsService;

    /**
     * @var FilesystemInterface|MockObject
     */
    private $filesystem;

    protected function setUp()
    {
        $this->settingsService = $this->getMockBuilder(SettingsServiceInterface::class)->getMock();
        $this->filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $this->subject = new PackageFactory($this->settingsService, $this->filesystem);
    }

    /**
     * @test
     */
    public function returnsPackageWithDefaultManifestPath()
    {
        $this->settingsService->method('getStringByPath')->with('manifestJsonPath')->willReturn('manifest.json');
        $this->filesystem->method('getFileAbsFileName')->with('manifest.json')->willReturn('manifest.json');
        $this->assertInstanceOf(PackageInterface::class, $this->subject->getPackage('_default'));
    }

    /**
     * @test
     */
    public function returnsPackageWithSpecificManifestPath()
    {
        $this->settingsService->method('getStringByPath')->with('packages.custom.manifestJsonPath')->willReturn('manifest.json');
        $this->filesystem->method('getFileAbsFileName')->with('manifest.json')->willReturn('manifest.json');
        $this->assertInstanceOf(PackageInterface::class, $this->subject->getPackage('custom'));
    }
}
