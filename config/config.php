<?php

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = [
    'config_cache_path' => 'data/config-cache.php'
];

$aggregator = new ConfigAggregator(
    [
        \Blast\BaseUrl\ConfigProvider::class,
        \Laminas\HttpHandlerRunner\ConfigProvider::class,
        \Mezzio\Router\LaminasRouter\ConfigProvider::class,
        \Mezzio\Helper\ConfigProvider::class,
        \Laminas\Db\ConfigProvider::class,
        \Mezzio\ConfigProvider::class,
        \Mezzio\Router\ConfigProvider::class,
        \Laminas\InputFilter\ConfigProvider::class,
        \Laminas\Filter\ConfigProvider::class,
        \Mezzio\ProblemDetails\ConfigProvider::class,
        \Laminas\I18n\ConfigProvider::class,
        \Laminas\Paginator\ConfigProvider::class,
        \Laminas\Hydrator\ConfigProvider::class,
        \Mezzio\Hal\ConfigProvider::class,
        \Laminas\Router\ConfigProvider::class,
        \Laminas\Validator\ConfigProvider::class,
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
