<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 with Webpack Encore',
    'description' => 'Webpack Encore from symfony for TYPO3',
    'category' => 'fe',
    'author' => 'Sebastian Schreiber',
    'author_email' => 'breakpoint@schreibersebastian.de',
    'state' => 'stable',
    'clearCacheOnLoad' => false,
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
