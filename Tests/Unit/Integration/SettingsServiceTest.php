<?php

declare(strict_types=1);

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

final class SettingsServiceTest extends UnitTestCase
{
    protected SettingsService $subject;

    /**
     * @var ConfigurationManagerInterface|MockObject
     */
    protected $configurationManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurationManager = $this->createMock(ConfigurationManagerInterface::class);
        $this->subject = new SettingsService($this->configurationManager);
    }

    public function testGetExistingSettingByPathReturnsCorrectValue(): void
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
}
