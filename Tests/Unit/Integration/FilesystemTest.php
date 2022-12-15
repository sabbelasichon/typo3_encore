<?php

declare(strict_types=1);

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

final class FilesystemTest extends UnitTestCase
{
    protected Filesystem $subject;

    protected string $fixtureFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Filesystem();
        $this->fixtureFile = __DIR__ . '/../Fixtures/testfile.txt';
    }

    public function testCanNotReadFileContentThrowsException(): void
    {
        $pathToFile = 'thisisnotafile';

        $this->expectException(UnexpectedValueException::class);
        $this->subject->get($pathToFile);
    }

    public function testCanReadFileContent(): void
    {
        self::assertStringEqualsFile($this->fixtureFile, $this->subject->get($this->fixtureFile));
    }

    public function testFileDoesNotExistsReturnsFalse(): void
    {
        self::assertFalse($this->subject->exists('doesnotexistsfile.txt'));
    }

    public function testFileExistsReturnsTrue(): void
    {
        self::assertTrue($this->subject->exists($this->fixtureFile));
    }
}
