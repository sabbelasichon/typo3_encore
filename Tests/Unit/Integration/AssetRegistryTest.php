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

use PHPUnit\Framework\TestCase;
use Ssch\Typo3Encore\Integration\AssetRegistry;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;

class AssetRegistryTest extends TestCase
{
    /**
     * @var AssetRegistryInterface
     */
    protected $subject;

    protected function setUp()
    {
        $this->subject = new AssetRegistry();
    }

    /**
     * @test
     */
    public function registerFilesSuccessFully()
    {
        $this->subject->registerFile('file1.css', 'style');
        $this->subject->registerFile('file2.css', 'style');
        $this->subject->registerFile('file.js', 'script');

        $registeredFiles = $this->subject->getRegisteredFiles();
        $this->assertCount(2, $registeredFiles['style']);
        $this->assertCount(1, $registeredFiles['script']);
    }
}
