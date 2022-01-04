<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {

    $header = <<<EOF
This file is part of the "typo3_encore" Extension for TYPO3 CMS.

For the full copyright and license information, please read the
LICENSE.txt file that was distributed with this source code.
EOF;

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PARALLEL, true);

    $parameters->set(Option::PATHS, [
        __DIR__.'/Classes',
        __DIR__.'/Configuration',
        __DIR__.'/Tests',
    ]);

    $services = $containerConfigurator->services();
    $services->set(ArraySyntaxFixer::class)
             ->call('configure', [
                 [
                     'syntax' => 'short',
                 ],
             ]);

    $services->set(HeaderCommentFixer::class)->call('configure', [
        ['header' => $header, 'separate' => 'both'],
    ]);

    $services->set(StandaloneLineInMultilineArrayFixer::class);
    $services->set(ArrayOpenerAndCloserNewlineFixer::class);

    $services->set(GeneralPhpdocAnnotationRemoveFixer::class)
             ->call('configure', [
                 [
                     'annotations' => ['throws', 'author', 'package', 'group'],
                 ],
             ]);

    $services->set(NoSuperfluousPhpdocTagsFixer::class)
             ->call('configure', [
                 [
                     'allow_mixed' => true,
                 ],
             ]);

    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(SetList::SYMPLIFY);
    $containerConfigurator->import(SetList::COMMON);
    $containerConfigurator->import(SetList::CLEAN_CODE);
    $services->set(DeclareStrictTypesFixer::class);
    $services->set(LineLengthFixer::class);
    $services->set(YodaStyleFixer::class);
};
