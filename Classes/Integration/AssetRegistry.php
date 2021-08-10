<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

use Ssch\Typo3Encore\ValueObject\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class AssetRegistry implements AssetRegistryInterface
{
    private array $registeredFiles = [];

    private array $defaultAttributes = [];

    public function __construct(SettingsServiceInterface $settingsService)
    {
        $this->defaultAttributes['crossorigin'] = $settingsService->getStringByPath('preload.crossorigin');
        $this->reset();
    }

    public function registerFile(File $file): void
    {
        $rel = $file->getRel();
        $type = $file->getType();
        $fileName = $file->getFile();
        $attributes = $file->getAttributes();

        if (! isset($this->registeredFiles[$rel])) {
            $this->registeredFiles[$rel] = [];
        }

        if (! isset($this->registeredFiles[$rel]['files'][$type])) {
            $this->registeredFiles[$rel]['files'][$type] = [];
        }

        $file = GeneralUtility::createVersionNumberedFilename($fileName);
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
