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
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $config): void {

    $header = <<<EOF
This file is part of the "typo3_encore" Extension for TYPO3 CMS.

For the full copyright and license information, please read the
LICENSE.txt file that was distributed with this source code.
EOF;

    $config->paths([
        __DIR__.'/Classes',
        __DIR__.'/Configuration',
        __DIR__.'/Tests',
    ]);

    $config->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short'
    ]);
    $config->ruleWithConfiguration(HeaderCommentFixer::class, ['header' => $header, 'separate' => 'both']);

    $config->rule(StandaloneLineInMultilineArrayFixer::class);
    $config->rule(ArrayOpenerAndCloserNewlineFixer::class);

    $config->ruleWithConfiguration(GeneralPhpdocAnnotationRemoveFixer::class, [
        'annotations' => ['throws', 'author', 'package', 'group'],
    ]);

    $config->ruleWithConfiguration(NoSuperfluousPhpdocTagsFixer::class, [
        'allow_mixed' => true,
    ]);

    $config->sets([
        SetList::PSR_12,
        SetList::SYMPLIFY,
        SetList::COMMON,
        SetList::CLEAN_CODE
    ]);
    $config->rule(DeclareStrictTypesFixer::class);
    $config->rule(LineLengthFixer::class);
    $config->rule(YodaStyleFixer::class);
};
