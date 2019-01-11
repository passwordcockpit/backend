<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App\Factory;

use Psr\Container\ContainerInterface;
use App\Service\DoctrineMetadataMap;
use Zend\Expressive\Hal\Metadata\MetadataMap;
use Zend\Expressive\Hal\Metadata\AbstractMetadata;
use Zend\Expressive\Hal\Metadata\Exception;
use Zend\Expressive\Hal\Metadata\MetadataFactoryInterface;
use function array_pop;
use function class_exists;
use function class_implements;
use function class_parents;
use function explode;
use function in_array;
use function is_array;
use function method_exists;
use function sprintf;

class DoctrineMetadataMapFactory
{
    public function __invoke(ContainerInterface $container) : MetadataMap
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $metadataMapConfig = $config[MetadataMap::class] ?? [];

        if (!is_array($metadataMapConfig)) {
            throw Exception\InvalidConfigException::dueToNonArray(
                $metadataMapConfig
            );
        }

        $metadataFactories =
            $config['zend-expressive-hal']['metadata-factories'] ?? [];

        return $this->populateMetadataMapFromConfig(
            new DoctrineMetadataMap(),
            $metadataMapConfig,
            $metadataFactories
        );
    }

    private function populateMetadataMapFromConfig(
        MetadataMap $metadataMap,
        array $metadataMapConfig,
        array $metadataFactories
    ) : MetadataMap {
        foreach ($metadataMapConfig as $metadata) {
            if (!is_array($metadata)) {
                throw Exception\InvalidConfigException::dueToNonArrayMetadata(
                    $metadata
                );
            }

            $this->injectMetadata($metadataMap, $metadata, $metadataFactories);
        }

        return $metadataMap;
    }

    /**
     * @throws Exception\InvalidConfigException if the metadata is missing a
     *     "__class__" entry.
     * @throws Exception\InvalidConfigException if the "__class__" entry is not
     *     a class.
     * @throws Exception\InvalidConfigException if the "__class__" entry is not
     *     an AbstractMetadata class.
     * @throws Exception\InvalidConfigException if no matching `create*()`
     *     method is found for the "__class__" entry.
     */
    private function injectMetadata(
        MetadataMap $metadataMap,
        array $metadata,
        array $metadataFactories
    ) {
        if (!isset($metadata['__class__'])) {
            throw Exception\InvalidConfigException::dueToMissingMetadataClass();
        }

        if (!class_exists($metadata['__class__'])) {
            throw Exception\InvalidConfigException::dueToInvalidMetadataClass(
                $metadata['__class__']
            );
        }

        $metadataClass = $metadata['__class__'];
        if (!in_array(
            AbstractMetadata::class,
            class_parents($metadataClass),
            true
        )) {
            throw Exception\InvalidConfigException::dueToNonMetadataClass(
                $metadataClass
            );
        }

        if (isset($metadataFactories[$metadataClass])) {
            // A factory was registered. Use it!
            $metadataMap->add(
                $this->createMetadataViaFactoryClass(
                    $metadataClass,
                    $metadata,
                    $metadataFactories[$metadataClass]
                )
            );
            return;
        }

        // No factory was registered. Use the deprecated factory method.
        $metadataMap->add(
            $this->createMetadataViaFactoryMethod($metadataClass, $metadata)
        );
    }

    /**
     * Uses the registered factory class to create the metadata instance.
     *
     * @param string $metadataClass
     * @param string $factoryClass
     * @param array  $metadata
     * @return AbstractMetadata
     */
    private function createMetadataViaFactoryClass(
        string $metadataClass,
        array $metadata,
        string $factoryClass
    ) : AbstractMetadata {
        if (!in_array(
            MetadataFactoryInterface::class,
            class_implements($factoryClass),
            true
        )) {
            throw Exception\InvalidConfigException::dueToInvalidMetadataFactoryClass(
                $factoryClass
            );
        }

        $factory = new $factoryClass();
        /* @var $factory MetadataFactoryInterface */
        return $factory->createMetadata($metadataClass, $metadata);
    }

    /**
     * Call the factory method in this class namend "createMyMetadata(array $metadata)".
     *
     * This function is to ensure backwards compatibility with versions prior to 0.6.0.
     *
     * @param string $metadataClass
     * @param array  $metadata
     * @return AbstractMetadata
     */
    private function createMetadataViaFactoryMethod(
        string $metadataClass,
        array $metadata
    ) : AbstractMetadata {
        $normalizedClass = $this->stripNamespaceFromClass($metadataClass);
        $method = sprintf('create%s', $normalizedClass);

        if (!method_exists($this, $method)) {
            throw Exception\InvalidConfigException::dueToUnrecognizedMetadataClass(
                $metadataClass
            );
        }

        return $this->$method($metadata);
    }

    private function stripNamespaceFromClass(string $class) : string
    {
        $segments = explode('\\', $class);
        return array_pop($segments);
    }
}
