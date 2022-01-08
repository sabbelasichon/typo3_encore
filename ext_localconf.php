<?php

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(static function ($packageKey) {

    if (TYPO3_MODE === 'FE') {
        $GLOBALS['TYPO3_CONF_VARS']['FE']['addAllowedPaths'] .= ',' . $packageKey;
    }

    // Enable for Frontend and Backend at the same time
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][$packageKey] = \Ssch\Typo3Encore\Integration\PageRendererHooks::class . '->renderPreProcess';
    // Add collected assets to page cache
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][$packageKey] =  \Ssch\Typo3Encore\Integration\TypoScriptFrontendControllerHooks::class . '->contentPostProcAll';
}, 'typo3_encore');
