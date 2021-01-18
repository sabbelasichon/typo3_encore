<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
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
        if (self::$collection !== null) {
            return self::$collection;
        }

        $buildConfigurations = $this->settingsService->getArrayByPath('builds');
        $entrypointsPathDefaultBuild = $this->settingsService->getStringByPath('entrypointJsonPath');
        $strictMode = $this->settingsService->getBooleanByPath('strictMode');

        $builds = [];

        if (! empty($buildConfigurations)) {
            foreach ($buildConfigurations as $buildConfigurationKey => $buildConfiguration) {
                $entrypointsPath = sprintf('%s/entrypoints.json', $buildConfiguration);
                $builds[$buildConfigurationKey] = $this->createEntrypointLookUp($entrypointsPath, $buildConfigurationKey, $strictMode);
            }
        }

        if ($this->filesystem->exists($this->filesystem->getFileAbsFileName($entrypointsPathDefaultBuild))) {
            $builds['_default'] =  $this->createEntrypointLookUp($entrypointsPathDefaultBuild, '_default', $strictMode);
        }

        self::$collection = $builds;

        return $builds;
    }

    private function createEntrypointLookUp(string $entrypointJsonPath, string $cacheKeyPrefix, bool $strictMode): EntrypointLookupInterface
    {
        return $this->objectManager->get(EntrypointLookupInterface::class, $entrypointJsonPath, $cacheKeyPrefix, $strictMode);
    }
}
