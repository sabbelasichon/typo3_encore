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

use Ssch\Typo3Encore\Asset\VersionStrategyInterface;
use Ssch\Typo3Encore\Integration\PackageFactoryInterface;
use Symfony\Component\Asset\PackageInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class AssetViewHelper extends AbstractViewHelper
{
    /**
     * @var PackageInterface
     */
    private $package;

    public function __construct(PackageFactoryInterface $packageFactory)
    {
        $this->package = $packageFactory->getPackage();
    }

    public function initializeArguments()
    {
        $this->registerArgument('pathToFile', 'string', 'The path to the file', true);
    }

    public function render()
    {
        return $this->package->getUrl($this->arguments['pathToFile']);
    }
}
