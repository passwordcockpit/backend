<?php

use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = [
    'config_cache_path' => 'data/config-cache.php'
];

$aggregator = new ConfigAggregator(
    [
        \Zend\HttpHandlerRunner\ConfigProvider::class,
        \Zend\Expressive\Router\ZendRouter\ConfigProvider::class,
        \Zend\Expressive\Helper\ConfigProvider::class,
        \Zend\Db\ConfigProvider::class,
        \Zend\Expressive\ConfigProvider::class,
        \Zend\Expressive\Router\ConfigProvider::class,
        \Zend\Mvc\I18n\ConfigProvider::class,
        \Zend\InputFilter\ConfigProvider::class,
        \Zend\Filter\ConfigProvider::class,
        \Acelaya\ExpressiveErrorHandler\ConfigProvider::class,
        \Zend\ProblemDetails\ConfigProvider::class,
        \Zend\I18n\ConfigProvider::class,
        \Zend\Paginator\ConfigProvider::class,
        \Zend\Hydrator\ConfigProvider::class,
        \Zend\Expressive\Hal\ConfigProvider::class,
        \Zend\Router\ConfigProvider::class,
        \Zend\Validator\ConfigProvider::class,
        // Include cache configuration
        new ArrayProvider($cacheConfig),
        // Default App module config
        App\ConfigProvider::class,

        // --- Aggiungo le configurazioni dei miei moduli ----
        Authentication\ConfigProvider::class,
        Authorization\ConfigProvider::class,
        User\ConfigProvider::class,
        Folder\ConfigProvider::class,
        Password\ConfigProvider::class,
        Log\ConfigProvider::class,
        File\ConfigProvider::class,

        // Load application config in a pre-defined order in such a way that local settings
        // overwrite global settings. (Loaded as first to last):
        //   - `global.php`
        //   - `*.global.php`
        //   - `local.php`
        //   - `*.local.php`
        new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
        // Load development config if it exists
        new PhpFileProvider('config/development.config.php')
    ],
    $cacheConfig['config_cache_path']
);

return $aggregator->getMergedConfig();
