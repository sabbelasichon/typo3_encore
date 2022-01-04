<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

use Ssch\Typo3Encore\Integration\AssetRegistry;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use Ssch\Typo3Encore\ValueObject\File;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class AssetRegistryTest extends UnitTestCase
{
    protected AssetRegistry $subject;

    protected function setUp(): void
    {
        $settingsService = $this->getMockBuilder(SettingsServiceInterface::class)->getMock();
        $settingsService->method('getStringByPath')
            ->with('preload.crossorigin')
            ->willReturn('anonymus');
        $this->subject = new AssetRegistry($settingsService);
    }

    public function testRegisterFilesSuccessFully(): void
    {
        $this->subject->registerFile(new File('file1.css', 'style'));
        $this->subject->registerFile(new File('file2.css', 'style'));
        $this->subject->registerFile(new File('file.js', 'script'));

        $registeredFiles = $this->subject->getRegisteredFiles();
        self::assertCount(2, $registeredFiles['preload']['files']['style']);
        self::assertCount(1, $registeredFiles['preload']['files']['script']);

        self::assertSame([
            'crossorigin' => 'anonymus',
        ], $this->subject->getDefaultAttributes());
    }
}
