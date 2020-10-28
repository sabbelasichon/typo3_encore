<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\ViewHelpers;

/**
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\PackageFactoryInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class AssetViewHelper extends AbstractViewHelper
{
    /**
     * @var PackageFactoryInterface
     */
    private $packageFactory;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(PackageFactoryInterface $packageFactory, FilesystemInterface $filesystem)
    {
        $this->packageFactory = $packageFactory;
        $this->filesystem = $filesystem;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('pathToFile', 'string', 'The path to the file', true);
        $this->registerArgument('package', 'string', 'The package configuration to use', false, '_default');
    }

    public function render(): string
    {
        return $this->packageFactory->getPackage($this->arguments['package'])->getUrl($this->filesystem->getRelativeFilePath($this->arguments['pathToFile']));
    }
}
