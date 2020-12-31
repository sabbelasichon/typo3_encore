<?php

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->getFinder()
       ->in(__DIR__ . '/Classes')
       ->in(__DIR__ . '/Configuration')
       ->in(__DIR__ . '/Tests')
;

$header = <<<EOF
This file is part of the "typo3_encore" Extension for TYPO3 CMS.

For the full copyright and license information, please read the
LICENSE.txt file that was distributed with this source code.
EOF;

$config->addRules([
    'header_comment' => ['header' => $header, 'separate' => 'both'],
]);

return $config;
