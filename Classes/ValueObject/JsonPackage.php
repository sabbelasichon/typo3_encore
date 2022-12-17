<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ValueObject;

use Symfony\Component\Asset\PackageInterface;

final class JsonPackage
{
    private string $manifestJsonPath;

    private PackageInterface $package;

    public function __construct(string $manifestJsonPath, PackageInterface $package)
    {
        $this->manifestJsonPath = $manifestJsonPath;
        $this->package = $package;
    }

    public function getManifestJsonPath(): string
    {
        return $this->manifestJsonPath;
    }

    public function getUrl(string $path): string
    {
        return $this->package->getUrl($path);
    }
}
