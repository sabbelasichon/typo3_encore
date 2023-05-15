<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 with Webpack Encore',
    'description' => 'Webpack Encore from Symfony for TYPO3',
    'category' => 'fe',
    'author' => 'Sebastian Schreiber',
    'author_email' => 'breakpoint@schreibersebastian.de',
    'state' => 'stable',
    'version' => '5.0.6',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.2-12.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Ssch\\Typo3Encore\\' => 'Classes']
    ],
];
