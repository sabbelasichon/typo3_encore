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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

final class ResourceFactoryExtended extends ResourceFactory
{
    public const SIGNAL_PreProcessFileIdentifier = 'preProcessFileIdentifier';

    /**
     * @param int $storageUid
     * @param string $fileIdentifier
     *
     * @return File|ProcessedFile|null
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function getFileObjectByStorageAndIdentifier($storageUid, &$fileIdentifier)
    {
        list($_, $fileIdentifier) = $this->signalSlotDispatcher->dispatch(__CLASS__, self::SIGNAL_PreProcessFileIdentifier, [$this, $fileIdentifier]);
        return parent::getFileObjectByStorageAndIdentifier($storageUid, $fileIdentifier);
    }
}
