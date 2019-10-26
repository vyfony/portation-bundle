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

use InvalidArgumentException;
use Vyfony\Bundle\PortationBundle\Exporter\ExporterInterface;
use Vyfony\Bundle\PortationBundle\Importer\ImporterInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class PortationRegistry implements PortationRegistryInterface
{
    /**
     * @var ExporterInterface[]
     */
    private $exporters = [];

    /**
     * @var ImporterInterface[]
     */
    private $importers = [];

    /**
     * @param string            $format
     * @param ExporterInterface $exporter
     */
    public function addExporter(string $format, ExporterInterface $exporter): void
    {
        if (\array_key_exists($format, $this->exporters)) {
            throw new InvalidArgumentException(sprintf('Duplicate exporter registered for format "%s"', $format));
        }

        $this->exporters[$format] = $exporter;
    }

    /**
     * @param string            $format
     * @param ImporterInterface $importer
     */
    public function addImporter(string $format, ImporterInterface $importer): void
    {
        if (\array_key_exists($format, $this->importers)) {
            throw new InvalidArgumentException(sprintf('Duplicate importer registered for format "%s"', $format));
        }

        $this->importers[$format] = $importer;
    }

    /**
     * @param string $format
     *
     * @return ExporterInterface
     */
    public function getExporter(string $format): ExporterInterface
    {
        if (!\array_key_exists($format, $this->exporters)) {
            throw new InvalidArgumentException(sprintf('Unknown export format "%s"', $format));
        }

        return $this->exporters[$format];
    }

    /**
     * @param string $format
     *
     * @return ImporterInterface
     */
    public function getImporter(string $format): ImporterInterface
    {
        if (!\array_key_exists($format, $this->importers)) {
            throw new InvalidArgumentException(sprintf('Unknown import format "%s"', $format));
        }

        return $this->importers[$format];
    }
}
