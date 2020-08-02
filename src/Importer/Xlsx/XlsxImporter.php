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
use Vyfony\Bundle\PortationBundle\Configuration\XlsxPortationConfiguration;
use Vyfony\Bundle\PortationBundle\Exporter\Xlsx\Accessor\XlsxAccessorInterface;
use Vyfony\Bundle\PortationBundle\Importer\ImporterInterface;
use Vyfony\Bundle\PortationBundle\RowType\RowTypeInterface;
use Vyfony\Bundle\PortationBundle\Target\Part\CellValueHandlerInterface;
use Vyfony\Bundle\PortationBundle\Target\Part\EntityFactoryInterface;
use Vyfony\Bundle\PortationBundle\Target\Part\EntitySaverInterface;
use Vyfony\Bundle\PortationBundle\Target\Part\SchemaProviderInterface;

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
     * @var EntitySaverInterface
     */
    private $entitySaver;

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
     * @var XlsxPortationConfiguration
     */
    private $portationConfiguration;

    public function __construct(
        EntityManagerInterface $entityManager,
        SchemaProviderInterface $schemaProvider,
        EntitySaverInterface $entitySaver,
        EntityFactoryInterface $entityFactory,
        CellValueHandlerInterface $cellValueHandlerProvider,
        XlsxAccessorInterface $xlsxAccessor,
        XlsxPortationConfiguration $portationConfiguration
    ) {
        $this->entityManager = $entityManager;
        $this->schemaProvider = $schemaProvider;
        $this->entitySaver = $entitySaver;
        $this->entityFactory = $entityFactory;
        $this->cellValueHandlerProvider = $cellValueHandlerProvider;
        $this->xlsxAccessor = $xlsxAccessor;
        $this->portationConfiguration = $portationConfiguration;
    }

    /**
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
                $this->entitySaver->save($entity);
            }
        }
    }

    /**
     * @return object|false|null
     */
    private function processRow(
        RowTypeInterface $rowType,
        int &$rowIndex,
        array $schema,
        Worksheet $sheet,
        int $nestedRowIndex = 0
    ) {
        $rowValues = $this->xlsxAccessor->readRow($rowIndex, $schema, $sheet);

        $rowKey = $rowValues['id'];

        if ('' === $rowKey) {
            return false;
        }

        if ($this->isRowTypeAndKeyMatch($rowType, $rowKey) ||
            $this->isValidNestedRow($rowType, $rowKey, $nestedRowIndex)
        ) {
            $entity = $this->entityFactory->createEntity($rowType);

            $cellValueHandlers = $this->cellValueHandlerProvider->getCellValueHandlers($rowType);

            foreach ($cellValueHandlers as $handlerKey => $cellValueHandler) {
                $cellValueHandler(
                    $entity,
                    $rowValues[$handlerKey]
                );
            }

            $useEntityRowForFirstNestedEntity = $this->portationConfiguration->getUseEntityRowForFirstNestedEntity();

            ++$rowIndex;

            $nestingLevel = 0;

            while (true) {
                $nestedRowType = $rowType->getNestedRowType($nestingLevel);

                if (null === $nestedRowType) {
                    break;
                }

                if (0 === $nestedRowIndex && $useEntityRowForFirstNestedEntity) {
                    --$rowIndex;
                }

                if (!$useEntityRowForFirstNestedEntity) {
                    ++$nestedRowIndex;
                }

                $nestedEntity = $this->processRow(
                    $nestedRowType,
                    $rowIndex,
                    $schema,
                    $sheet,
                    $nestedRowIndex
                );

                if (false === $nestedEntity) {
                    break;
                }

                if (null === $nestedEntity) {
                    ++$nestingLevel;
                } else {
                    $this->entityFactory->setNestedEntity($rowKey, $entity, $nestedEntity);
                }

                if ($useEntityRowForFirstNestedEntity) {
                    ++$nestedRowIndex;
                }
            }

            return $entity;
        }

        return null;
    }

    private function isRowTypeAndKeyMatch(RowTypeInterface $rowType, string $rowKey): bool
    {
        return $rowType->getNewRowKey() === $rowKey;
    }

    private function isValidNestedRow(RowTypeInterface $rowType, string $rowKey, int $nestedRowIndex): bool
    {
        if (0 === $nestedRowIndex && $this->portationConfiguration->getUseEntityRowForFirstNestedEntity()) {
            $parentRowType = $rowType->getParentRowType();

            if (null !== $parentRowType) {
                return $this->isRowTypeAndKeyMatch($parentRowType, $rowKey);
            }
        }

        return false;
    }
}
