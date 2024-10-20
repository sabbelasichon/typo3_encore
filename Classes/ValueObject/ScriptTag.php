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
    public function __construct(
        private readonly string $entryName,
        private readonly string $position,
        private readonly string $buildName,
        private readonly ?PageRenderer $pageRenderer = null,
        private readonly array $parameters = [],
        private readonly bool $registerFile = true,
        private readonly bool $isLibrary = false
    ) {
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
