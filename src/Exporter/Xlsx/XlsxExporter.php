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

namespace Vyfony\Bundle\PortationBundle\Exporter\Xlsx;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vyfony\Bundle\PortationBundle\Configuration\XlsxPortationConfiguration;
use Vyfony\Bundle\PortationBundle\Exporter\ExporterInterface;
use Vyfony\Bundle\PortationBundle\Exporter\Xlsx\Accessor\XlsxAccessorInterface;
use Vyfony\Bundle\PortationBundle\RowType\RowTypeInterface;
use Vyfony\Bundle\PortationBundle\Target\Part\CellValueExtractorInterface;
use Vyfony\Bundle\PortationBundle\Target\Part\EntitySourceInterface;
use Vyfony\Bundle\PortationBundle\Target\Part\SchemaProviderInterface;
use Vyfony\Bundle\PortationBundle\VyfonyPortationBundle;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxExporter implements ExporterInterface
{
    /**
     * @var EntitySourceInterface
     */
    private $entitySource;

    /**
     * @var SchemaProviderInterface
     */
    private $schemaProvider;

    /**
     * @var CellValueExtractorInterface
     */
    private $cellValuesExtractor;

    /**
     * @var XlsxAccessorInterface
     */
    private $xlsxAccessor;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var XlsxPortationConfiguration
     */
    private $portationConfiguration;

    public function __construct(
        EntitySourceInterface $entitySource,
        SchemaProviderInterface $schemaProvider,
        CellValueExtractorInterface $cellValuesExtractor,
        XlsxAccessorInterface $xlsxAccessor,
        TranslatorInterface $translator,
        XlsxPortationConfiguration $portationConfiguration
    ) {
        $this->entitySource = $entitySource;
        $this->schemaProvider = $schemaProvider;
        $this->cellValuesExtractor = $cellValuesExtractor;
        $this->xlsxAccessor = $xlsxAccessor;
        $this->translator = $translator;
        $this->portationConfiguration = $portationConfiguration;
    }

    public function export(
        string $pathToFile,
        ?int $bunchSize
    ): void {
        if (null !== $bunchSize && $bunchSize <= 0) {
            throw new InvalidArgumentException('Bunch size can only be null or greater than zero');
        }

        $entities = $this->entitySource->getEntities();

        if (null === $bunchSize) {
            $this->exportEntities($entities, $pathToFile);
        } else {
            foreach (array_chunk($entities, $bunchSize) as $bunchIndex => $bunch) {
                $this->exportEntities($bunch, $this->getBunchPathToFile($pathToFile, $bunchIndex));
            }
        }
    }

    private function exportEntities(
        array $entities,
        string $pathToFile
    ): void {
        $schema = $this->schemaProvider->getSchema();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $rowIndex = 0;

        $this->drawHeader($schema, $rowIndex, $sheet);

        $rootRowType = $this->schemaProvider->getRootRowType();

        foreach ($entities as $entity) {
            $this->processEntity($rootRowType, $entity, $rowIndex, $schema, $sheet);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($pathToFile);
    }

    private function processEntity(
        RowTypeInterface $rowType,
        object $entity,
        int &$rowIndex,
        array $schema,
        Worksheet $sheet,
        int $nestedRowIndex = 0
    ): void {
        $cellValues = $this->cellValuesExtractor->getCellValues($entity);

        $this->xlsxAccessor->writeRow($cellValues, $rowIndex, $schema, $sheet);

        $useEntityRowForFirstNestedEntity = $this->portationConfiguration->getUseEntityRowForFirstNestedEntity();

        ++$rowIndex;

        $nestingLevel = 0;

        while (true) {
            $nestedRowType = $rowType->getNestedRowType($nestingLevel);

            if (null === $nestedRowType) {
                break;
            }

            $nestedEntities = $this->entitySource->getNestedEntities($nestedRowType, $entity);

            if (0 === $nestedRowIndex && $useEntityRowForFirstNestedEntity) {
                --$rowIndex;
            }

            if (!$useEntityRowForFirstNestedEntity) {
                ++$nestedRowIndex;
            }

            foreach ($nestedEntities as $nestedEntity) {
                $this->processEntity($nestedRowType, $nestedEntity, $rowIndex, $schema, $sheet, $nestedRowIndex);
            }

            if ($useEntityRowForFirstNestedEntity) {
                ++$nestedRowIndex;
            }

            ++$nestingLevel;
        }
    }

    private function getBunchPathToFile(string $pathToFile, int $bunchIndex): string
    {
        $pathToFileParts = explode('.', $pathToFile);

        $formattedBunchIndex = (string) ($bunchIndex + 1);

        if (\count($pathToFileParts) > 1) {
            $extension = array_pop($pathToFileParts);

            $pathToFileParts[] = $formattedBunchIndex;
            $pathToFileParts[] = $extension;
        } else {
            $pathToFileParts[] = $formattedBunchIndex;
        }

        return implode('.', $pathToFileParts);
    }

    private function drawHeader(array $schema, int &$rowIndex, Worksheet $sheet): void
    {
        $convertSchemaValueToTranslationKey = function (string $schemaValue): string {
            return sprintf('portation.format.xlsx.header.%s', $schemaValue);
        };

        $translate = function (string $key): string {
            return $this->translator->trans($key, [], VyfonyPortationBundle::TRANSLATION_DOMAIN);
        };

        $this->xlsxAccessor->writeRow(
            array_combine(
                $schema,
                array_map(
                    $translate,
                    array_map(
                        $convertSchemaValueToTranslationKey,
                        $schema
                    )
                )
            ),
            $rowIndex++,
            $schema,
            $sheet
        );
    }
}
