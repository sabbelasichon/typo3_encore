<?php
declare(strict_types=1);


namespace Ssch\Typo3Encore\ViewHelpers;


use Ssch\Typo3Encore\Asset\TagRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class RenderWebpackScriptTagsViewHelper extends AbstractViewHelper
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
        $this->registerArgument('position', 'string', 'The position to render the files', false, 'footer');
    }

    public function render(): void
    {
        $this->tagRenderer->renderWebpackScriptTags($this->arguments['entryName'], $this->arguments['position']);
    }

}