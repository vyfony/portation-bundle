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

namespace Vyfony\Bundle\PortationBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class PortationTargetPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $portationTargetDefinition = $container->findDefinition(
            $container->getParameter('vyfony_portation.portation_target')
        );

        $container
            ->getDefinition('vyfony_portation.exporter.xlsx.xlsx_exporter')
            ->setArgument('$entitySource', $portationTargetDefinition)
            ->setArgument('$schemaProvider', $portationTargetDefinition)
            ->setArgument('$cellValuesExtractor', $portationTargetDefinition)
        ;

        $container
            ->getDefinition('vyfony_portation.importer.xlsx.xlsx_importer')
            ->setArgument('$schemaProvider', $portationTargetDefinition)
            ->setArgument('$entityFactory', $portationTargetDefinition)
            ->setArgument('$cellValueHandlerProvider', $portationTargetDefinition)
        ;
    }
}
