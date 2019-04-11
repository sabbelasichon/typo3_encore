<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Integration;

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

use Ssch\Typo3Encore\Asset\TagRenderer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

final class IncludeWebPackFiles implements SingletonInterface
{

    /**
     * @var TagRenderer
     */
    private $tagRenderer;

    /**
     * IncludeWebPackFiles constructor.
     *
     * @param object|TagRenderer|null $tagRenderer
     */
    public function __construct(TagRenderer $tagRenderer = null)
    {
        if (! $tagRenderer instanceof TagRenderer) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $tagRenderer = $objectManager->get(TagRenderer::class);
        }
        $this->tagRenderer = $tagRenderer;
    }

    public function addWebpackScriptTags(string $content, array $conf): void
    {
        if (! isset($conf['entryName'])) {
            throw new \InvalidArgumentException('Please provide an entryName.');
        }

        $position = $conf['position'] ?: 'footer';

        $this->tagRenderer->renderWebpackScriptTags($conf['entryName'], $position);
    }

    public function addWebpackLinkTags(string $content, array $conf): void
    {
        if (! isset($conf['entryName'])) {
            throw new \InvalidArgumentException('Please provide an entryName.');
        }

        $media = $conf['media'] ?: 'all';

        $this->tagRenderer->renderWebpackLinkTags($conf['entryName'], $media);
    }
}
