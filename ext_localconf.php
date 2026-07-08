<?php

// Enable for Frontend and Backend at the same time
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess']['typo3_encore'] = \Ssch\Typo3Encore\Integration\PageRendererHooks::class . '->renderPreProcess';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\Ssch\Typo3Encore\Form\FormDataProvider\RichtextEncoreConfiguration::class] = [
    'depends' => [\TYPO3\CMS\Backend\Form\FormDataProvider\TcaText::class],
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['flexFormSegment'][\Ssch\Typo3Encore\Form\FormDataProvider\RichtextEncoreConfiguration::class] = [
    'depends' => [\TYPO3\CMS\Backend\Form\FormDataProvider\TcaText::class],
];
