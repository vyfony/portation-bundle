<?php

declare(strict_types=1);

/*
 * This file is part of VyfonyPortationBundle project.
 *
 * (c) Anton Dyshkant <vyshkant@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vyfony\Bundle\PortationBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VyfonyPortationExtension extends ConfigurableExtension
{
    /**
     * @param array            $mergedConfig
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yaml');

        $portationTargetDefinition = $container->findDefinition($mergedConfig['portation_target']);

        $container
            ->getDefinition('vyfony_portation.exporter.xlsx.xlsx_exporter')
            ->setArgument('$entitySource', $portationTargetDefinition)
            ->setArgument('$schemaProvider', $portationTargetDefinition)
            ->setArgument('$cellValuesExtractor', $portationTargetDefinition)
        ;
    }
}
