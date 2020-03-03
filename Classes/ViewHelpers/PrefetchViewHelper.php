<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\ViewHelpers;

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

use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class PrefetchViewHelper extends AbstractViewHelper
{
    /**
     * @var AssetRegistryInterface
     */
    private $assetRegistry;

    public function __construct(AssetRegistryInterface $assetRegistry)
    {
        $this->assetRegistry = $assetRegistry;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('uri', 'string', 'The uri to prefetch', true);
        $this->registerArgument('as', 'string', 'The type like style or script', true);
        $this->registerArgument('attributes', 'array', 'The attributes of this link (e.g. "[\'as\' => true]", "[\'pr\' => 0.5]")', false, []);
    }

    public function render(): void
    {
        $attributes = $this->arguments['attributes'] ?? [];
        $this->assetRegistry->registerFile($this->arguments['uri'], $this->arguments['as'], $attributes, 'prefetch');
    }
}
