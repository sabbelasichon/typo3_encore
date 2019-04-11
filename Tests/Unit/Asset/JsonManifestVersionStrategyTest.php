<?php

namespace Ssch\Typo3Encore\Tests\Unit\Asset;

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
use Ssch\Typo3Encore\Asset\JsonManifestVersionStrategy;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\JsonDecoderInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class JsonManifestVersionStrategyTest extends UnitTestCase
{
    /**
     * @var JsonManifestVersionStrategy
     */
    private $subject;

    /**
     * @var FilesystemInterface|MockObject
     */
    private $filesystem;

    /**
     * @var JsonDecoderInterface|MockObject
     */
    private $jsonDecoder;

    /**
     * @var MockObject|SettingsServiceInterface
     */
    private $settingsService;

    /**
     * @var string
     */
    private $manifestJsonFilePath;
    /**
     * @var string
     */
    private $manifestJsonFile;

    protected function setUp()
    {
        $this->filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $this->jsonDecoder = $this->getMockBuilder(JsonDecoderInterface::class)->getMock();
        $this->settingsService = $this->getMockBuilder(SettingsServiceInterface::class)->getMock();
        $this->manifestJsonFile = 'manifest.json';
        $this->manifestJsonFilePath = GeneralUtility::getFileAbsFileName($this->manifestJsonFile);
        $this->settingsService->method('getByPath')->with('manifestJsonPath')->willReturn($this->manifestJsonFile);
        $this->subject = new JsonManifestVersionStrategy($this->settingsService, $this->filesystem, $this->jsonDecoder);
    }

    /**
     * @test
     */
    public function assetManifestFileDoesNotExistThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->filesystem->expects($this->once())->method('exists')->willReturn(false);
        $this->subject->getVersion('pathToFile');
    }

    /**
     * @test
     */
    public function nonExistingPathInManifestReturnsPathItself(): void
    {
        $this->filesystem->expects($this->once())->method('exists')->willReturn(true);
        $this->filesystem->expects($this->once())->method('get')->with($this->manifestJsonFilePath)->willReturn('contentoffile');
        $this->jsonDecoder->expects($this->once())->method('decode')->with('contentoffile')->willReturn(['file1' => 'pathtofile1.jpg']);
        $this->assertEquals('pathToFile', $this->subject->getVersion('pathToFile'));
    }

    /**
     * @test
     */
    public function existingPathInManifestReturnsThePathDefinedInManifest(): void
    {
        $this->filesystem->expects($this->once())->method('exists')->willReturn(true);
        $this->filesystem->expects($this->once())->method('get')->with($this->manifestJsonFilePath)->willReturn('contentoffile');
        $this->jsonDecoder->expects($this->once())->method('decode')->with('contentoffile')->willReturn(['file1' => 'pathtofile1.jpg']);
        $this->assertEquals('pathtofile1.jpg', $this->subject->getVersion('file1'));
    }
}
