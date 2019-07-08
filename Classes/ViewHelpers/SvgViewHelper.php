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

use Ssch\Typo3Encore\Integration\IdGeneratorInterface;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * @final
 */
class SvgViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'svg';

    /**
     * @var bool
     */
    protected $forceClosingTag = true;

    /**
     * @var ImageService
     */
    protected $imageService;

    /**
     * @var IdGeneratorInterface
     */
    private $idGenerator;

    public function injectImageService(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function injectIdGenerator(IdGeneratorInterface $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('src', 'string', 'Path to the svg file', true);
        $this->registerArgument('role', 'string', 'Role', false, 'img');
        $this->registerArgument('name', 'string', 'The icon name of the sprite', true);
        $this->registerTagAttribute('description', 'string', 'Description text of element');
        $this->registerArgument('width', 'string', 'Width of the image.');
        $this->registerArgument('height', 'string', 'Height of the image.');
        $this->registerArgument('absolute', 'bool', 'Force absolute URL', false, false);
    }

    public function render(): string
    {
        $image = $this->imageService->getImage($this->arguments['src'], null, null);
        $imageUri = $this->imageService->getImageUri($image, $this->arguments['absolute']);

        $content = [];
        $uniqueId = 'unique';
        $ariaLabelledBy = [];

        if ($this->arguments['title'] || $this->arguments['description']) {
            $uniqueId = $this->idGenerator->generate();
        }

        if ($this->arguments['title']) {
            $titleId = sprintf('title-%s', $uniqueId);
            $ariaLabelledBy[] = $titleId;
            $content[] = sprintf('<title id="%s">%s</title>', $titleId, htmlspecialchars($this->arguments['title']));
        }

        if ($this->arguments['description']) {
            $descriptionId = sprintf('description-%s', $uniqueId);
            $ariaLabelledBy[] = $descriptionId;
            $content[] = sprintf('<desc id="%s">%s</desc>', $descriptionId, htmlspecialchars($this->arguments['description']));
        }

        if (! empty($ariaLabelledBy)) {
            $this->tag->addAttribute('aria-labelledby', implode(' ', $ariaLabelledBy));
        }

        $content[] = sprintf('<use xlink:href="%s#%s" />', $imageUri, htmlspecialchars($this->arguments['name']));

        $this->tag->setContent(implode('', $content));

        if ($this->arguments['width']) {
            $this->tag->addAttribute('width', $this->arguments['width']);
        }

        if ($this->arguments['height']) {
            $this->tag->addAttribute('height', $this->arguments['height']);
        }

        $this->tag->addAttribute('xmlns', 'http://www.w3.org/2000/svg');
        $this->tag->addAttribute('focusable', 'false');

        if ($this->arguments['role']) {
            $this->tag->addAttribute('role', $this->arguments['role']);
        }

        return $this->tag->render();
    }
}
