<?php

namespace Lib\BarBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Hi Command for bundle Bar, prints a static message
 */
class HiCommand extends Command
{
    public function __construct()
    {
        parent::__construct('bar:hi');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * 
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Hi from Bar!');

        return Command::SUCCESS;
    }

    /**
     * @return string|null
     */
    public static function getDefaultDescription(): ?string
    {
        return "This is Hi command from Bar Bundle.";
    }
}
