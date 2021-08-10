<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Integration\SettingsService;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Integration\SettingsService
 */
final class SettingsServiceTest extends UnitTestCase
{
    protected SettingsService $subject;

    /**
     * @var ConfigurationManagerInterface|MockObject
     */
    protected $configurationManager;

    protected function setUp(): void
    {
        $this->configurationManager = $this->createMock(ConfigurationManagerInterface::class);
        $this->subject = new SettingsService($this->configurationManager);
    }

    /**
     * @test
     */
    public function getExistingSettingByPathReturnsCorrectValue(): void
    {
        $settings = [
            'entrypointJsonPath' => 'pathToFile',
            'manifestJsonPath' => 'pathToFile',
        ];
        $this->configurationManager->expects(self::once())
                                   ->method('getConfiguration')
                                   ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'Typo3Encore')
                                   ->willReturn($settings);

        self::assertEquals($settings['entrypointJsonPath'], $this->subject->getStringByPath('entrypointJsonPath'));
    }

    /**
     * @test
     */
    public function getNonExistingSettingStringByPathReturnsString(): void
    {
        $this->expectEmptySettingsAreReturned();
        self::assertIsString($this->subject->getStringByPath('entrypointJsonPath'));
    }

    /**
     * @test
     */
    public function getNonExistingSettingArrayByPathReturnsArray(): void
    {
        $this->expectEmptySettingsAreReturned();
        self::assertIsArray($this->subject->getArrayByPath('entrypointJsonPath'));
    }

    /**
     * @test
     */
    public function getNonExistingSettingBooleanByPathReturnsBoolean(): void
    {
        $this->expectEmptySettingsAreReturned();
        self::assertIsBool($this->subject->getBooleanByPath('entrypointJsonPath'));
    }

    private function expectEmptySettingsAreReturned(): void
    {
        $settings = [];

        $this->configurationManager->expects(self::once())
                                   ->method('getConfiguration')
                                   ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'Typo3Encore')
                                   ->willReturn([]);
    }
}
