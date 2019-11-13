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
final class ImportCommand extends Command
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
            ->setName('vyfony:portation:import')
            ->setDescription('Import data from human-readable format to database')
            ->addArgument('import-format', InputArgument::REQUIRED, 'Import format')
            ->addArgument('import-file', InputArgument::REQUIRED, 'Path to import file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this
            ->portationRegistry
            ->getImporter($input->getArgument('import-format'))
            ->import($input->getArgument('import-file'));

        (new SymfonyStyle($input, $output))->success('Import has been successfully finished');

        return 0;
    }
}
