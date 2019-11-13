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

namespace Vyfony\Bundle\PortationBundle\Registry;

use Vyfony\Bundle\PortationBundle\Exporter\ExporterInterface;
use Vyfony\Bundle\PortationBundle\Importer\ImporterInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
interface PortationRegistryInterface
{
    public function getExporter(string $format): ExporterInterface;

    public function getImporter(string $format): ImporterInterface;
}
