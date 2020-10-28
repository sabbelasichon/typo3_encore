<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase

use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Integration\SettingsService;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Integration\SettingsService
 */
final class SettingsServiceTest extends UnitTestCase
{
    /**
     * @var SettingsService
     */
    protected $subject;

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
        $this->configurationManager->expects($this->once())
                                   ->method('getConfiguration')
                                   ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'Typo3Encore')
                                   ->willReturn($settings);

        $this->assertEquals($settings['entrypointJsonPath'], $this->subject->getStringByPath('entrypointJsonPath'));
    }

    /**
     * @test
     */
    public function getNonExistingSettingStringByPathReturnsString(): void
    {
        $this->expectEmptySettingsAreReturned();
        $this->assertIsString($this->subject->getStringByPath('entrypointJsonPath'));
    }

    /**
     * @test
     */
    public function getNonExistingSettingArrayByPathReturnsArray(): void
    {
        $this->expectEmptySettingsAreReturned();
        $this->assertIsArray($this->subject->getArrayByPath('entrypointJsonPath'));
    }

    /**
     * @test
     */
    public function getNonExistingSettingBooleanByPathReturnsBoolean(): void
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
