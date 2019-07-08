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
use Ssch\Typo3Encore\Integration\SettingsService;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class SettingsServiceTest extends UnitTestCase
{
    /**
     * @var SettingsService
     */
    private $subject;

    /**
     * @var ConfigurationManagerInterface|MockObject
     */
    private $configurationManager;

    protected function setUp()
    {
        $this->configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMock();
        $this->subject = new SettingsService($this->configurationManager);
    }

    /**
     * @test
     */
    public function getExistingSettingByPathReturnsCorrectValue()
    {
        $settings = [
            'entrypointJsonPath' => 'pathToFile',
            'manifestJsonPath' => 'pathToFile',
        ];
        $this->configurationManager->expects($this->once())
                                   ->method('getConfiguration')
                                   ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'Typo3Encore')
                                   ->willReturn($settings);

        $this->assertEquals($settings['entrypointJsonPath'], $this->subject->getByPath('entrypointJsonPath'));
    }

    /**
     * @test
     */
    public function getNonExistingSettingByPathReturnsNull()
    {
        $settings = [];

        $this->configurationManager->expects($this->once())
                                   ->method('getConfiguration')
                                   ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'Typo3Encore')
                                   ->willReturn([]);

        $this->assertNull($this->subject->getByPath('entrypointJsonPath'));
    }
}
