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
use Ssch\Typo3Encore\ValueObject\JsonPackage;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

final class PackageFactory implements PackageFactoryInterface
{
    public function __construct(
        private readonly SettingsServiceInterface $settingsService,
        private readonly FilesystemInterface $filesystem
    ) {
    }

    public function getPackage(string $package): JsonPackage
    {
        $manifestJsonPath = $package === EntrypointLookupInterface::DEFAULT_BUILD ? 'manifestJsonPath' : sprintf(
            'packages.%s.manifestJsonPath',
            $package
        );
        $absoluteManifestJsonPath = $this->filesystem->getFileAbsFileName(
            $this->settingsService->getStringByPath($manifestJsonPath)
        );

        return new JsonPackage($absoluteManifestJsonPath, new Package(new JsonManifestVersionStrategy(
            $absoluteManifestJsonPath
        )));
    }
}
