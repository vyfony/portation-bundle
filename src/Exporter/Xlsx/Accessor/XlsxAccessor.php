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

namespace Vyfony\Bundle\PortationBundle\Exporter\Xlsx\Accessor;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxAccessor implements XlsxAccessorInterface
{
    /**
     * @param int       $columnIndex
     * @param int       $rowIndex
     * @param Worksheet $sheet
     *
     * @return string
     */
    public function readCell(int $columnIndex, int $rowIndex, Worksheet $sheet): string
    {
        return $sheet->getCellByColumnAndRow($columnIndex + 1, $rowIndex + 1)->getFormattedValue();
    }

    /**
     * @param int       $rowIndex
     * @param string[]  $schema
     * @param Worksheet $sheet
     *
     * @return string[]
     */
    public function readRow(int $rowIndex, array $schema, Worksheet $sheet): array
    {
        $array = [];

        foreach ($schema as $indexInSchema => $key) {
            $array[$key] = $this->readCell($indexInSchema, $rowIndex, $sheet);
        }

        return $array;
    }

    /**
     * @param string    $cellValue
     * @param int       $columnIndex
     * @param int       $rowIndex
     * @param Worksheet $sheet
     */
    public function writeCell(string $cellValue, int $columnIndex, int $rowIndex, Worksheet $sheet): void
    {
        $sheet->getColumnDimensionByColumn($columnIndex)->setAutoSize(true);

        $sheet->setCellValueExplicitByColumnAndRow($columnIndex + 1, $rowIndex + 1, $cellValue, DataType::TYPE_STRING);
    }

    /**
     * @param string[]  $cellValues
     * @param int       $rowIndex
     * @param string[]  $schema
     * @param Worksheet $sheet
     */
    public function writeRow(array $cellValues, int $rowIndex, array $schema, Worksheet $sheet): void
    {
        foreach ($cellValues as $cellKey => $cellValue) {
            $columnIndex = $this->getIndexInSchema($cellKey, $schema);

            // todo use some kind of injectable universal converters
            if (null === $cellValue) {
                $cellValue = '';
            }

            $this->writeCell($cellValue, $columnIndex, $rowIndex, $sheet);
        }
    }

    /**
     * @param string   $key
     * @param string[] $schema
     *
     * @return int
     */
    private function getIndexInSchema(string $key, array $schema): int
    {
        $columnIndex = array_search($key, $schema, true);

        if (false === $columnIndex) {
            throw new InvalidArgumentException(sprintf('Key "%s" is not present in the schema', $key));
        }

        return $columnIndex;
    }
}
