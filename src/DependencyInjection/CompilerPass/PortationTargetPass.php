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
use Symfony\Component\DependencyInjection\Definition;
use Vyfony\Bundle\PortationBundle\Configuration\XlsxPortationConfiguration;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class PortationTargetPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $portationTargetDefinition = $container->findDefinition(
            $container->getParameter('vyfony_portation.portation_target')
        );

        $this->addXlsxPortation($portationTargetDefinition, $container);
    }

    private function addXlsxPortation(Definition $portationTargetDefinition, ContainerBuilder $container): void
    {
        $xlsxConfiguration = $container->getParameter('vyfony_portation.formats')['xlsx'];

        $container->setDefinition(
            'vyfony_portation.exporter.xlsx.configuration',
            new Definition(
                XlsxPortationConfiguration::class,
                [
                    '$useEntityRowForFirstNestedEntity' => $xlsxConfiguration['use_entity_row_for_first_nested_entity'],
                ]
            )
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
