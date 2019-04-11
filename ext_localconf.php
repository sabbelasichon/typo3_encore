<?php

if (! defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function ($packageKey) {

    // Caching of user requests
    if (! is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\Ssch\Typo3Encore\Integration\CacheFactory::CACHE_KEY])
    ) {
        $context = \TYPO3\CMS\Core\Utility\GeneralUtility::getApplicationContext();

        $cacheBackend = \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class;

        if ($context->isProduction()) {
            $cacheBackend = \TYPO3\CMS\Core\Cache\Backend\NullBackend::class;
        }
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\Ssch\Typo3Encore\Integration\CacheFactory::CACHE_KEY] = [
            'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            'backend' => $cacheBackend,
            'options' => [],
        ];
    }

    if (TYPO3_MODE === '') {
        $dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
        $dispatcher->connect(
            \TYPO3\CMS\Core\Resource\ResourceFactory::class,
            \TYPO3\CMS\Core\Resource\ResourceFactoryInterface::SIGNAL_PreProcessStorage,
            \Ssch\Typo3Encore\Aspect\ResourceFactorySlot::class,
            'jsonManifestVersionStrategy'
        );
    }
}, 'typo3_encore');
