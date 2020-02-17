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
use Ssch\Typo3Encore\Integration\Filesystem;

/**
 * @covers \Ssch\Typo3Encore\Integration\Filesystem
 */
final class FilesystemTest extends UnitTestCase
{

    /**
     * @var Filesystem
     */
    private $subject;

    /**
     * @var string
     */
    private $fixtureFile;

    protected function setUp()
    {
        $this->subject = new Filesystem();
        $this->fixtureFile = __DIR__ . '/../Fixtures/testfile.txt';
    }

    /**
     * @test
     */
    public function canNotReadFileContentThrowsException()
    {
        $pathToFile = 'thisisnotafile';

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessageRegExp('#' . $pathToFile . '#');
        $this->subject->get($pathToFile);
    }

    /**
     * @test
     */
    public function canReadFileContent()
    {
        $this->assertStringEqualsFile($this->fixtureFile, $this->subject->get($this->fixtureFile));
    }

    /**
     * @test
     */
    public function fileDoesNotExistsReturnsFalse()
    {
        $this->assertFalse($this->subject->exists('doesnotexistsfile.txt'));
    }

    /**
     * @test
     */
    public function fileExistsReturnsTrue()
    {
        $this->assertTrue($this->subject->exists($this->fixtureFile));
    }
}
