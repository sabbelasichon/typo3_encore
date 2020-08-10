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

use Ssch\Typo3Encore\Integration\AssetRegistry;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Integration\AssetRegistry
 */
final class AssetRegistryTest extends UnitTestCase
{
    /**
     * @var AssetRegistryInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        $settingsService = $this->getMockBuilder(SettingsServiceInterface::class)->getMock();
        $settingsService->method('getStringByPath')->with('preload.crossorigin')->willReturn('anonymus');
        $this->subject = new AssetRegistry($settingsService);
    }

    /**
     * @test
     */
    public function registerFilesSuccessFully(): void
    {
        $this->subject->registerFile('file1.css', 'style');
        $this->subject->registerFile('file2.css', 'style');
        $this->subject->registerFile('file.js', 'script');

        $registeredFiles = $this->subject->getRegisteredFiles();
        $this->assertCount(2, $registeredFiles['preload']['files']['style']);
        $this->assertCount(1, $registeredFiles['preload']['files']['script']);

        $this->assertSame(['crossorigin' => 'anonymus'], $this->subject->getDefaultAttributes());
    }
}
