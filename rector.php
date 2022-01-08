<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\DeadCode\Rector\ClassMethod\RemoveLastReturnRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\SetList;
use Rector\Transform\Rector\StaticCall\StaticCallToFuncCallRector;
use Rector\Transform\ValueObject\StaticCallToFuncCall;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::PATHS, [
        __DIR__.'/Classes',
        __DIR__.'/Tests',
    ]);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_70);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_71);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_72);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_73);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_74);
    // Define what rule sets will be applied

    // get services (needed for register a single rule)
    $services = $containerConfigurator->services();
};
