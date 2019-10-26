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

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
interface XlsxAccessorInterface
{
    /**
     * @param int       $columnIndex
     * @param int       $rowIndex
     * @param Worksheet $sheet
     *
     * @return string
     */
    public function readCell(int $columnIndex, int $rowIndex, Worksheet $sheet): string;

    /**
     * @param int       $rowIndex
     * @param array     $schema
     * @param Worksheet $sheet
     *
     * @return string[]
     */
    public function readRow(int $rowIndex, array $schema, Worksheet $sheet): array;

    /**
     * @param string    $cellValue
     * @param int       $columnIndex
     * @param int       $rowIndex
     * @param Worksheet $sheet
     */
    public function writeCell(string $cellValue, int $columnIndex, int $rowIndex, Worksheet $sheet): void;

    /**
     * @param string[]  $cellValues
     * @param int       $rowIndex
     * @param string[]  $schema
     * @param Worksheet $sheet
     */
    public function writeRow(array $cellValues, int $rowIndex, array $schema, Worksheet $sheet): void;
}
