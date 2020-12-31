<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\DependencyInjection;

use Ssch\Typo3Encore\Asset\EntrypointLookup;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Integration\SettingsService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class EntryLookupCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $filesystem = new Reference(Filesysem::class);
        $settingsService = new Reference(SettingsService::class);

        $buildConfigurations = $settingsService->getArrayByPath('builds');
        $entrypointsPathDefaultBuild = $settingsService->getStringByPath('entrypointJsonPath');

        if (! empty($buildConfigurations)) {
            foreach ($buildConfigurations as $buildConfigurationKey => $buildConfiguration) {
                $entrypointsPath = sprintf('%s/entrypoints.json', $buildConfiguration);
                $arguments = [
                    $entrypointsPath,
                    $buildConfigurationKey,
                ];
                $definition = new Definition(EntrypointLookup::class, $arguments);
                $container->setDefinition($buildConfigurationKey, $definition);
            }
        }

        if ($filesystem->exists($filesystem->getFileAbsFileName($entrypointsPathDefaultBuild))) {
            $arguments = [
                $entrypointsPathDefaultBuild,
                EntrypointLookupInterface::DEFAULT_BUILD,
            ];
            $definition = new Definition(EntrypointLookup::class, $arguments);
            $container->setDefinition(EntrypointLookupInterface::DEFAULT_BUILD, $definition);
        }
    }
}
