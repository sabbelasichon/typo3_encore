<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Ssch\Typo3Encore\Integration\FixedIdGenerator;
use Ssch\Typo3Encore\Integration\IdGenerator;
use Ssch\Typo3Encore\Integration\IdGeneratorInterface;
use Ssch\Typo3Encore\Integration\TypoScriptFrontendControllerEventListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Ssch\\Typo3Encore\\', __DIR__ . '/../Classes/')->exclude([
        __DIR__ . '/../Classes/ValueObject',
        __DIR__ . '/../Classes/Asset/EntrypointLookup.php',
    ]);

    $services->alias(IdGeneratorInterface::class, IdGenerator::class);
    $services->set(FixedIdGenerator::class)->args(['fixed']);
    $services->set(TypoScriptFrontendControllerEventListener::class)->tag('event.listener', [
        'event' => AfterCacheableContentIsGeneratedEvent::class,
    ]);

    if (Environment::getContext()->isTesting()) {
        $services->alias(IdGeneratorInterface::class, FixedIdGenerator::class);
    }
};
