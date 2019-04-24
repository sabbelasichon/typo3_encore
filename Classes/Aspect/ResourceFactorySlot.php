<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Aspect;

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

use Ssch\Typo3Encore\Asset\JsonManifestVersionStrategy;
use TYPO3\CMS\Core\Resource\ResourceFactory;

final class ResourceFactorySlot
{
    /**
     * @var JsonManifestVersionStrategy
     */
    private $jsonManifestVersionStrategy;

    /**
     * ResourceFactorySlot constructor.
     *
     * @param JsonManifestVersionStrategy $JsonManifestVersionStrategy
     */
    public function __construct(JsonManifestVersionStrategy $JsonManifestVersionStrategy)
    {
        $this->jsonManifestVersionStrategy = $JsonManifestVersionStrategy;
    }

    /**
     * @param ResourceFactory $resourceFactory
     * @param string|null $fileIdentifier
     * @param string|null $slotName
     *
     * @return array
     */
    public function jsonManifestVersionStrategy(ResourceFactory $resourceFactory, string $fileIdentifier = null, string $slotName = null): array
    {
        return [$resourceFactory, $this->jsonManifestVersionStrategy->getVersion($fileIdentifier)];
    }
}
