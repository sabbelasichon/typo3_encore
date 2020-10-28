<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase

use Ssch\Typo3Encore\Integration\Filesystem;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Integration\Filesystem
 */
final class FilesystemTest extends UnitTestCase
{

    /**
     * @var Filesystem
     */
    protected $subject;

    /**
     * @var string
     */
    protected $fixtureFile;

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

        $this->expectException(\UnexpectedValueException::class);
        $this->subject->get($pathToFile);
    }

    /**
     * @test
     */
    public function canReadFileContent(): void
    {
        $this->assertStringEqualsFile($this->fixtureFile, $this->subject->get($this->fixtureFile));
    }

    /**
     * @test
     */
    public function fileDoesNotExistsReturnsFalse(): void
    {
        $this->assertFalse($this->subject->exists('doesnotexistsfile.txt'));
    }

    /**
     * @test
     */
    public function fileExistsReturnsTrue(): void
    {
        $this->assertTrue($this->subject->exists($this->fixtureFile));
    }
}
