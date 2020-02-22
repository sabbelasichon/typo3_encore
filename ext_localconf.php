<?php

if (! defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(static function ($packageKey) {

    // Caching of user requests
    if (! is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\Ssch\Typo3Encore\Integration\CacheFactory::CACHE_KEY])
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\Ssch\Typo3Encore\Integration\CacheFactory::CACHE_KEY] = [
            'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
            'options' => [],
        ];
    }

    if (TYPO3_MODE === 'FE') {
        $GLOBALS['TYPO3_CONF_VARS']['FE']['addAllowedPaths'] .= ',' . $packageKey;
    }

    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)->registerImplementation(
        \Ssch\Typo3Encore\Asset\VersionStrategyInterface::class,
        Ssch\Typo3Encore\Asset\JsonManifestVersionStrategy::class
    );

    // Enable for Frontend and Backend at the same time
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][$packageKey] = \Ssch\Typo3Encore\Integration\PageRendererHooks::class . '->renderPreProcess';
}, 'typo3_encore');

if (!\TYPO3\CMS\Core\Core\Environment::isComposerMode()) {
    require \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('typo3_encore') . '/Resources/Private/Php/Libraries/vendor/autoload.php';
}
