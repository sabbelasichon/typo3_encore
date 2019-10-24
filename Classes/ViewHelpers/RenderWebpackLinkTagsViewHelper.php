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

use Ssch\Typo3Encore\Asset\TagRendererInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class RenderWebpackLinkTagsViewHelper extends AbstractViewHelper
{

    /**
     * @var TagRendererInterface
     */
    private $tagRenderer;

    public function __construct(TagRendererInterface $tagRenderer)
    {
        $this->tagRenderer = $tagRenderer;
    }

    public function initializeArguments()
    {
        $this->registerArgument('entryName', 'string', 'The entry name', true);
        $this->registerArgument('media', 'string', 'Media type', false, 'all');
        $this->registerArgument('buildName', 'string', 'The build name', false, '_default');
        $this->registerArgument('parameters', 'array', 'Additional parameters for the PageRenderer', false, []);
    }

    public function render()
    {
        $this->tagRenderer->renderWebpackLinkTags($this->arguments['entryName'], $this->arguments['media'], $this->arguments['buildName'], null, $this->arguments['parameters']);
    }
}
