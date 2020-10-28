<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\ViewHelpers;

/**
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use DOMDocument;
use DOMElement;
use DOMXPath;
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
     * @var ImageService
     */
    private $imageService;

    /**
     * @var IdGeneratorInterface
     */
    private $idGenerator;

    public function injectImageService(ImageService $imageService): void
    {
        $this->imageService = $imageService;
    }

    public function injectIdGenerator(IdGeneratorInterface $idGenerator): void
    {
        $this->idGenerator = $idGenerator;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('src', 'string', 'Path to the svg file', true);
        $this->registerArgument('role', 'string', 'Role', false, 'img');
        $this->registerArgument('name', 'string', 'The icon name of the sprite', true);
        $this->registerArgument('inline', 'string', 'Inline icon instead of referencing it', false, false);
        $this->registerTagAttribute('description', 'string', 'Description text of element');
        $this->registerArgument('width', 'string', 'Width of the image.');
        $this->registerArgument('height', 'string', 'Height of the image.');
        $this->registerArgument('absolute', 'bool', 'Force absolute URL', false, false);
    }

    public function render(): string
    {
        $image = $this->imageService->getImage($this->arguments['src'], null, false);
        $imageUri = $this->imageService->getImageUri($image, (bool)$this->arguments['absolute']);

        $content = [];
        $uniqueId = 'unique';
        $ariaLabelledBy = [];

        if ($this->arguments['title'] || $this->arguments['description']) {
            $uniqueId = $this->idGenerator->generate();
        }

        if ($this->arguments['title']) {
            $titleId = sprintf('title-%s', $uniqueId);
            $ariaLabelledBy[] = $titleId;
            $content[] = sprintf(
                '<title id="%s">%s</title>',
                $titleId,
                htmlspecialchars((string)$this->arguments['title'], ENT_QUOTES | ENT_HTML5)
            );
        }

        if ($this->arguments['description']) {
            $descriptionId = sprintf('description-%s', $uniqueId);
            $ariaLabelledBy[] = $descriptionId;
            $content[] = sprintf(
                '<desc id="%s">%s</desc>',
                $descriptionId,
                htmlspecialchars((string)$this->arguments['description'], ENT_QUOTES | ENT_HTML5)
            );
        }

        if (!empty($ariaLabelledBy)) {
            $this->tag->addAttribute('aria-labelledby', implode(' ', $ariaLabelledBy));
        }

        $name = (string)$this->arguments['name'];
        if ((bool)$this->arguments['inline']) {
            $doc = new DOMDocument();
            $doc->loadXML($image->getContents());
            $xpath = new DOMXPath($doc);
            if (($icon = $xpath->query("//*[@id='{$name}']")->item(0)) !== null) {
                if ($icon instanceof DOMElement && $icon->hasAttribute('viewBox')) {
                    $this->tag->addAttribute('viewBox', $icon->getAttribute('viewBox'));
                }
                foreach ($icon->childNodes as $node) {
                    $content[] = $node->ownerDocument->saveXML($node);
                }
            }
        } else {
            $content[] = sprintf(
                '<use xlink:href="%s#%s" />',
                $imageUri,
                htmlspecialchars($name, ENT_QUOTES | ENT_HTML5)
            );
        }

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
