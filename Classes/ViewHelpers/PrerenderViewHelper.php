<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers;

use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\ValueObject\File;
use Ssch\Typo3Encore\ValueObject\FileType;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class PrerenderViewHelper extends AbstractViewHelper
{
    private AssetRegistryInterface $assetRegistry;

    public function __construct(AssetRegistryInterface $assetRegistry)
    {
        $this->assetRegistry = $assetRegistry;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('uri', 'string', 'The uri to prerender', true);
        $this->registerArgument('as', 'string', 'The type like style or script', true);
        $this->registerArgument(
            'attributes',
            'array',
            'The attributes of this link (e.g. "[\'as\' => true]", "[\'pr\' => 0.5]")',
            false,
            []
        );
    }

    public function render(): void
    {
        $attributes = $this->arguments['attributes'] ?? [];
        $file = new File($this->arguments['uri'], FileType::createFromString(
            $this->arguments['as']
        ), $attributes, 'prerender');
        $this->assetRegistry->registerFile($file);
    }
}
