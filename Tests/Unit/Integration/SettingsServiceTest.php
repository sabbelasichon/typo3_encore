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

/**
 * @covers \Ssch\Typo3Encore\Integration\SettingsService
 */
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
        $this->configurationManager = $this->createMock(ConfigurationManagerInterface::class);
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

        $this->assertEquals($settings['entrypointJsonPath'], $this->subject->getStringByPath('entrypointJsonPath'));
    }

    /**
     * @test
     */
    public function getNonExistingSettingStringByPathReturnsString()
    {
        $this->expectEmptySettingsAreReturned();
        $this->assertIsString($this->subject->getStringByPath('entrypointJsonPath'));
    }

    /**
     * @test
     */
    public function getNonExistingSettingArrayByPathReturnsArray()
    {
        $this->expectEmptySettingsAreReturned();
        $this->assertIsArray($this->subject->getArrayByPath('entrypointJsonPath'));
    }

    /**
     * @test
     */
    public function getNonExistingSettingBooleanByPathReturnsBoolean()
    {
        $this->expectEmptySettingsAreReturned();
        $this->assertIsBool($this->subject->getBooleanByPath('entrypointJsonPath'));
    }

    private function expectEmptySettingsAreReturned(): void
    {
        $settings = [];

        $this->configurationManager->expects($this->once())
                                   ->method('getConfiguration')
                                   ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'Typo3Encore')
                                   ->willReturn([]);
    }
}
