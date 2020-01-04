<?php
declare(strict_types=1);


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

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

final class PackageFactory implements PackageFactoryInterface
{

    /**
     * @var SettingsServiceInterface
     */
    private $settingsService;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(SettingsServiceInterface $settingsService, FilesystemInterface $filesystem)
    {
        $this->settingsService = $settingsService;
        $this->filesystem = $filesystem;
    }

    public function getPackage(): PackageInterface
    {
        return new Package(new JsonManifestVersionStrategy($this->filesystem->getFileAbsFileName($this->settingsService->getByPath('manifestJsonPath'))));
    }
}
