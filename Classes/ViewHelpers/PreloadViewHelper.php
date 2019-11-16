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

final class PreloadViewHelper extends AbstractViewHelper
{
    /**
     * @var AssetRegistryInterface
     */
    private $assetRegistry;

    public function __construct(AssetRegistryInterface $assetRegistry)
    {
        $this->assetRegistry = $assetRegistry;
    }

    public function initializeArguments()
    {
        $this->registerArgument('uri', 'string', 'The uri to preload', true);
        $this->registerArgument('as', 'string', 'The type like style or script', true);
        $this->registerArgument('attributes', 'array', 'Additional attributes like importance', false, []);
    }

    public function render()
    {
        $attributes = $this->arguments['attributes'] ?? [];
        $this->assetRegistry->registerFile($this->arguments['uri'], $this->arguments['as'], $attributes);
    }
}
