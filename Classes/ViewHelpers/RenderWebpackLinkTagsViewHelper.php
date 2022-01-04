<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers;

use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\TagRendererInterface;
use Ssch\Typo3Encore\ValueObject\LinkTag;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class RenderWebpackLinkTagsViewHelper extends AbstractViewHelper
{
    private TagRendererInterface $tagRenderer;

    public function __construct(TagRendererInterface $tagRenderer)
    {
        $this->tagRenderer = $tagRenderer;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('entryName', 'string', 'The entry name', true);
        $this->registerArgument('media', 'string', 'Media type', false, 'all');
        $this->registerArgument(
            'buildName',
            'string',
            'The build name',
            false,
            EntrypointLookupInterface::DEFAULT_BUILD
        );
        $this->registerArgument('parameters', 'array', 'Additional parameters for the PageRenderer', false, []);
        $this->registerArgument('registerFile', 'bool', 'Register file for HTTP/2 push functionality', false, true);
    }

    public function render(): void
    {
        $linkTag = new LinkTag(
            $this->arguments['entryName'],
            $this->arguments['media'],
            $this->arguments['buildName'],
            null,
            $this->arguments['parameters'],
            $this->arguments['registerFile']
        );
        $this->tagRenderer->renderWebpackLinkTags($linkTag);
    }
}
