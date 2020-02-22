<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 with Webpack Encore',
    'description' => 'Webpack Encore from Symfony for TYPO3',
    'category' => 'fe',
    'author' => 'Sebastian Schreiber',
    'author_email' => 'breakpoint@schreibersebastian.de',
    'state' => 'stable',
    'clearCacheOnLoad' => false,
    'version' => '2.0.6',
    'constraints' => [
        'depends' => [
            'php' => '7.2.5-7.4.999',
            'typo3' => '9.5.0-10.2.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
