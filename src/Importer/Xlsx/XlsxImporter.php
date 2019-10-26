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

namespace Vyfony\Bundle\PortationBundle\Importer\Xlsx;

use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Vyfony\Bundle\PortationBundle\Exporter\Xlsx\Accessor\XlsxAccessorInterface;
use Vyfony\Bundle\PortationBundle\Importer\ImporterInterface;
use Vyfony\Bundle\PortationBundle\RowType\RowTypeInterface;
use Vyfony\Bundle\PortationBundle\Target\CellValueHandlerInterface;
use Vyfony\Bundle\PortationBundle\Target\EntityFactoryInterface;
use Vyfony\Bundle\PortationBundle\Target\SchemaProviderInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxImporter implements ImporterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SchemaProviderInterface
     */
    private $schemaProvider;

    /**
     * @var EntityFactoryInterface
     */
    private $entityFactory;

    /**
     * @var CellValueHandlerInterface
     */
    private $cellValueHandlerProvider;

    /**
     * @var XlsxAccessorInterface
     */
    private $xlsxAccessor;

    /**
     * @param EntityManagerInterface    $entityManager
     * @param SchemaProviderInterface   $schemaProvider
     * @param EntityFactoryInterface    $entityFactory
     * @param CellValueHandlerInterface $cellValueHandlerProvider
     * @param XlsxAccessorInterface     $xlsxAccessor
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SchemaProviderInterface $schemaProvider,
        EntityFactoryInterface $entityFactory,
        CellValueHandlerInterface $cellValueHandlerProvider,
        XlsxAccessorInterface $xlsxAccessor
    ) {
        $this->entityManager = $entityManager;
        $this->schemaProvider = $schemaProvider;
        $this->entityFactory = $entityFactory;
        $this->cellValueHandlerProvider = $cellValueHandlerProvider;
        $this->xlsxAccessor = $xlsxAccessor;
    }

    /**
     * @param string $pathToFile
     *
     * @throws PhpSpreadsheetException
     */
    public function import(string $pathToFile): void
    {
        $schema = $this->schemaProvider->getSchema();

        $reader = new Xlsx();

        $spreadsheet = $reader->load($pathToFile);

        $sheet = $spreadsheet->getActiveSheet();

        $rowIndex = 1;

        $rootRowType = $this->schemaProvider->getRootRowType();

        while (true) {
            $entity = $this->processRow($rootRowType, $rowIndex, $schema, $sheet);

            if (false === $entity) {
                break;
            }

            if (null !== $entity) {
                $this->entityManager->persist($entity);

                $this->entityManager->flush();
            }
        }
    }

    /**
     * @param RowTypeInterface $rowType
     * @param int              $rowIndex
     * @param array            $schema
     * @param Worksheet        $sheet
     *
     * @return object|false|null
     */
    private function processRow(RowTypeInterface $rowType, int &$rowIndex, array $schema, Worksheet $sheet)
    {
        $rowValues = $this->xlsxAccessor->readRow($rowIndex, $schema, $sheet);

        $rowKey = $rowValues['id'];

        if ('' === $rowKey) {
            return false;
        }

        if ($rowType->getNewRowKey() === $rowKey) {
            $entity = $this->entityFactory->createEntity($rowKey);

            $cellValueHandlers = $this->cellValueHandlerProvider->getCellValueHandlers($rowKey);

            foreach ($cellValueHandlers as $handlerKey => $cellValueHandler) {
                $cellValueHandler(
                    $entity,
                    $rowValues[$handlerKey]
                );
            }

            ++$rowIndex;

            $nestedRowType = $rowType->getNestedRowType();

            if (null !== $nestedRowType) {
                while (true) {
                    $nestedEntity = $this->processRow($nestedRowType, $rowIndex, $schema, $sheet);

                    if (false === $nestedEntity) {
                        return false;
                    }

                    if (null === $nestedEntity) {
                        break;
                    }

                    $this->entityFactory->setNestedEntity($rowKey, $entity, $nestedEntity);

                    ++$rowIndex;
                }
            }

            return $entity;
        }

        return null;
    }
}
