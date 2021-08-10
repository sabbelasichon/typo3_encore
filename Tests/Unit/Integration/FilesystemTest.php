<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

use Ssch\Typo3Encore\Integration\Filesystem;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use UnexpectedValueException;

/**
 * @covers \Ssch\Typo3Encore\Integration\Filesystem
 */
final class FilesystemTest extends UnitTestCase
{
    protected Filesystem $subject;

    protected string $fixtureFile;

    protected function setUp(): void
    {
        $this->subject = new Filesystem();
        $this->fixtureFile = __DIR__ . '/../Fixtures/testfile.txt';
    }

    /**
     * @test
     */
    public function canNotReadFileContentThrowsException(): void
    {
        $pathToFile = 'thisisnotafile';

        $this->expectException(UnexpectedValueException::class);
        $this->subject->get($pathToFile);
    }

    /**
     * @test
     */
    public function canReadFileContent(): void
    {
        self::assertStringEqualsFile($this->fixtureFile, $this->subject->get($this->fixtureFile));
    }

    /**
     * @test
     */
    public function fileDoesNotExistsReturnsFalse(): void
    {
        self::assertFalse($this->subject->exists('doesnotexistsfile.txt'));
    }

    /**
     * @test
     */
    public function fileExistsReturnsTrue(): void
    {
        self::assertTrue($this->subject->exists($this->fixtureFile));
    }
}
