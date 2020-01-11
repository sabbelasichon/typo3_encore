<?php
declare(strict_types = 1);

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

namespace Ssch\Typo3Encore\Integration;

use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

final class EntryLookupFactory implements EntryLookupFactoryInterface
{
    /**
     * @var SettingsServiceInterface
     */
    private $settingsService;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var array|EntrypointLookupInterface[]
     */
    private static $collection;

    /**
     * EntryLookupFactory constructor.
     *
     * @param SettingsServiceInterface $settingsService
     */
    public function __construct(SettingsServiceInterface $settingsService, ObjectManagerInterface $objectManager, FilesystemInterface $filesystem)
    {
        $this->settingsService = $settingsService;
        $this->objectManager = $objectManager;
        $this->filesystem = $filesystem;
    }

    /**
     * @return array|EntrypointLookupInterface[]
     */
    public function getCollection(): array
    {
        if (static::$collection !== null) {
            return static::$collection;
        }

        $buildConfigurations = $this->settingsService->getByPath('builds');
        $entrypointsPathDefaultBuild = $this->settingsService->getByPath('entrypointJsonPath');

        $builds = [];

        if (! empty($buildConfigurations)) {
            foreach ($buildConfigurations as $buildConfigurationKey => $buildConfiguration) {
                $entrypointsPath = sprintf('%s/entrypoints.json', $buildConfiguration);
                $builds[$buildConfigurationKey] = $this->objectManager->get(EntrypointLookupInterface::class, $entrypointsPath, $buildConfigurationKey);
            }
        }

        if ($this->filesystem->exists($this->filesystem->getFileAbsFileName($entrypointsPathDefaultBuild))) {
            $builds['_default'] =  $this->objectManager->get(EntrypointLookupInterface::class, $entrypointsPathDefaultBuild, '_default');
        }

        static::$collection = $builds;

        return $builds;
    }
}
