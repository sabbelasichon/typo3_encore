<?php
declare(strict_types=1);


namespace Ssch\Typo3Encore\ViewHelpers;


use Ssch\Typo3Encore\Asset\TagRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class RenderWebpackLinkTagsViewHelper extends AbstractViewHelper
{

    /**
     * @var TagRenderer
     */
    private $tagRenderer;

    /**
     * RenderWebpackScriptTagsViewHelper constructor.
     *
     * @param TagRenderer $tagRenderer
     */
    public function __construct(TagRenderer $tagRenderer)
    {
        $this->tagRenderer = $tagRenderer;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('entryName', 'string', 'The entry name', true);
    }


    public function render(): void
    {
        $this->tagRenderer->renderWebpackLinkTags($this->arguments['entryName']);
    }

}