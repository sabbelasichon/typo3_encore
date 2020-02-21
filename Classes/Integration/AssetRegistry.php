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

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class AssetRegistry implements AssetRegistryInterface
{
    /**
     * @var array
     */
    private $registeredFiles = [];

    /**
     * @var array
     */
    private $defaultAttributes = [];

    public function __construct(SettingsServiceInterface $settingsService)
    {
        $this->defaultAttributes['crossorigin'] = $settingsService->getStringByPath('preload.crossorigin');
        $this->reset();
    }

    public function registerFile(string $file, string $type, array $attributes = [], string $rel = 'preload'): void
    {
        if (!isset($this->registeredFiles[$rel])) {
            $this->registeredFiles[$rel] = [];
        }

        if (!isset($this->registeredFiles[$rel]['files'][$type])) {
            $this->registeredFiles[$rel]['files'][$type] = [];
        }

        $file = GeneralUtility::createVersionNumberedFilename($file);
        $this->registeredFiles[$rel]['files'][$type][$file] = $attributes;
    }

    public function getRegisteredFiles(): array
    {
        return $this->registeredFiles;
    }

    public function getDefaultAttributes(): array
    {
        return $this->defaultAttributes;
    }

    private function reset(): void
    {
        $this->registeredFiles = [];
    }
}
