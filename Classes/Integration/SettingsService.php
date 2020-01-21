<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Integration;

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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

final class SettingsService implements SettingsServiceInterface
{

    /**
     * @var array
     */
    private $settings;

    /**
     * @var ConfigurationManagerInterface
     */
    private $configurationManager;

    public function __construct(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    public function getSettings(): array
    {
        if ($this->settings === null) {
            $this->settings = (array)$this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                'Typo3Encore'
            );
        }

        return $this->settings;
    }

    /**
     * Returns the settings at path $path, which is separated by ".",
     * e.g. "pages.uid".
     * "pages.uid" would return $this->settings['pages']['uid'].
     *
     * If the path is invalid or no entry is found, false is returned.
     *
     * @param string $path
     *
     * @return mixed
     */
    private function getByPath(string $path)
    {
        return ObjectAccess::getPropertyPath($this->getSettings(), $path);
    }

    public function getArrayByPath(string $path): array
    {
        return (array)$this->getByPath($path);
    }

    public function getStringByPath(string $path): string
    {
        return (string)$this->getByPath($path);
    }

    public function getBooleanByPath(string $path): bool
    {
        return (bool)$this->getByPath($path);
    }
}
