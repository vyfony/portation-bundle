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

namespace Vyfony\Bundle\PortationBundle\Exporter;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
interface ExporterInterface
{
    /**
     * @param string   $pathToFile
     * @param int|null $bunchSize
     */
    public function export(
        string $pathToFile,
        ?int $bunchSize
    ): void;
}
