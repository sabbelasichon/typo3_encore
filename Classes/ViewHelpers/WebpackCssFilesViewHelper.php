<?php
declare(strict_types=1);


namespace Ssch\Typo3Encore\ViewHelpers;


use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class WebpackCssFilesViewHelper extends AbstractViewHelper
{

    /**
     * @var EntrypointLookupInterface
     */
    private $entrypointLookup;

    /**
     * WebpackCssFiles constructor.
     *
     * @param EntrypointLookupInterface $entrypointLookup
     */
    public function __construct(EntrypointLookupInterface $entrypointLookup)
    {
        $this->entrypointLookup = $entrypointLookup;
    }


    public function initializeArguments(): void
    {
        $this->registerArgument('entryName', 'string', 'The entry name', true);
    }

    public function render(): array
    {
        return $this->entrypointLookup->getCssFiles($this->arguments['entryName']);
    }

}