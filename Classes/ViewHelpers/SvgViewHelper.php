<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ViewHelpers;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\IdGeneratorInterface;
use TYPO3\CMS\Core\Resource\Exception\FolderDoesNotExistException;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use UnexpectedValueException;

/**
 * @final
 */
class SvgViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'svg';

    private IdGeneratorInterface $idGenerator;

    private FilesystemInterface $filesystem;

    private ImageService $imageService;

    public function __construct(
        FilesystemInterface $filesystem,
        IdGeneratorInterface $idGenerator,
        ImageService $imageService
    ) {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->idGenerator = $idGenerator;
        $this->imageService = $imageService;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerTagAttribute('class', 'string', 'CSS class(es) for this element');
        $this->registerTagAttribute('id', 'string', 'Unique (in this file) identifier for this HTML element.');
        $this->registerTagAttribute(
            'lang',
            'string',
            'Language for this element. Use short names specified in RFC 1766'
        );
        $this->registerTagAttribute('style', 'string', 'Individual CSS styles for this element');
        $this->registerTagAttribute('accesskey', 'string', 'Keyboard shortcut to access this element');
        $this->registerTagAttribute('tabindex', 'integer', 'Specifies the tab order of this element');
        $this->registerTagAttribute('onclick', 'string', 'JavaScript evaluated for the onclick event');

        $this->registerArgument('title', 'string', 'Title', false);
        $this->registerArgument('description', 'string', 'Description', false);
        $this->registerArgument('src', 'string', 'Path to the svg file', true);
        $this->registerArgument('role', 'string', 'Role', false, 'img');
        $this->registerArgument('name', 'string', 'The icon name of the sprite', true);
        $this->registerArgument('inline', 'string', 'Inline icon instead of referencing it', false, false);
        $this->registerArgument('width', 'string', 'Width of the image.');
        $this->registerArgument('height', 'string', 'Height of the image.');
        $this->registerArgument('absolute', 'bool', 'Force absolute URL', false, false);
    }

    public function render(): string
    {
        try {
            $image = $this->imageService->getImage($this->arguments['src'], null, false);
            $imageUri = $this->imageService->getImageUri($image, (bool) $this->arguments['absolute']);
            $imageContents = $image->getContents();
        } catch (FolderDoesNotExistException $folderDoesNotExistException) {
            $imageUri = $this->arguments['src'];
            $imageContents = $this->filesystem->get($imageUri);
        }

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
                htmlspecialchars((string) $this->arguments['title'], ENT_QUOTES | ENT_HTML5)
            );
        }

        if ($this->arguments['description']) {
            $descriptionId = sprintf('description-%s', $uniqueId);
            $ariaLabelledBy[] = $descriptionId;
            $content[] = sprintf(
                '<desc id="%s">%s</desc>',
                $descriptionId,
                htmlspecialchars((string) $this->arguments['description'], ENT_QUOTES | ENT_HTML5)
            );
        }

        if (count($ariaLabelledBy) > 0) {
            $this->tag->addAttribute('aria-labelledby', implode(' ', $ariaLabelledBy));
        }

        $name = (string) $this->arguments['name'];
        if ($this->arguments['inline']) {
            $doc = new DOMDocument();
            $doc->loadXML($imageContents);
            $xpath = new DOMXPath($doc);
            $iconNodeList = $xpath->query("//*[@id='{$name}']");

            if (! $iconNodeList instanceof DOMNodeList) {
                throw new UnexpectedValueException('Could not query for iconNodeList');
            }

            $icon = $iconNodeList
                ->item(0);

            if (null !== $icon) {
                if ($icon instanceof DOMElement && $icon->hasAttribute('viewBox')) {
                    $this->tag->addAttribute('viewBox', $icon->getAttribute('viewBox'));
                }
                foreach ($icon->childNodes as $node) {
                    if (null === $node->ownerDocument) {
                        continue;
                    }
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
