<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

use Ssch\Typo3Encore\Asset\EntrypointLookup;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;

final class EntryLookupFactory implements EntryLookupFactoryInterface
{
    private SettingsServiceInterface $settingsService;

    private FilesystemInterface $filesystem;

    /**
     * @var array|EntrypointLookupInterface[]
     */
    private static ?array $collection = null;

    private JsonDecoderInterface $jsonDecoder;

    public function __construct(
        SettingsServiceInterface $settingsService,
        FilesystemInterface $filesystem,
        JsonDecoderInterface $jsonDecoder
    ) {
        $this->settingsService = $settingsService;
        $this->filesystem = $filesystem;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * @return EntrypointLookupInterface[]
     */
    public function getCollection(): array
    {
        if (null !== self::$collection) {
            return self::$collection;
        }

        $buildConfigurations = $this->settingsService->getArrayByPath('builds');
        $entrypointsPathDefaultBuild = $this->settingsService->getStringByPath('entrypointJsonPath');
        $strictMode = $this->settingsService->getBooleanByPath('strictMode');

        $builds = [];

        if (count($buildConfigurations) > 0) {
            foreach ($buildConfigurations as $buildConfigurationKey => $buildConfiguration) {
                $entrypointsPath = sprintf('%s/entrypoints.json', $buildConfiguration);
                $builds[$buildConfigurationKey] = $this->createEntrypointLookUp($entrypointsPath, $strictMode);
            }
        }

        if ($this->filesystem->exists($this->filesystem->getFileAbsFileName($entrypointsPathDefaultBuild))) {
            $builds['_default'] = $this->createEntrypointLookUp($entrypointsPathDefaultBuild, $strictMode);
        }

        self::$collection = $builds;

        return $builds;
    }

    private function createEntrypointLookUp(
        string $entrypointJsonPath,
        bool $strictMode
    ): EntrypointLookupInterface {
        return new EntrypointLookup($entrypointJsonPath, $strictMode, $this->jsonDecoder, $this->filesystem);
    }
}
