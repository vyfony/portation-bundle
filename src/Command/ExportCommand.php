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

namespace Vyfony\Bundle\PortationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vyfony\Bundle\PortationBundle\Registry\PortationRegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ExportCommand extends Command
{
    /**
     * @var PortationRegistryInterface
     */
    private $portationRegistry;

    public function __construct(PortationRegistryInterface $portationRegistry)
    {
        parent::__construct();
        $this->portationRegistry = $portationRegistry;
    }

    protected function configure(): void
    {
        $this
            ->setName('vyfony:portation:export')
            ->setDescription('Export data from database to human-readable format')
            ->addArgument('export-format', InputArgument::REQUIRED, 'Export format')
            ->addArgument('export-file', InputArgument::REQUIRED, 'Path to export file')
            ->addArgument('bunch-size', InputArgument::OPTIONAL, 'Bunch size')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bunchSizeArgument = $input->getArgument('bunch-size');

        $this
            ->portationRegistry
            ->getExporter($input->getArgument('export-format'))
            ->export(
                $input->getArgument('export-file'),
                null === $bunchSizeArgument ? null : (int) $bunchSizeArgument
            );

        (new SymfonyStyle($input, $output))->success('Export has been successfully finished');

        return 0;
    }
}
