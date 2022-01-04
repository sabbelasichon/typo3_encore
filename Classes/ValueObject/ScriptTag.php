<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ValueObject;

use TYPO3\CMS\Core\Page\PageRenderer;

final class ScriptTag
{
    private string $entryName;

    private string $position;

    private string $buildName;

    private ?PageRenderer $pageRenderer;

    private array $parameters;

    private bool $registerFile;

    private bool $isLibrary;

    public function __construct(
        string $entryName,
        string $position,
        string $buildName,
        PageRenderer $pageRenderer = null,
        array $parameters = [],
        bool $registerFile = true,
        bool $isLibrary = false
    ) {
        $this->entryName = $entryName;
        $this->position = $position;
        $this->buildName = $buildName;
        $this->pageRenderer = $pageRenderer;
        $this->parameters = $parameters;
        $this->registerFile = $registerFile;
        $this->isLibrary = $isLibrary;
    }

    public function getEntryName(): string
    {
        return $this->entryName;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getBuildName(): string
    {
        return $this->buildName;
    }

    public function getPageRenderer(): ?PageRenderer
    {
        return $this->pageRenderer;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function isRegisterFile(): bool
    {
        return $this->registerFile;
    }

    public function isLibrary(): bool
    {
        return $this->isLibrary;
    }
}
