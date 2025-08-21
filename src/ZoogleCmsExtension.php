<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Symfony;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Zantolov\Zoogle\Symfony\Client\CachedGoogleDriveClient;
use Zantolov\Zoogle\Symfony\Service\LocalImagePersistenceProcessor;
use Zantolov\ZoogleCms\Client\GoogleDriveClient;

final class ZoogleCmsExtension extends Extension
{
    /**
     * @param array<mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new ZoogleCmsConfiguration();
        /**
         * @var array{
         *     google_api: array{
         *       auth_file: string,
         *       client_id: string,
         *       google_drive_root_directory: string,
         *     },
         *     cache: bool
         * } $config
         */
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__));
        $loader->load('../config/services.php');

        $container->getDefinition(GoogleDriveClient::class)->setArguments([
            '$useCache' => $config['cache'],
        ]);

        $container->getDefinition(CachedGoogleDriveClient::class)->setArgument(
            '$cache',
            new Reference($config['cache_pool']),
        );

        $container->getDefinition(LocalImagePersistenceProcessor::class)->setArgument(
            '$cache',
            new Reference($config['cache_pool']),
        );
    }
}
